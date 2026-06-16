@extends('layouts.app')
@section('title', 'Supplier — ' . $supplier->name)
@section('content')
<div class="container-fluid">
    <h3><i class="fas fa-handshake me-2 text-primary"></i>{{ $supplier->name }}</h3>
    <div class="row g-3">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <table class="table table-sm">
                        <tr><th>Kode</th><td><code>{{ $supplier->code }}</code></td></tr>
                        <tr><th>CP</th><td>{{ $supplier->contact_person ?? '-' }}</td></tr>
                        <tr><th>HP</th><td>{{ $supplier->phone ?? '-' }}</td></tr>
                        <tr><th>Email</th><td>{{ $supplier->email ?? '-' }}</td></tr>
                        <tr><th>Alamat</th><td>{{ $supplier->address ?? '-' }}</td></tr>
                        <tr><th>Kota</th><td>{{ $supplier->city ?? '-' }}</td></tr>
                        <tr><th>NPWP</th><td>{{ $supplier->npwp ?? '-' }}</td></tr>
                    </table>
                    <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-warning w-100"><i class="fas fa-edit me-1"></i>Edit</a>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white"><h5>Riwayat Pembelian</h5></div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light"><tr><th>PO</th><th>Tanggal</th><th>Status</th><th class="text-end">Total</th></tr></thead>
                        <tbody>
                            @forelse ($supplier->purchases as $p)
                            <tr>
                                <td><a href="{{ route('purchases.show', $p) }}"><code>{{ $p->invoice_number }}</code></a></td>
                                <td>{{ $p->purchase_date->format('d M Y') }}</td>
                                <td><span class="badge bg-info">{{ $p->status }}</span></td>
                                <td class="text-end text-money">Rp {{ number_format($p->total, 0, ',', '.') }}</td>
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
