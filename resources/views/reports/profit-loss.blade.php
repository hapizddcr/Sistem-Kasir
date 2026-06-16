@extends('layouts.app')
@section('title', 'Laba Rugi')
@section('content')
<div class="container-fluid">
    <h3 class="mb-3"><i class="fas fa-chart-line me-2 text-primary"></i>Laporan Laba Rugi</h3>
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2">
                <div class="col-md-3"><label>Dari</label><input type="date" name="from" class="form-control" value="{{ $from }}"></div>
                <div class="col-md-3"><label>Sampai</label><input type="date" name="to" class="form-control" value="{{ $to }}"></div>
                <div class="col-md-2"><label>&nbsp;</label><button class="btn btn-primary w-100">Tampilkan</button></div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-3"><div class="stat-card"><small>Pendapatan</small><h4 class="text-money text-success">Rp {{ number_format($report['revenue'], 0, ',', '.') }}</h4></div></div>
        <div class="col-md-3"><div class="stat-card"><small>HPP (COGS)</small><h4 class="text-money text-danger">Rp {{ number_format($report['cogs'], 0, ',', '.') }}</h4></div></div>
        <div class="col-md-3"><div class="stat-card"><small>Laba Kotor</small><h4 class="text-money text-primary">Rp {{ number_format($report['gross_profit'], 0, ',', '.') }}</h4></div></div>
        <div class="col-md-3"><div class="stat-card"><small>Margin</small><h4 class="text-money">{{ $report['margin'] }}%</h4></div></div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <table class="table">
                <tr class="table-light"><th colspan="2" class="fs-5">Ringkasan Laba Rugi</th></tr>
                <tr><td>Pendapatan</td><td class="text-end text-money text-success fw-bold">Rp {{ number_format($report['revenue'], 0, ',', '.') }}</td></tr>
                <tr><td>(-) HPP</td><td class="text-end text-money text-danger">(Rp {{ number_format($report['cogs'], 0, ',', '.') }})</td></tr>
                <tr class="table-light"><th>Laba Kotor</th><th class="text-end text-money">Rp {{ number_format($report['gross_profit'], 0, ',', '.') }}</th></tr>
                <tr><td>(-) Diskon</td><td class="text-end text-money">(Rp {{ number_format($report['discounts'], 0, ',', '.') }})</td></tr>
                <tr><td>Pajak Terkumpul</td><td class="text-end text-money">Rp {{ number_format($report['tax_collected'], 0, ',', '.') }}</td></tr>
                <tr class="table-success"><th>Laba Bersih</th><th class="text-end text-money fs-5">Rp {{ number_format($report['net_profit'], 0, ',', '.') }}</th></tr>
            </table>
        </div>
    </div>
</div>
@endsection
