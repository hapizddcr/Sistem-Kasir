@extends('layouts.app')
@section('title', 'Pelanggan — Form')
@section('content')
<div class="container-fluid">
    <h3 class="mb-3"><i class="fas fa-user me-2 text-primary"></i>{{ $customer->exists ? 'Edit' : 'Tambah' }} Pelanggan</h3>
    <form method="POST" action="{{ $customer->exists ? route('customers.update', $customer) : route('customers.store') }}">
        @csrf
        @if ($customer->exists) @method('PUT') @endif
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-md-6"><label class="form-label">Nama <span class="text-danger">*</span></label><input type="text" name="name" class="form-control" value="{{ old('name', $customer->name) }}" required></div>
                    <div class="col-md-3"><label class="form-label">HP</label><input type="text" name="phone" class="form-control" value="{{ old('phone', $customer->phone) }}"></div>
                    <div class="col-md-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ old('email', $customer->email) }}"></div>
                    <div class="col-md-3"><label class="form-label">Gender</label><select name="gender" class="form-select"><option value="">-</option><option value="male" {{ old('gender', $customer->gender) === 'male' ? 'selected' : '' }}>Laki-laki</option><option value="female" {{ old('gender', $customer->gender) === 'female' ? 'selected' : '' }}>Perempuan</option></select></div>
                    <div class="col-md-3"><label class="form-label">Tgl Lahir</label><input type="date" name="birth_date" class="form-control" value="{{ old('birth_date', $customer->birth_date?->format('Y-m-d')) }}"></div>
                    <div class="col-md-3"><label class="form-label">Membership</label><select name="membership_id" class="form-select"><option value="">-</option>@foreach($memberships as $m)<option value="{{ $m->id }}" {{ old('membership_id', $customer->membership_id) == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>@endforeach</select></div>
                    <div class="col-md-3"><label class="form-label">Kota</label><input type="text" name="city" class="form-control" value="{{ old('city', $customer->city) }}"></div>
                    <div class="col-12"><label class="form-label">Alamat</label><textarea name="address" class="form-control" rows="2">{{ old('address', $customer->address) }}</textarea></div>
                    <div class="col-12"><div class="form-check"><input type="checkbox" name="is_active" value="1" class="form-check-input" id="isActive" {{ old('is_active', $customer->is_active ?? true) ? 'checked' : '' }}><label class="form-check-label" for="isActive">Aktif</label></div></div>
                </div>
            </div>
            <div class="card-footer bg-white d-flex justify-content-between">
                <a href="{{ route('customers.index') }}" class="btn btn-secondary">Batal</a>
                <button class="btn btn-primary" type="submit"><i class="fas fa-save me-1"></i>Simpan</button>
            </div>
        </div>
    </form>
</div>
@endsection
