@extends('layouts.app')
@section('title', 'Penjualan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between mb-3">
        <h3><i class="fas fa-receipt me-2 text-primary"></i>Daftar Penjualan</h3>
        <a href="{{ route('sales.pos') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Transaksi Baru</a>
    </div>

    <div class="row g-2 mb-3">
        <div class="col-md-3">
            <div class="stat-card"><small class="text-muted">Total Pendapatan</small>
                <h5 class="text-money mb-0">Rp {{ number_format($summary['total_revenue'] ?? 0, 0, ',', '.') }}</h5>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card"><small class="text-muted">Jumlah Transaksi</small>
                <h5 class="text-money mb-0">{{ $summary['total_transactions'] ?? 0 }}</h5>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="GET" class="row g-2 mb-3">
                <div class="col-md-3"><input type="text" name="search" class="form-control" placeholder="Invoice..." value="{{ request('search') }}"></div>
                <div class="col-md-2"><input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}"></div>
                <div class="col-md-2"><input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}"></div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Batal</option>
                        <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>Refund</option>
                    </select>
                </div>
                <div class="col-md-2"><button class="btn btn-outline-primary w-100">Filter</button></div>
                <div class="col-md-1"><a href="{{ route('reports.export.sales') }}" class="btn btn-success w-100"><i class="fas fa-file-csv"></i></a></div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Invoice</th>
                            <th>Tanggal</th>
                            <th>Kasir</th>
                            <th>Pelanggan</th>
                            <th>Items</th>
                            <th class="text-end">Total</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sales as $s)
                        <tr>
                            <td><code>{{ $s->invoice_number }}</code></td>
                            <td>{{ $s->sale_date->format('d M Y H:i') }}</td>
                            <td>{{ $s->user->name ?? '-' }}</td>
                            <td>{{ $s->customer->name ?? 'Umum' }}</td>
                            <td>{{ $s->items->sum('quantity') }}</td>
                            <td class="text-end text-money fw-bold">Rp {{ number_format($s->total, 0, ',', '.') }}</td>
                            <td>
                                @php
                                    $colors = ['completed' => 'success', 'cancelled' => 'secondary', 'refunded' => 'warning'];
                                @endphp
                                <span class="badge bg-{{ $colors[$s->status] ?? 'secondary' }}">{{ $s->status }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('sales.show', $s) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('sales.receipt', $s) }}" target="_blank" class="btn btn-sm btn-secondary"><i class="fas fa-print"></i></a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">Belum ada penjualan</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $sales->links() }}
        </div>
    </div>
</div>
@endsection
