@extends('layouts.app')
@section('title', $product->exists ? 'Edit Produk' : 'Tambah Produk')

@section('content')
<div class="container-fluid">
    <h3 class="mb-3"><i class="fas fa-{{ $product->exists ? 'edit' : 'plus' }} me-2 text-primary"></i>{{ $product->exists ? 'Edit' : 'Tambah' }} Produk</h3>

    <form method="POST" action="{{ $product->exists ? route('products.update', $product) : route('products.store') }}" enctype="multipart/form-data">
        @csrf
        @if ($product->exists) @method('PUT') @endif

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">SKU <span class="text-danger">*</span></label>
                        <input type="text" name="sku" class="form-control @error('sku') is-invalid @enderror" value="{{ old('sku', $product->sku) }}" required>
                        @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Barcode</label>
                        <input type="text" name="barcode" class="form-control" value="{{ old('barcode', $product->barcode) }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $product->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kategori <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select" required>
                            <option value="">Pilih</option>
                            @foreach ($categories as $c)
                            <option value="{{ $c->id }}" {{ old('category_id', $product->category_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Satuan <span class="text-danger">*</span></label>
                        <select name="unit_id" class="form-select" required>
                            <option value="">Pilih</option>
                            @foreach ($units as $u)
                            <option value="{{ $u->id }}" {{ old('unit_id', $product->unit_id) == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->short_name }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="2">{{ old('description', $product->description) }}</textarea>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Harga Beli <span class="text-danger">*</span></label>
                        <input type="number" name="purchase_price" class="form-control text-money" value="{{ old('purchase_price', $product->purchase_price ?? 0) }}" step="0.01" min="0" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Harga Jual <span class="text-danger">*</span></label>
                        <input type="number" name="selling_price" class="form-control text-money" value="{{ old('selling_price', $product->selling_price ?? 0) }}" step="0.01" min="0" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Stok Awal</label>
                        <input type="number" name="stock" class="form-control" value="{{ old('stock', $product->stock ?? 0) }}" min="0">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Min Stok</label>
                        <input type="number" name="min_stock" class="form-control" value="{{ old('min_stock', $product->min_stock ?? 0) }}" min="0">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Gambar Produk</label>
                        <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                        <small class="text-muted">Bisa upload multiple. Max 2MB per file.</small>
                    </div>
                    <div class="col-md-6 d-flex align-items-end gap-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="isActive" {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="isActive">Aktif</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="track_stock" value="1" class="form-check-input" id="trackStock" {{ old('track_stock', $product->track_stock ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="trackStock">Lacak Stok</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="is_taxable" value="1" class="form-check-input" id="isTaxable" {{ old('is_taxable', $product->is_taxable ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="isTaxable">Kena Pajak</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white d-flex justify-content-between">
                <a href="{{ route('products.index') }}" class="btn btn-secondary">Batal</a>
                <button class="btn btn-primary" type="submit"><i class="fas fa-save me-1"></i>Simpan</button>
            </div>
        </div>
    </form>
</div>
@endsection
