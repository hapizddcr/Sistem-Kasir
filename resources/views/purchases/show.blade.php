@extends('layouts.app')
@section('title', 'Detail PO')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between mb-3">
        <h3><i class="fas fa-truck me-2 text-primary"></i>{{ $purchase->invoice_number }}</h3>
        <div>
            <a href="{{ route('purchases.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Kembali</a>
            @if (in_array($purchase->status, ['ordered', 'partial']))
            <form method="POST" action="{{ route('purchases.receive', $purchase) }}" class="d-inline" onsubmit="return confirm('Tandai semua item sudah diterima? Stok akan bertambah.')">
                @csrf
                <button class="btn btn-success"><i class="fas fa-check me-1"></i>Terima Semua</button>
            </form>
            @endif
        </div>
    </div>
    <div class="row g-3">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <table class="table table-sm">
                        <tr><th>Supplier</th><td>{{ $purchase->supplier->name }}</td></tr>
                        <tr><th>Tanggal</th><td>{{ $purchase->purchase_date->format('d M Y') }}</td></tr>
                        <tr><th>Expected</th><td>{{ $purchase->expected_date?->format('d M Y') ?? '-' }}</td></tr>
                        <tr><th>Received</th><td>{{ $purchase->received_date?->format('d M Y') ?? '-' }}</td></tr>
                        <tr><th>Status</th><td><span class="badge bg-info">{{ $purchase->status }}</span></td></tr>
                        <tr><th>User</th><td>{{ $purchase->user->name }}</td></tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="table-light">
                            <tr><th>Produk</th><th class="text-end">Qty</th><th class="text-end">Diterima</th><th class="text-end">Harga</th><th class="text-end">Subtotal</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($purchase->items as $item)
                            <tr>
                                <td>{{ $item->product->name }}</td>
                                <td class="text-end">{{ $item->quantity }}</td>
                                <td class="text-end"><span class="badge bg-{{ $item->received_quantity >= $item->quantity ? 'success' : 'warning' }}">{{ $item->received_quantity }}</span></td>
                                <td class="text-end text-money">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                <td class="text-end text-money">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr><td colspan="4" class="text-end">Subtotal</td><td class="text-end text-money">Rp {{ number_format($purchase->subtotal, 0, ',', '.') }}</td></tr>
                            <tr><td colspan="4" class="text-end text-danger">Diskon</td><td class="text-end text-money">-Rp {{ number_format($purchase->discount, 0, ',', '.') }}</td></tr>
                            <tr><td colspan="4" class="text-end">Pajak</td><td class="text-end text-money">Rp {{ number_format($purchase->tax, 0, ',', '.') }}</td></tr>
                            <tr><td colspan="4" class="text-end">Ongkir</td><td class="text-end text-money">Rp {{ number_format($purchase->shipping_cost, 0, ',', '.') }}</td></tr>
                            <tr class="table-light"><td colspan="4" class="text-end fw-bold fs-5">TOTAL</td><td class="text-end text-money fw-bold fs-5">Rp {{ number_format($purchase->total, 0, ',', '.') }}</td></tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
