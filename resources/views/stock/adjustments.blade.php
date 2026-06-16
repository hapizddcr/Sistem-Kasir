@extends('layouts.app')
@section('title', 'Penyesuaian Stok')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between mb-3">
        <h3><i class="fas fa-adjust me-2 text-primary"></i>Penyesuaian Stok</h3>
        <a href="{{ route('stock.create-adjustment') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Buat Penyesuaian</a>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr><th>Referensi</th><th>Tanggal</th><th>Alasan</th><th>User</th><th>Items</th><th>Status</th><th>Catatan</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($adjustments as $a)
                        <tr>
                            <td><code>{{ $a->reference }}</code></td>
                            <td>{{ $a->adjustment_date->format('d M Y') }}</td>
                            <td><span class="badge bg-info">{{ $a->reason }}</span></td>
                            <td>{{ $a->user->name ?? '-' }}</td>
                            <td>{{ $a->items->count() }} produk</td>
                            <td><span class="badge bg-{{ $a->status === 'approved' ? 'success' : 'secondary' }}">{{ $a->status }}</span></td>
                            <td><small>{{ $a->notes }}</small></td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $adjustments->links() }}
        </div>
    </div>
</div>
@endsection
