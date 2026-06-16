@extends('layouts.app')
@section('title', $product->name)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between mb-3">
        <h3><i class="fas fa-box me-2 text-primary"></i>{{ $product->name }}</h3>
        <a href="{{ route('products.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Kembali</a>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="bg-light rounded p-4 mb-3"><i class="fas fa-box fa-4x text-secondary"></i></div>
                    <h4 class="mb-1">{{ $product->name }}</h4>
                    <code class="text-muted">{{ $product->sku }}</code>
                    <hr>
                    <div class="row text-start">
                        <div class="col-6"><small class="text-muted">Kategori</small><div>{{ $product->category->name }}</div></div>
                        <div class="col-6"><small class="text-muted">Satuan</small><div>{{ $product->unit->name ?? '-' }}</div></div>
                        <div class="col-6 mt-2"><small class="text-muted">Stok</small><div class="fw-bold {{ $product->isLowStock() ? 'text-danger' : 'text-success' }}">{{ $product->stock }}</div></div>
                        <div class="col-6 mt-2"><small class="text-muted">Min Stok</small><div>{{ $product->min_stock }}</div></div>
                        <div class="col-6 mt-2"><small class="text-muted">Harga Beli</small><div class="text-money">Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</div></div>
                        <div class="col-6 mt-2"><small class="text-muted">Harga Jual</small><div class="text-money fw-bold">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</div></div>
                        <div class="col-12 mt-2"><small class="text-muted">Margin</small><div class="text-money">{{ number_format($product->profitMargin(), 1) }}%</div></div>
                    </div>
                </div>
            </div>
            @if ($product->barcode)
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-body text-center">
                    <small class="text-muted">Barcode</small>
                    <img src="{{ route('products.barcode', $product) }}" alt="barcode" class="img-fluid mt-2">
                </div>
            </div>
            @endif
        </div>
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white"><h5 class="mb-0">Riwayat Stok</h5></div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr><th>Tanggal</th><th>Type</th><th class="text-end">Qty</th><th class="text-end">Stok Awal</th><th class="text-end">Stok Akhir</th><th>User</th><th>Catatan</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($product->stockMovements as $m)
                            <tr>
                                <td>{{ $m->created_at->format('d M H:i') }}</td>
                                <td><span class="badge bg-{{ $m->quantity > 0 ? 'success' : 'danger' }}">{{ $m->type }}</span></td>
                                <td class="text-end text-money">{{ $m->quantity > 0 ? '+' : '' }}{{ $m->quantity }}</td>
                                <td class="text-end text-muted">{{ $m->stock_before }}</td>
                                <td class="text-end text-muted">{{ $m->stock_after }}</td>
                                <td>{{ $m->user->name ?? '-' }}</td>
                                <td><small>{{ $m->notes }}</small></td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="text-center text-muted py-3">Belum ada pergerakan</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
