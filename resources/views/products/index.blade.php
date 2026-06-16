@extends('layouts.app')
@section('title', 'Produk')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3><i class="fas fa-box me-2 text-primary"></i>Manajemen Produk</h3>
        @can('create', App\Models\Product::class)
        <a href="{{ route('products.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>Tambah Produk
        </a>
        @endcan
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="GET" class="row g-2 mb-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama/SKU/barcode..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="category_id" class="form-select">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $c)
                        <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Non-aktif</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="form-check pt-2">
                        <input class="form-check-input" type="checkbox" name="low_stock" value="1" id="lowStock" {{ request('low_stock') ? 'checked' : '' }}>
                        <label class="form-check-label" for="lowStock">Stok Menipis</label>
                    </div>
                </div>
                <div class="col-md-1">
                    <button class="btn btn-outline-primary w-100" type="submit"><i class="fas fa-search"></i></button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>SKU</th>
                            <th>Nama</th>
                            <th>Kategori</th>
                            <th class="text-end">Harga Beli</th>
                            <th class="text-end">Harga Jual</th>
                            <th class="text-end">Stok</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $p)
                        <tr>
                            <td><code>{{ $p->sku }}</code></td>
                            <td>
                                <div class="fw-bold">{{ $p->name }}</div>
                                @if ($p->barcode)
                                <small class="text-muted">{{ $p->barcode }}</small>
                                @endif
                            </td>
                            <td><span class="badge bg-secondary">{{ $p->category->name ?? '-' }}</span></td>
                            <td class="text-end text-money">Rp {{ number_format($p->purchase_price, 0, ',', '.') }}</td>
                            <td class="text-end text-money fw-bold">Rp {{ number_format($p->selling_price, 0, ',', '.') }}</td>
                            <td class="text-end">
                                <span class="badge {{ $p->isLowStock() ? 'bg-danger' : 'bg-success' }}">
                                    {{ $p->stock }} {{ $p->unit->short_name ?? '' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $p->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $p->is_active ? 'Aktif' : 'Non-aktif' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('products.show', $p) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                @can('update', $p)
                                <a href="{{ route('products.edit', $p) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                @endcan
                                @can('delete', $p)
                                <form method="POST" action="{{ route('products.destroy', $p) }}" class="d-inline" onsubmit="return confirm('Hapus produk ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">Belum ada produk</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection
