@extends('layouts.app')
@section('title', 'Penjualan per Kategori')
@section('content')
<div class="container-fluid">
    <h3 class="mb-3"><i class="fas fa-chart-pie me-2 text-primary"></i>Penjualan per Kategori</h3>
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
                <thead class="table-light"><tr><th>Kategori</th><th class="text-end">Qty</th><th class="text-end">Pendapatan</th><th class="text-end">%</th></tr></thead>
                <tbody>
                    @php $total = array_sum(array_column($data, 'revenue')); @endphp
                    @forelse ($data as $d)
                    <tr>
                        <td><strong>{{ $d->category }}</strong></td>
                        <td class="text-end text-money">{{ $d->qty }}</td>
                        <td class="text-end text-money">Rp {{ number_format($d->revenue, 0, ',', '.') }}</td>
                        <td class="text-end">{{ $total > 0 ? round(($d->revenue / $total) * 100, 1) : 0 }}%</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">Belum ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
