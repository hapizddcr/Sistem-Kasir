<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function dailySales(string $date): array
    {
        $sales = Sale::whereDate('sale_date', $date)
            ->where('status', 'completed')
            ->get();

        return [
            'date' => $date,
            'total_transactions' => $sales->count(),
            'total_items_sold' => $sales->sum(fn($s) => $s->items->sum('quantity')),
            'total_revenue' => (float) $sales->sum('total'),
            'total_tax' => (float) $sales->sum('tax'),
            'total_discount' => (float) $sales->sum('discount'),
            'total_profit' => (float) $sales->sum(fn($s) => $s->totalProfit()),
            'average_transaction' => $sales->count() > 0 ? (float) $sales->avg('total') : 0,
            'sales' => $sales->load(['customer', 'user', 'items']),
        ];
    }

    public function monthlySales(int $year, int $month): array
    {
        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $sales = Sale::whereBetween('sale_date', [$start->toDateString(), $end->toDateString()])
            ->where('status', 'completed')
            ->get();

        // Daily breakdown
        $dailyBreakdown = [];
        for ($day = 1; $day <= $start->daysInMonth; $day++) {
            $date = $start->copy()->day($day)->toDateString();
            $daySales = $sales->filter(fn($s) => $s->sale_date->toDateString() === $date);
            $dailyBreakdown[] = [
                'date' => $date,
                'transactions' => $daySales->count(),
                'revenue' => (float) $daySales->sum('total'),
                'profit' => (float) $daySales->sum(fn($s) => $s->totalProfit()),
            ];
        }

        return [
            'period' => $start->format('F Y'),
            'year' => $year,
            'month' => $month,
            'total_transactions' => $sales->count(),
            'total_revenue' => (float) $sales->sum('total'),
            'total_profit' => (float) $sales->sum(fn($s) => $s->totalProfit()),
            'average_daily_revenue' => $sales->count() > 0
                ? (float) $sales->sum('total') / max(1, $sales->pluck('sale_date')->unique()->count())
                : 0,
            'daily_breakdown' => $dailyBreakdown,
        ];
    }

    public function topProducts(string $from, string $to, int $limit = 10): array
    {
        $items = SaleItem::whereHas('sale', function ($q) use ($from, $to) {
            $q->whereBetween('sale_date', [$from, $to])->where('status', 'completed');
        })
            ->selectRaw('product_id, product_name, SUM(quantity) as total_qty, SUM(subtotal) as total_revenue, SUM((unit_price - cost_price) * quantity) as total_profit, COUNT(*) as transactions')
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('total_revenue')
            ->limit($limit)
            ->get();

        return $items->map(fn($i) => [
            'product_id' => $i->product_id,
            'product_name' => $i->product_name,
            'quantity_sold' => (int) $i->total_qty,
            'revenue' => (float) $i->total_revenue,
            'profit' => (float) $i->total_profit,
            'transactions' => (int) $i->transactions,
        ])->toArray();
    }

    public function profitLoss(string $from, string $to): array
    {
        $revenue = Sale::whereBetween('sale_date', [$from, $to])
            ->where('status', 'completed')
            ->sum('total');

        $cogs = SaleItem::whereHas('sale', function ($q) use ($from, $to) {
            $q->whereBetween('sale_date', [$from, $to])->where('status', 'completed');
        })->selectRaw('SUM(cost_price * quantity) as total')->value('total') ?? 0;

        $discount = Sale::whereBetween('sale_date', [$from, $to])
            ->where('status', 'completed')
            ->sum('discount');

        $tax = Sale::whereBetween('sale_date', [$from, $to])
            ->where('status', 'completed')
            ->sum('tax');

        $expenses = 0; // Could be expanded with an Expense model

        $grossProfit = $revenue - $cogs;
        $netProfit = $grossProfit - $expenses;

        return [
            'period' => ['from' => $from, 'to' => $to],
            'revenue' => (float) $revenue,
            'cogs' => (float) $cogs,
            'gross_profit' => (float) $grossProfit,
            'discounts' => (float) $discount,
            'tax_collected' => (float) $tax,
            'expenses' => (float) $expenses,
            'net_profit' => (float) $netProfit,
            'margin' => $revenue > 0 ? round(($grossProfit / $revenue) * 100, 2) : 0,
        ];
    }

    public function stockReport(): array
    {
        $products = Product::with(['category', 'unit'])->where('track_stock', true)->get();

        $totalValue = $products->sum(fn($p) => $p->stock * $p->purchase_price);
        $totalRetailValue = $products->sum(fn($p) => $p->stock * $p->selling_price);

        return [
            'total_products' => $products->count(),
            'total_stock_units' => (int) $products->sum('stock'),
            'total_stock_value' => (float) $totalValue,
            'total_retail_value' => (float) $totalRetailValue,
            'potential_profit' => (float) ($totalRetailValue - $totalValue),
            'low_stock_count' => $products->where('stock', '<=', \DB::raw('min_stock'))->count(),
            'out_of_stock_count' => $products->where('stock', 0)->count(),
            'products' => $products,
        ];
    }

    public function salesByCategory(string $from, string $to): array
    {
        return DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->join('products', 'products.id', '=', 'sale_items.product_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->whereBetween('sales.sale_date', [$from, $to])
            ->where('sales.status', 'completed')
            ->groupBy('categories.id', 'categories.name')
            ->selectRaw('categories.name as category, SUM(sale_items.quantity) as qty, SUM(sale_items.subtotal) as revenue')
            ->orderByDesc('revenue')
            ->get()
            ->toArray();
    }
}
