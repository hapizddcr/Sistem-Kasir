<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Models\Customer;
use App\Models\Product;
use App\Services\SalesService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function __construct(
        private SalesService $salesService,
    ) {}

    /**
     * POS interface.
     */
    public function pos(): View
    {
        $customers = Customer::active()->orderBy('name')->limit(100)->get();
        $featuredProducts = Product::active()->inStock()->with(['category', 'images'])->latest()->limit(12)->get();
        return view('sales.pos', compact('customers', 'featuredProducts'));
    }

    public function index(Request $request): View
    {
        $query = \App\Models\Sale::with(['customer', 'user', 'items']);

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where('invoice_number', 'like', "%{$search}%");
        }
        if ($request->filled('date_from')) {
            $query->whereDate('sale_date', '>=', $request->date('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('sale_date', '<=', $request->date('date_to'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $sales = $query->latest('sale_date')->paginate(20)->withQueryString();

        $summary = [
            'total_revenue' => (clone $query)->sum('total'),
            'total_transactions' => $query->count(),
        ];

        return view('sales.index', compact('sales', 'summary'));
    }

    public function show(\App\Models\Sale $sale): View
    {
        $sale->load(['customer', 'user', 'items.product', 'payments']);
        return view('sales.show', compact('sale'));
    }

    public function store(StoreSaleRequest $request): RedirectResponse|JsonResponse
    {
        try {
            $sale = $this->salesService->createSale($request->validated());
            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'data' => $sale, 'redirect' => route('sales.show', $sale)]);
            }
            return redirect()->route('sales.show', $sale)->with('success', "Penjualan {$sale->invoice_number} berhasil");
        } catch (\Throwable $e) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 422);
            }
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Calculate cart preview (called via AJAX from POS).
     */
    public function previewCart(Request $request): JsonResponse
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'nullable|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'global_discount' => 'nullable|numeric|min:0',
        ]);

        $preview = $this->salesService->calculateCart(
            $request->array('items'),
            (float) $request->input('global_discount', 0),
        );

        return response()->json($preview);
    }

    public function refund(Request $request, \App\Models\Sale $sale): RedirectResponse
    {
        $this->authorize('refund', $sale);
        $request->validate([
            'reason' => 'required|string|max:200',
            'notes' => 'nullable|string|max:500',
        ]);
        try {
            $this->salesService->refundSale($sale, $request->string('reason'), $request->string('notes'));
            return redirect()->route('sales.show', $sale)->with('success', 'Penjualan berhasil di-refund');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function receipt(\App\Models\Sale $sale)
    {
        $sale->load(['customer', 'user', 'items.product', 'payments']);
        return view('sales.receipt', compact('sale'));
    }
}
