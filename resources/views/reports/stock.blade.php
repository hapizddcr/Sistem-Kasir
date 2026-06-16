@extends('layouts.app')
@section('title', 'Laporan Stok')
@section('content')
<div class="container-fluid">
    <h3 class="mb-3"><i class="fas fa-boxes me-2 text-primary"></i>Laporan Stok Barang</h3>
    <div class="row g-3 mb-3">
        <div class="col-md-3"><div class="stat-card"><small>Total Produk</small><h4>{{ $report['total_products'] }}</h4></div></div>
        <div class="col-md-3"><div class="stat-card"><small>Total Unit</small><h4 class="text-money">{{ $report['total_stock_units'] }}</h4></div></div>
        <div class="col-md-3"><div class="stat-card"><small>Nilai Stok (HPP)</small><h4 class="text-money">Rp {{ number_format($report['total_stock_value'], 0, ',', '.') }}</h4></div></div>
        <div class="col-md-3"><div class="stat-card"><small>Nilai Jual</small><h4 class="text-money text-success">Rp {{ number_format($report['total_retail_value'], 0, ',', '.') }}</h4></div></div>
        <div class="col-md-3"><div class="stat-card"><small>Potensi Laba</small><h4 class="text-money text-primary">Rp {{ number_format($report['potential_profit'], 0, ',', '.') }}</h4></div></div>
        <div class="col-md-3"><div class="stat-card"><small>Stok Menipis</small><h4 class="text-warning">{{ $report['low_stock_count'] }}</h4></div></div>
        <div class="col-md-3"><div class="stat-card"><small>Stok Habis</small><h4 class="text-danger">{{ $report['out_of_stock_count'] }}</h4></div></div>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>SKU</th><th>Produk</th><th>Kategori</th><th class="text-end">Stok</th><th class="text-end">Min</th><th class="text-end">Nilai (HPP)</th><th class="text-end">Nilai Jual</th><th>Status</th></tr>
                </thead>
                <tbody>
                    @foreach ($report['products'] as $p)
                    <tr>
                        <td><code>{{ $p->sku }}</code></td>
                        <td>{{ $p->name }}</td>
                        <td>{{ $p->category->name ?? '-' }}</td>
                        <td class="text-end text-money">{{ $p->stock }}</td>
                        <td class="text-end">{{ $p->min_stock }}</td>
                        <td class="text-end text-money">Rp {{ number_format($p->stock * $p->purchase_price, 0, ',', '.') }}</td>
                        <td class="text-end text-money">Rp {{ number_format($p->stock * $p->selling_price, 0, ',', '.') }}</td>
                        <td>@if($p->stock === 0)<span class="badge bg-danger">Habis</span>@elseif($p->stock <= $p->min_stock)<span class="badge bg-warning">Menipis</span>@else<span class="badge bg-success">Aman</span>@endif</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
