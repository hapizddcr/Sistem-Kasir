@extends('layouts.app')
@section('title', 'Supplier')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between mb-3">
        <h3><i class="fas fa-handshake me-2 text-primary"></i>Supplier</h3>
        <a href="{{ route('suppliers.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Tambah</a>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="GET" class="mb-3">
                <div class="row g-2">
                    <div class="col-md-4"><input type="text" name="search" class="form-control" placeholder="Cari..." value="{{ request('search') }}"></div>
                    <div class="col-md-2"><button class="btn btn-outline-primary w-100">Cari</button></div>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr><th>Kode</th><th>Nama</th><th>CP</th><th>HP</th><th>Email</th><th>Kota</th><th>Status</th><th class="text-end">Aksi</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($suppliers as $s)
                        <tr>
                            <td><code>{{ $s->code }}</code></td>
                            <td><div class="fw-bold">{{ $s->name }}</div><small class="text-muted">{{ $s->address }}</small></td>
                            <td>{{ $s->contact_person ?? '-' }}</td>
                            <td>{{ $s->phone ?? '-' }}</td>
                            <td>{{ $s->email ?? '-' }}</td>
                            <td>{{ $s->city ?? '-' }}</td>
                            <td><span class="badge bg-{{ $s->is_active ? 'success' : 'secondary' }}">{{ $s->is_active ? 'Aktif' : 'Non' }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('suppliers.show', $s) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('suppliers.edit', $s) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                <form method="POST" action="{{ route('suppliers.destroy', $s) }}" class="d-inline" onsubmit="return confirm('Hapus?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">Belum ada supplier</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $suppliers->links() }}
        </div>
    </div>
</div>
@endsection
