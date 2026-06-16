@extends('layouts.app')
@section('title', 'Pembelian')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between mb-3">
        <h3><i class="fas fa-truck me-2 text-primary"></i>Pembelian</h3>
        <a href="{{ route('purchases.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>PO Baru</a>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="GET" class="row g-2 mb-3">
                <div class="col-md-3"><input type="text" name="search" class="form-control" placeholder="No PO..." value="{{ request('search') }}"></div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        @foreach (['draft', 'ordered', 'partial', 'received', 'cancelled'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2"><input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}"></div>
                <div class="col-md-1"><button class="btn btn-outline-primary w-100">Filter</button></div>
            </form>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr><th>No PO</th><th>Tanggal</th><th>Supplier</th><th>Items</th><th class="text-end">Total</th><th>Status</th><th class="text-end">Aksi</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($purchases as $p)
                        <tr>
                            <td><a href="{{ route('purchases.show', $p) }}"><code>{{ $p->invoice_number }}</code></a></td>
                            <td>{{ $p->purchase_date->format('d M Y') }}</td>
                            <td>{{ $p->supplier->name }}</td>
                            <td>{{ $p->items->count() }}</td>
                            <td class="text-end text-money fw-bold">Rp {{ number_format($p->total, 0, ',', '.') }}</td>
                            <td><span class="badge bg-info">{{ $p->status }}</span></td>
                            <td class="text-end"><a href="{{ route('purchases.show', $p) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a></td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $purchases->links() }}
        </div>
    </div>
</div>
@endsection
