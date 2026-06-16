@extends('layouts.app')
@section('title', 'Stok Menipis')
@section('content')
<div class="container-fluid">
    <h3 class="mb-3"><i class="fas fa-exclamation-triangle me-2 text-warning"></i>Stok Menipis</h3>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr><th>SKU</th><th>Produk</th><th>Kategori</th><th class="text-end">Stok</th><th class="text-end">Min</th><th class="text-end">Selisih</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $p)
                        <tr>
                            <td><code>{{ $p->sku }}</code></td>
                            <td><a href="{{ route('products.show', $p) }}">{{ $p->name }}</a></td>
                            <td>{{ $p->category->name }}</td>
                            <td class="text-end text-money fw-bold text-danger">{{ $p->stock }}</td>
                            <td class="text-end text-muted">{{ $p->min_stock }}</td>
                            <td class="text-end text-money text-danger">{{ $p->stock - $p->min_stock }}</td>
                            <td><span class="badge bg-{{ $p->stock === 0 ? 'danger' : 'warning' }}">{{ $p->stock === 0 ? 'Habis' : 'Menipis' }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">Semua stok aman ✓</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
