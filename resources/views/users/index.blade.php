@extends('layouts.app')
@section('title', 'User')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between mb-3">
        <h3><i class="fas fa-user-shield me-2 text-primary"></i>Manajemen User</h3>
        <a href="{{ route('users.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Tambah User</a>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="GET" class="mb-3"><div class="row g-2"><div class="col-md-4"><input type="text" name="search" class="form-control" placeholder="Cari nama/email..." value="{{ request('search') }}"></div><div class="col-md-2"><button class="btn btn-outline-primary w-100">Cari</button></div></div></form>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr><th>Nama</th><th>Email</th><th>Roles</th><th>HP</th><th>Status</th><th class="text-end">Aksi</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $u)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $u->avatar_url }}" class="rounded-circle me-2" width="32" height="32">
                                    <div>{{ $u->name }}</div>
                                </div>
                            </td>
                            <td>{{ $u->email }}</td>
                            <td>@foreach($u->roles as $r)<span class="badge bg-primary me-1">{{ $r->name }}</span>@endforeach</td>
                            <td>{{ $u->phone ?? '-' }}</td>
                            <td><span class="badge bg-{{ $u->is_active ? 'success' : 'secondary' }}">{{ $u->is_active ? 'Aktif' : 'Non' }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('users.edit', $u) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                @if ($u->id !== auth()->id())
                                <form method="POST" action="{{ route('users.destroy', $u) }}" class="d-inline" onsubmit="return confirm('Hapus user?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
