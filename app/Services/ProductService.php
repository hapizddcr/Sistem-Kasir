<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class ProductService
{
    public function createProduct(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            $data['slug'] = $data['slug'] ?? Str::slug($data['name']) . '-' . Str::random(5);
            $data['barcode'] = $data['barcode'] ?? $data['sku'];

            $product = Product::create($data);

            if (!empty($data['images'])) {
                $this->attachImages($product, $data['images']);
            }

            return $product->fresh(['category', 'unit', 'images']);
        });
    }

    public function updateProduct(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            $product->update($data);

            if (!empty($data['images'])) {
                $this->attachImages($product, $data['images']);
            }

            return $product->fresh(['category', 'unit', 'images']);
        });
    }

    public function deleteProduct(Product $product): bool
    {
        if ($product->saleItems()->exists() || $product->purchaseItems()->exists()) {
            throw new RuntimeException('Tidak dapat menghapus produk yang memiliki transaksi');
        }

        // Delete associated images from storage
        foreach ($product->images as $image) {
            $this->deleteImageFile($image);
        }

        return $product->delete();
    }

    /**
     * Search products by name, SKU, or barcode.
     */
    public function search(string $query, int $limit = 20)
    {
        return Product::active()
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('sku', 'like', "%{$query}%")
                    ->orWhere('barcode', 'like', "%{$query}%");
            })
            ->with(['category', 'unit', 'primaryImage'])
            ->limit($limit)
            ->get();
    }

    /**
     * Find a product by SKU or barcode (for POS quick lookup).
     */
    public function findByBarcode(string $code): ?Product
    {
        return Product::where('barcode', $code)
            ->orWhere('sku', $code)
            ->with(['category', 'unit', 'primaryImage'])
            ->first();
    }

    private function attachImages(Product $product, array $images): void
    {
        foreach ($images as $index => $image) {
            if (!$image instanceof UploadedFile) continue;

            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs("products/{$product->id}", $filename, 'public');

            ProductImage::create([
                'product_id' => $product->id,
                'path' => $path,
                'filename' => $filename,
                'is_primary' => $index === 0,
                'sort_order' => $index,
            ]);
        }
    }

    private function deleteImageFile(ProductImage $image): void
    {
        $fullPath = public_path('storage/' . $image->path);
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }

    public function generateUniqueSku(Category $category = null): string
    {
        $prefix = $category ? strtoupper(substr($category->name, 0, 3)) : 'PRD';
        do {
            $sku = $prefix . '-' . str_pad((string) random_int(1, 99999), 5, '0', STR_PAD_LEFT);
        } while (Product::where('sku', $sku)->exists());
        return $sku;
    }
}
