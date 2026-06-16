<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseRequest;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Services\PurchaseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    public function __construct(
        private PurchaseService $purchaseService,
    ) {}

    public function index(Request $request): View
    {
        $query = Purchase::with(['supplier', 'user', 'items']);
        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where('invoice_number', 'like', "%{$search}%");
        }
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }
        if ($request->filled('date_from')) {
            $query->whereDate('purchase_date', '>=', $request->date('date_from'));
        }
        $purchases = $query->latest('purchase_date')->paginate(20)->withQueryString();
        return view('purchases.index', compact('purchases'));
    }

    public function create(): View
    {
        $suppliers = Supplier::active()->orderBy('name')->get();
        $products = Product::active()->orderBy('name')->limit(200)->get();
        return view('purchases.create', compact('suppliers', 'products'));
    }

    public function store(StorePurchaseRequest $request): RedirectResponse
    {
        try {
            $purchase = $this->purchaseService->createPurchase($request->validated());
            return redirect()->route('purchases.show', $purchase)->with('success', "PO {$purchase->invoice_number} berhasil dibuat");
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(Purchase $purchase): View
    {
        $purchase->load(['supplier', 'user', 'items.product']);
        return view('purchases.show', compact('purchase'));
    }

    public function receive(Request $request, Purchase $purchase): RedirectResponse
    {
        $this->authorize('receive', $purchase);
        try {
            $this->purchaseService->receivePurchase($purchase, $request->array('quantities'));
            return redirect()->route('purchases.show', $purchase)->with('success', 'Barang berhasil diterima dan masuk ke stok');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function cancel(Purchase $purchase): RedirectResponse
    {
        try {
            $this->purchaseService->cancelPurchase($purchase);
            return redirect()->route('purchases.show', $purchase)->with('success', 'PO dibatalkan');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
