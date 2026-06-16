@extends('layouts.app')
@section('title', 'Pelanggan')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between mb-3">
        <h3><i class="fas fa-users me-2 text-primary"></i>Pelanggan</h3>
        <a href="{{ route('customers.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Tambah</a>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="GET" class="mb-3">
                <div class="row g-2">
                    <div class="col-md-4"><input type="text" name="search" class="form-control" placeholder="Cari nama/HP/kode..." value="{{ request('search') }}"></div>
                    <div class="col-md-2"><button class="btn btn-outline-primary w-100">Cari</button></div>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr><th>Kode</th><th>Nama</th><th>HP</th><th>Membership</th><th class="text-end">Poin</th><th class="text-end">Total Belanja</th><th>Status</th><th class="text-end">Aksi</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($customers as $c)
                        <tr>
                            <td><code>{{ $c->code }}</code></td>
                            <td><div class="fw-bold">{{ $c->name }}</div><small class="text-muted">{{ $c->email }}</small></td>
                            <td>{{ $c->phone ?? '-' }}</td>
                            <td>
                                @if ($c->membership)
                                <span class="badge" style="background: {{ $c->membership->color }}">{{ $c->membership->name }}</span>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end text-money fw-bold">{{ $c->points }}</td>
                            <td class="text-end text-money">Rp {{ number_format($c->total_spent, 0, ',', '.') }}</td>
                            <td><span class="badge bg-{{ $c->is_active ? 'success' : 'secondary' }}">{{ $c->is_active ? 'Aktif' : 'Non' }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('customers.show', $c) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('customers.edit', $c) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                <form method="POST" action="{{ route('customers.destroy', $c) }}" class="d-inline" onsubmit="return confirm('Hapus?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">Belum ada pelanggan</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $customers->links() }}
        </div>
    </div>
</div>
@endsection
