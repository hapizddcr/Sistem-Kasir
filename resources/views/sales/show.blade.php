@extends('layouts.app')
@section('title', $sale->invoice_number)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between mb-3 no-print">
        <h3><i class="fas fa-receipt me-2 text-primary"></i>{{ $sale->invoice_number }}</h3>
        <div>
            <a href="{{ route('sales.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Kembali</a>
            <a href="{{ route('sales.receipt', $sale) }}" target="_blank" class="btn btn-primary"><i class="fas fa-print me-1"></i>Cetak</a>
            @if ($sale->status === 'completed' && auth()->user()->can('refund', $sale))
            <form method="POST" action="{{ route('sales.refund', $sale) }}" class="d-inline" onsubmit="return confirm('Yakin refund penjualan ini?')">
                @csrf
                <input type="hidden" name="reason" value="Customer request">
                <button class="btn btn-danger"><i class="fas fa-undo me-1"></i>Refund</button>
            </form>
            @endif
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Info</h5>
                    <table class="table table-sm">
                        <tr><th>Tanggal</th><td>{{ $sale->sale_date->format('d M Y H:i') }}</td></tr>
                        <tr><th>Kasir</th><td>{{ $sale->user->name }}</td></tr>
                        <tr><th>Pelanggan</th><td>{{ $sale->customer->name ?? 'Umum' }}</td></tr>
                        <tr><th>Status</th><td><span class="badge bg-{{ $sale->status === 'completed' ? 'success' : 'secondary' }}">{{ $sale->status }}</span></td></tr>
                        @if ($sale->points_earned)
                        <tr><th>Poin</th><td><span class="badge bg-warning text-dark">+{{ $sale->points_earned }}</span></td></tr>
                        @endif
                    </table>
                </div>
            </div>
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-body">
                    <h5>Pembayaran</h5>
                    @foreach ($sale->payments as $p)
                    <div class="d-flex justify-content-between"><span>{{ ucfirst($p->method) }} @if($p->reference) <small class="text-muted">({{ $p->reference }})</small> @endif</span><span class="text-money">Rp {{ number_format($p->amount, 0, ',', '.') }}</span></div>
                    @endforeach
                    <hr>
                    <div class="d-flex justify-content-between"><span>Dibayar</span><span class="text-money fw-bold">Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</span></div>
                    <div class="d-flex justify-content-between text-success"><span>Kembali</span><span class="text-money">Rp {{ number_format($sale->change_amount, 0, ',', '.') }}</span></div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white"><h5 class="mb-0">Item</h5></div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="table-light">
                            <tr><th>Produk</th><th>SKU</th><th class="text-end">Harga</th><th class="text-end">Qty</th><th class="text-end">Subtotal</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($sale->items as $item)
                            <tr>
                                <td>{{ $item->product_name }}</td>
                                <td><code>{{ $item->product_sku }}</code></td>
                                <td class="text-end text-money">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                <td class="text-end">{{ $item->quantity }}</td>
                                <td class="text-end text-money fw-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr><td colspan="4" class="text-end">Subtotal</td><td class="text-end text-money">Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</td></tr>
                            <tr><td colspan="4" class="text-end text-danger">Diskon</td><td class="text-end text-money">- Rp {{ number_format($sale->discount, 0, ',', '.') }}</td></tr>
                            <tr><td colspan="4" class="text-end">Pajak</td><td class="text-end text-money">Rp {{ number_format($sale->tax, 0, ',', '.') }}</td></tr>
                            <tr class="table-light"><td colspan="4" class="text-end fw-bold fs-5">TOTAL</td><td class="text-end text-money fw-bold fs-5 text-success">Rp {{ number_format($sale->total, 0, ',', '.') }}</td></tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
