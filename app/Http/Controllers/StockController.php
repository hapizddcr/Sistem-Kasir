<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockAdjustment;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockController extends Controller
{
    public function __construct(
        private StockService $stockService,
    ) {}

    public function history(Request $request): View
    {
        $query = \App\Models\StockMovement::with(['product', 'user']);

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->integer('product_id'));
        }
        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date('date_to'));
        }

        $movements = $query->latest('created_at')->paginate(30)->withQueryString();
        $products = Product::orderBy('name')->limit(500)->get(['id', 'name', 'sku']);
        return view('stock.history', compact('movements', 'products'));
    }

    public function lowStock(): View
    {
        $products = $this->stockService->getLowStockProducts();
        return view('stock.low', compact('products'));
    }

    public function adjustments(Request $request): View
    {
        $adjustments = StockAdjustment::with(['user', 'items.product'])
            ->latest()
            ->paginate(20);
        return view('stock.adjustments', compact('adjustments'));
    }

    public function createAdjustment(): View
    {
        $products = Product::orderBy('name')->get(['id', 'name', 'sku', 'stock']);
        return view('stock.create-adjustment', compact('products'));
    }

    public function storeAdjustment(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'reason' => 'required|in:opname,damage,expired,lost,found,correction,other',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.actual_stock' => 'required|integer|min:0',
        ]);

        $adjustment = StockAdjustment::create([
            'reference' => 'ADJ-' . now()->format('Ymd') . '-' . str_pad((string) (StockAdjustment::count() + 1), 4, '0', STR_PAD_LEFT),
            'user_id' => auth()->id(),
            'adjustment_date' => now()->toDateString(),
            'reason' => $data['reason'],
            'notes' => $data['notes'] ?? null,
            'status' => 'approved',
        ]);

        foreach ($data['items'] as $item) {
            $product = Product::findOrFail($item['product_id']);
            $difference = $item['actual_stock'] - $product->stock;

            if ($difference !== 0) {
                $adjustment->items()->create([
                    'product_id' => $product->id,
                    'system_stock' => $product->stock,
                    'actual_stock' => $item['actual_stock'],
                    'difference' => $difference,
                    'unit_cost' => $product->purchase_price,
                ]);

                $this->stockService->record(
                    product: $product,
                    type: 'adjustment',
                    quantity: $difference,
                    notes: "Penyesuaian stok: {$data['reason']}",
                    referenceType: StockAdjustment::class,
                    referenceId: $adjustment->id,
                );
            }
        }

        return redirect()->route('stock.adjustments')->with('success', 'Penyesuaian stok berhasil');
    }

    public function stockIn(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $product = Product::findOrFail($data['product_id']);
        $this->stockService->record(
            product: $product,
            type: 'in',
            quantity: $data['quantity'],
            notes: $data['notes'] ?? 'Barang masuk manual',
        );

        return back()->with('success', "Stok {$product->name} ditambah {$data['quantity']}");
    }

    public function stockOut(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $product = Product::findOrFail($data['product_id']);
        try {
            $this->stockService->record(
                product: $product,
                type: 'out',
                quantity: $data['quantity'],
                notes: $data['notes'] ?? 'Barang keluar manual',
            );
            return back()->with('success', "Stok {$product->name} dikurangi {$data['quantity']}");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
