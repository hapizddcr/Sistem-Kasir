@extends('layouts.app')
@section('title', 'Tambah/Edit Supplier')
@section('content')
<div class="container-fluid">
    <h3 class="mb-3"><i class="fas fa-{{ isset($supplier) && $supplier->exists ? 'edit' : 'plus' }} me-2 text-primary"></i>{{ isset($supplier) && $supplier->exists ? 'Edit' : 'Tambah' }} Supplier</h3>
    <form method="POST" action="{{ isset($supplier) && $supplier->exists ? route('suppliers.update', $supplier) : route('suppliers.store') }}">
        @csrf
        @if (isset($supplier) && $supplier->exists) @method('PUT') @endif
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3"><label class="form-label">Kode <span class="text-danger">*</span></label><input type="text" name="code" class="form-control" value="{{ old('code', $supplier->code ?? '') }}" required></div>
                    <div class="col-md-9"><label class="form-label">Nama <span class="text-danger">*</span></label><input type="text" name="name" class="form-control" value="{{ old('name', $supplier->name ?? '') }}" required></div>
                    <div class="col-md-4"><label class="form-label">Contact Person</label><input type="text" name="contact_person" class="form-control" value="{{ old('contact_person', $supplier->contact_person ?? '') }}"></div>
                    <div class="col-md-4"><label class="form-label">HP</label><input type="text" name="phone" class="form-control" value="{{ old('phone', $supplier->phone ?? '') }}"></div>
                    <div class="col-md-4"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ old('email', $supplier->email ?? '') }}"></div>
                    <div class="col-12"><label class="form-label">Alamat</label><textarea name="address" class="form-control" rows="2">{{ old('address', $supplier->address ?? '') }}</textarea></div>
                    <div class="col-md-4"><label class="form-label">Kota</label><input type="text" name="city" class="form-control" value="{{ old('city', $supplier->city ?? '') }}"></div>
                    <div class="col-md-4"><label class="form-label">Provinsi</label><input type="text" name="province" class="form-control" value="{{ old('province', $supplier->province ?? '') }}"></div>
                    <div class="col-md-2"><label class="form-label">Kode Pos</label><input type="text" name="postal_code" class="form-control" value="{{ old('postal_code', $supplier->postal_code ?? '') }}"></div>
                    <div class="col-md-2"><label class="form-label">NPWP</label><input type="text" name="npwp" class="form-control" value="{{ old('npwp', $supplier->npwp ?? '') }}"></div>
                    <div class="col-12"><label class="form-label">Catatan</label><textarea name="notes" class="form-control" rows="2">{{ old('notes', $supplier->notes ?? '') }}</textarea></div>
                    <div class="col-12"><div class="form-check"><input type="checkbox" name="is_active" value="1" class="form-check-input" id="isActive" {{ old('is_active', $supplier->is_active ?? true) ? 'checked' : '' }}><label class="form-check-label" for="isActive">Aktif</label></div></div>
                </div>
            </div>
            <div class="card-footer bg-white d-flex justify-content-between">
                <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Batal</a>
                <button class="btn btn-primary" type="submit"><i class="fas fa-save me-1"></i>Simpan</button>
            </div>
        </div>
    </form>
</div>
@endsection
