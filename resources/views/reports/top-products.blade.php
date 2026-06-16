@extends('layouts.app')
@section('title', 'Produk Terlaris')
@section('content')
<div class="container-fluid">
    <h3 class="mb-3"><i class="fas fa-fire me-2 text-primary"></i>Produk Terlaris</h3>
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2">
                <div class="col-md-3"><label>Dari</label><input type="date" name="from" class="form-control" value="{{ $from }}"></div>
                <div class="col-md-3"><label>Sampai</label><input type="date" name="to" class="form-control" value="{{ $to }}"></div>
                <div class="col-md-2"><label>&nbsp;</label><button class="btn btn-primary w-100">Tampilkan</button></div>
            </form>
        </div>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>#</th><th>Produk</th><th class="text-end">Qty</th><th class="text-end">Transaksi</th><th class="text-end">Pendapatan</th><th class="text-end">Laba</th></tr>
                </thead>
                <tbody>
                    @forelse ($products as $i => $p)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $p['product_name'] }}</td>
                        <td class="text-end text-money">{{ $p['quantity_sold'] }}</td>
                        <td class="text-end">{{ $p['transactions'] }}</td>
                        <td class="text-end text-money">Rp {{ number_format($p['revenue'], 0, ',', '.') }}</td>
                        <td class="text-end text-money text-success">Rp {{ number_format($p['profit'], 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Belum ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
