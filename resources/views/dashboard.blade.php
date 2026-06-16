@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0"><i class="fas fa-tachometer-alt me-2 text-primary"></i>Dashboard</h3>
        <small class="text-muted">{{ now()->format('l, d F Y') }}</small>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted">Penjualan Hari Ini</small>
                        <h4 class="mb-0 text-money">Rp {{ number_format($today['total_revenue'], 0, ',', '.') }}</h4>
                    </div>
                    <div class="stat-icon bg-success bg-opacity-10 text-success">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                </div>
                <small class="text-muted mt-2 d-block">{{ $today['total_transactions'] }} transaksi</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted">Laba Hari Ini</small>
                        <h4 class="mb-0 text-money">Rp {{ number_format($today['total_profit'], 0, ',', '.') }}</h4>
                    </div>
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
                <small class="text-muted mt-2 d-block">Margin: {{ $today['total_revenue'] > 0 ? round(($today['total_profit'] / $today['total_revenue']) * 100, 1) : 0 }}%</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted">Penjualan Bulan Ini</small>
                        <h4 class="mb-0 text-money">Rp {{ number_format($month['total_revenue'], 0, ',', '.') }}</h4>
                    </div>
                    <div class="stat-icon bg-info bg-opacity-10 text-info">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
                <small class="text-muted mt-2 d-block">{{ $month['total_transactions'] }} transaksi</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted">Stok Menipis</small>
                        <h4 class="mb-0 text-money">{{ $lowStock->count() }}</h4>
                    </div>
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
                <small class="text-muted mt-2 d-block"><a href="{{ route('stock.low') }}" class="text-decoration-none">Lihat →</a></small>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-7">
            <div class="table-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="fas fa-fire me-2 text-danger"></i>Produk Terlaris (30 hari)</h5>
                    <a href="{{ route('reports.top-products') }}" class="btn btn-sm btn-outline-primary">Semua</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Produk</th>
                                <th class="text-end">Qty</th>
                                <th class="text-end">Pendapatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($topProducts as $i => $p)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $p['product_name'] }}</td>
                                <td class="text-end text-money">{{ $p['quantity_sold'] }}</td>
                                <td class="text-end text-money">Rp {{ number_format($p['revenue'], 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">Belum ada penjualan</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="table-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2 text-warning"></i>Stok Menipis</h5>
                    <a href="{{ route('stock.low') }}" class="btn btn-sm btn-outline-warning">Semua</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Produk</th>
                                <th class="text-end">Stok</th>
                                <th class="text-end">Min</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($lowStock->take(8) as $p)
                            <tr>
                                <td>{{ $p->name }}</td>
                                <td class="text-end text-money text-danger fw-bold">{{ $p->stock }}</td>
                                <td class="text-end text-muted">{{ $p->min_stock }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center text-muted py-3">Semua stok aman ✓</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
