<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Services\ReportService;
use App\Services\StockService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct(
        private ReportService $reportService,
        private StockService $stockService,
    ) {}

    public function dashboard(): View
    {
        $today = $this->reportService->dailySales(today()->toDateString());
        $month = $this->reportService->monthlySales(now()->year, now()->month);
        $lowStock = $this->stockService->getLowStockProducts();
        $topProducts = $this->reportService->topProducts(
            now()->subDays(30)->toDateString(),
            now()->toDateString(),
            5,
        );

        return view('dashboard', compact('today', 'month', 'lowStock', 'topProducts'));
    }

    public function daily(Request $request): View
    {
        $date = $request->date('date') ?? today()->toDateString();
        $report = $this->reportService->dailySales($date->toDateString());
        return view('reports.daily', compact('report', 'date'));
    }

    public function monthly(Request $request): View
    {
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);
        $report = $this->reportService->monthlySales($year, $month);
        return view('reports.monthly', compact('report', 'month', 'year'));
    }

    public function topProducts(Request $request): View
    {
        $from = $request->date('from', now()->subDays(30))->toDateString();
        $to = $request->date('to', now())->toDateString();
        $products = $this->reportService->topProducts($from, $to, 20);
        return view('reports.top-products', compact('products', 'from', 'to'));
    }

    public function profitLoss(Request $request): View
    {
        $from = $request->date('from', now()->startOfMonth())->toDateString();
        $to = $request->date('to', now())->toDateString();
        $report = $this->reportService->profitLoss($from, $to);
        return view('reports.profit-loss', compact('report', 'from', 'to'));
    }

    public function stock(): View
    {
        $report = $this->reportService->stockReport();
        return view('reports.stock', compact('report'));
    }

    public function salesByCategory(Request $request): View
    {
        $from = $request->date('from', now()->startOfMonth())->toDateString();
        $to = $request->date('to', now())->toDateString();
        $data = $this->reportService->salesByCategory($from, $to);
        return view('reports.by-category', compact('data', 'from', 'to'));
    }

    /**
     * Export sales as CSV.
     */
    public function exportSales(Request $request): StreamedResponse
    {
        $query = Sale::with(['customer', 'user', 'items']);
        if ($request->filled('date_from')) {
            $query->whereDate('sale_date', '>=', $request->date('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('sale_date', '<=', $request->date('date_to'));
        }
        $sales = $query->latest('sale_date')->get();

        $filename = 'sales-' . now()->format('Ymd-His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        return response()->stream(function () use ($sales) {
            $handle = fopen('php://output', 'w');
            // BOM for Excel UTF-8 detection
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Invoice', 'Tanggal', 'Kasir', 'Pelanggan', 'Items', 'Subtotal', 'Diskon', 'Pajak', 'Total', 'Status']);

            foreach ($sales as $sale) {
                fputcsv($handle, [
                    $sale->invoice_number,
                    $sale->sale_date->format('Y-m-d H:i'),
                    $sale->user->name ?? '-',
                    $sale->customer->name ?? 'Umum',
                    $sale->items->sum('quantity'),
                    $sale->subtotal,
                    $sale->discount,
                    $sale->tax,
                    $sale->total,
                    $sale->status,
                ]);
            }
            fclose($handle);
        }, 200, $headers);
    }
}
