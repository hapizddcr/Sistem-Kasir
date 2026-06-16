@extends('layouts.app')
@section('title', 'Penjualan Bulanan')
@section('content')
<div class="container-fluid">
    <h3 class="mb-3"><i class="fas fa-calendar-alt me-2 text-primary"></i>Laporan Bulanan — {{ $report['period'] }}</h3>
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2">
                <div class="col-md-2"><input type="number" name="month" class="form-control" min="1" max="12" value="{{ $month }}" placeholder="Bulan"></div>
                <div class="col-md-2"><input type="number" name="year" class="form-control" min="2020" max="2099" value="{{ $year }}"></div>
                <div class="col-md-2"><button class="btn btn-primary w-100">Tampilkan</button></div>
            </form>
        </div>
    </div>
    <div class="row g-3 mb-3">
        <div class="col-md-3"><div class="stat-card"><small class="text-muted">Transaksi</small><h4>{{ $report['total_transactions'] }}</h4></div></div>
        <div class="col-md-3"><div class="stat-card"><small class="text-muted">Pendapatan</small><h4 class="text-money">Rp {{ number_format($report['total_revenue'], 0, ',', '.') }}</h4></div></div>
        <div class="col-md-3"><div class="stat-card"><small class="text-muted">Laba</small><h4 class="text-money text-success">Rp {{ number_format($report['total_profit'], 0, ',', '.') }}</h4></div></div>
        <div class="col-md-3"><div class="stat-card"><small class="text-muted">Rata-rata/Hari</small><h4 class="text-money">Rp {{ number_format($report['average_daily_revenue'], 0, ',', '.') }}</h4></div></div>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white"><h5 class="mb-0">Breakdown Harian</h5></div>
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead class="table-light">
                    <tr><th>Tanggal</th><th>Transaksi</th><th class="text-end">Pendapatan</th><th class="text-end">Laba</th></tr>
                </thead>
                <tbody>
                    @foreach ($report['daily_breakdown'] as $d)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($d['date'])->format('d M Y') }}</td>
                        <td>{{ $d['transactions'] }}</td>
                        <td class="text-end text-money">Rp {{ number_format($d['revenue'], 0, ',', '.') }}</td>
                        <td class="text-end text-money text-success">Rp {{ number_format($d['profit'], 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
