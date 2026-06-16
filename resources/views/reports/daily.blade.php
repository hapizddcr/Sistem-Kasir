@extends('layouts.app')
@section('title', 'Penjualan Harian')
@section('content')
<div class="container-fluid">
    <h3 class="mb-3"><i class="fas fa-calendar-day me-2 text-primary"></i>Laporan Penjualan Harian</h3>
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2">
                <div class="col-md-3"><input type="date" name="date" class="form-control" value="{{ $date->format('Y-m-d') }}"></div>
                <div class="col-md-2"><button class="btn btn-primary w-100">Tampilkan</button></div>
                <div class="col-md-2 ms-auto"><a href="{{ route('reports.export.sales', ['date_from' => $date->format('Y-m-d'), 'date_to' => $date->format('Y-m-d')]) }}" class="btn btn-success w-100"><i class="fas fa-file-csv me-1"></i>Export CSV</a></div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-3"><div class="stat-card"><small class="text-muted">Transaksi</small><h4 class="text-money">{{ $report['total_transactions'] }}</h4></div></div>
        <div class="col-md-3"><div class="stat-card"><small class="text-muted">Item Terjual</small><h4 class="text-money">{{ $report['total_items_sold'] }}</h4></div></div>
        <div class="col-md-3"><div class="stat-card"><small class="text-muted">Pendapatan</small><h4 class="text-money">Rp {{ number_format($report['total_revenue'], 0, ',', '.') }}</h4></div></div>
        <div class="col-md-3"><div class="stat-card"><small class="text-muted">Laba Kotor</small><h4 class="text-money text-success">Rp {{ number_format($report['total_profit'], 0, ',', '.') }}</h4></div></div>
        <div class="col-md-3"><div class="stat-card"><small class="text-muted">Rata-rata</small><h4 class="text-money">Rp {{ number_format($report['average_transaction'], 0, ',', '.') }}</h4></div></div>
        <div class="col-md-3"><div class="stat-card"><small class="text-muted">Pajak</small><h4 class="text-money">Rp {{ number_format($report['total_tax'], 0, ',', '.') }}</h4></div></div>
        <div class="col-md-3"><div class="stat-card"><small class="text-muted">Diskon</small><h4 class="text-money">Rp {{ number_format($report['total_discount'], 0, ',', '.') }}</h4></div></div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white"><h5 class="mb-0">Daftar Transaksi — {{ $date->format('d F Y') }}</h5></div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr><th>Invoice</th><th>Waktu</th><th>Kasir</th><th>Pelanggan</th><th>Items</th><th class="text-end">Total</th></tr>
                </thead>
                <tbody>
                    @forelse ($report['sales'] as $s)
                    <tr>
                        <td><a href="{{ route('sales.show', $s) }}"><code>{{ $s->invoice_number }}</code></a></td>
                        <td>{{ $s->sale_date->format('H:i') }}</td>
                        <td>{{ $s->user->name }}</td>
                        <td>{{ $s->customer->name ?? 'Umum' }}</td>
                        <td>{{ $s->items->sum('quantity') }}</td>
                        <td class="text-end text-money fw-bold">Rp {{ number_format($s->total, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada transaksi</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
