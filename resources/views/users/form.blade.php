@extends('layouts.app')
@section('title', 'User Form')
@section('content')
<div class="container-fluid">
    <h3 class="mb-3"><i class="fas fa-user-shield me-2 text-primary"></i>{{ $user->exists ? 'Edit' : 'Tambah' }} User</h3>
    <form method="POST" action="{{ $user->exists ? route('users.update', $user) : route('users.store') }}">
        @csrf
        @if ($user->exists) @method('PUT') @endif
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-md-6"><label class="form-label">Nama <span class="text-danger">*</span></label><input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required></div>
                    <div class="col-md-6"><label class="form-label">Email <span class="text-danger">*</span></label><input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required></div>
                    <div class="col-md-4"><label class="form-label">{{ $user->exists ? 'Password Baru (kosongkan jika tidak diubah)' : 'Password' }}</label><input type="password" name="password" class="form-control" {{ $user->exists ? '' : 'required' }}></div>
                    <div class="col-md-4"><label class="form-label">Konfirmasi</label><input type="password" name="password_confirmation" class="form-control" {{ $user->exists ? '' : 'required' }}></div>
                    <div class="col-md-4"><label class="form-label">HP</label><input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}"></div>
                    <div class="col-12">
                        <label class="form-label">Roles <span class="text-danger">*</span></label>
                        <div class="row">
                            @foreach ($roles as $r)
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $r->id }}" id="r{{ $r->id }}" {{ in_array($r->id, old('roles', $user->exists ? $user->roles->pluck('id')->toArray() : [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="r{{ $r->id }}">{{ $r->name }}</label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-12"><div class="form-check"><input type="checkbox" name="is_active" value="1" class="form-check-input" id="isActive" {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }}><label class="form-check-label" for="isActive">Aktif</label></div></div>
                </div>
            </div>
            <div class="card-footer bg-white d-flex justify-content-between">
                <a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a>
                <button class="btn btn-primary" type="submit"><i class="fas fa-save me-1"></i>Simpan</button>
            </div>
        </div>
    </form>
</div>
@endsection
