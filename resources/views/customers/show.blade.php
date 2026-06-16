@extends('layouts.app')
@section('title', 'Pelanggan — ' . $customer->name)
@section('content')
<div class="container-fluid">
    <h3><i class="fas fa-user me-2 text-primary"></i>{{ $customer->name }}</h3>
    <div class="row g-3">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <table class="table table-sm">
                        <tr><th>Kode</th><td><code>{{ $customer->code }}</code></td></tr>
                        <tr><th>HP</th><td>{{ $customer->phone ?? '-' }}</td></tr>
                        <tr><th>Email</th><td>{{ $customer->email ?? '-' }}</td></tr>
                        <tr><th>Gender</th><td>{{ $customer->gender === 'male' ? 'Laki-laki' : ($customer->gender === 'female' ? 'Perempuan' : '-') }}</td></tr>
                        <tr><th>TTL</th><td>{{ $customer->birth_date?->format('d M Y') ?? '-' }}</td></tr>
                        <tr><th>Alamat</th><td>{{ $customer->address ?? '-' }}</td></tr>
                    </table>
                </div>
            </div>
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-body">
                    <h5>Statistik</h5>
                    <div class="d-flex justify-content-between"><span>Membership:</span><span>@if($customer->membership)<span class="badge" style="background: {{ $customer->membership->color }}">{{ $customer->membership->name }}</span>@else - @endif</span></div>
                    <div class="d-flex justify-content-between mt-2"><span>Poin:</span><span class="fw-bold text-money">{{ $customer->points }}</span></div>
                    <div class="d-flex justify-content-between"><span>Total Belanja:</span><span class="text-money">Rp {{ number_format($customer->total_spent, 0, ',', '.') }}</span></div>
                    <div class="d-flex justify-content-between"><span>Transaksi:</span><span class="text-money">{{ $customer->total_transactions }}</span></div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white"><h5 class="mb-0">Riwayat Pembelian</h5></div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light"><tr><th>Invoice</th><th>Tanggal</th><th>Items</th><th class="text-end">Total</th></tr></thead>
                        <tbody>
                            @forelse ($customer->sales as $s)
                            <tr>
                                <td><a href="{{ route('sales.show', $s) }}"><code>{{ $s->invoice_number }}</code></a></td>
                                <td>{{ $s->sale_date->format('d M Y') }}</td>
                                <td>{{ $s->items->sum('quantity') }}</td>
                                <td class="text-end text-money">Rp {{ number_format($s->total, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">Belum ada</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
