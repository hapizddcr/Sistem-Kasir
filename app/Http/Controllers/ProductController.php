<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use App\Services\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService,
    ) {}

    public function index(Request $request): View
    {
        $query = Product::with(['category', 'unit', 'images']);

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            });
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }
        if ($request->boolean('low_stock')) {
            $query->whereColumn('stock', '<=', 'min_stock')->where('track_stock', true);
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->string('status') === 'active');
        }

        $products = $query->latest()->paginate(20)->withQueryString();
        $categories = Category::active()->orderBy('name')->get();
        return view('products.index', compact('products', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::active()->orderBy('name')->get();
        $units = Unit::active()->orderBy('name')->get();
        return view('products.create', compact('categories', 'units'));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $product = $this->productService->createProduct($request->validated());
        return redirect()
            ->route('products.index')
            ->with('success', "Produk {$product->name} berhasil ditambahkan");
    }

    public function show(Product $product): View
    {
        $product->load(['category', 'unit', 'images', 'stockMovements' => fn($q) => $q->latest()->limit(20)]);
        return view('products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        $categories = Category::active()->orderBy('name')->get();
        $units = Unit::active()->orderBy('name')->get();
        $product->load('images');
        return view('products.edit', compact('product', 'categories', 'units'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $this->productService->updateProduct($product, $request->validated());
        return redirect()
            ->route('products.index')
            ->with('success', "Produk {$product->name} berhasil diperbarui");
    }

    public function destroy(Product $product): RedirectResponse
    {
        try {
            $this->productService->deleteProduct($product);
            return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function barcode(Product $product)
    {
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        $barcode = $generator->getBarcode($product->barcode ?? $product->sku, $generator::TYPE_CODE_128, 2, 60);
        return response($barcode)->header('Content-Type', 'image/png');
    }

    public function searchApi(Request $request)
    {
        $request->validate(['q' => 'nullable|string|max:100']);
        $products = $this->productService->search($request->string('q')->toString() ?? '', 30);
        return response()->json(['data' => $products]);
    }
}
