@extends('layouts.app')
@section('title', 'Riwayat Stok')
@section('content')
<div class="container-fluid">
    <h3 class="mb-3"><i class="fas fa-history me-2 text-primary"></i>Riwayat Stok</h3>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="GET" class="row g-2 mb-3">
                <div class="col-md-3">
                    <select name="product_id" class="form-select">
                        <option value="">Semua Produk</option>
                        @foreach ($products as $p)
                        <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="type" class="form-select">
                        <option value="">Semua Tipe</option>
                        @foreach (['in', 'out', 'sale', 'purchase', 'adjustment', 'return', 'opname'] as $t)
                        <option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2"><input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}"></div>
                <div class="col-md-2"><input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}"></div>
                <div class="col-md-1"><button class="btn btn-outline-primary w-100">Filter</button></div>
            </form>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr><th>Tanggal</th><th>Produk</th><th>Tipe</th><th class="text-end">Qty</th><th class="text-end">Stok Awal</th><th class="text-end">Stok Akhir</th><th>User</th><th>Catatan</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($movements as $m)
                        <tr>
                            <td>{{ $m->created_at->format('d M Y H:i') }}</td>
                            <td>{{ $m->product->name ?? '-' }}</td>
                            <td><span class="badge bg-{{ $m->quantity > 0 ? 'success' : 'danger' }}">{{ $m->type }}</span></td>
                            <td class="text-end text-money {{ $m->quantity > 0 ? 'text-success' : 'text-danger' }} fw-bold">{{ $m->quantity > 0 ? '+' : '' }}{{ $m->quantity }}</td>
                            <td class="text-end text-muted">{{ $m->stock_before }}</td>
                            <td class="text-end text-muted">{{ $m->stock_after }}</td>
                            <td>{{ $m->user->name ?? '-' }}</td>
                            <td><small>{{ $m->notes }}</small></td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">Tidak ada pergerakan</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $movements->links() }}
        </div>
    </div>
</div>
@endsection
