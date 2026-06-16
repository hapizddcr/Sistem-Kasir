@extends('layouts.app')
@section('title', 'Membership')
@section('content')
<div class="container-fluid" x-data="{ editing: null }">
    <div class="d-flex justify-content-between mb-3">
        <h3><i class="fas fa-crown me-2 text-primary"></i>Membership</h3>
        <button class="btn btn-primary" @click="editing = 'new'"><i class="fas fa-plus me-1"></i>Tambah</button>
    </div>
    <div class="row g-3 mb-3">
        @foreach ($memberships as $m)
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border-left: 4px solid {{ $m->color }} !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h5 style="color: {{ $m->color }}">{{ $m->name }}</h5>
                        <div>
                            <button class="btn btn-sm btn-link" @click='editing = @json($m)'><i class="fas fa-edit"></i></button>
                            <form method="POST" action="{{ route('master.memberships.destroy', $m) }}" class="d-inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="btn btn-sm btn-link text-danger"><i class="fas fa-trash"></i></button></form>
                        </div>
                    </div>
                    <p class="text-muted small mb-2">{{ $m->description }}</p>
                    <div><small>Min Belanja:</small> <strong class="text-money">Rp {{ number_format($m->min_spent, 0, ',', '.') }}</strong></div>
                    <div><small>Diskon:</small> <strong>{{ $m->discount_percent }}%</strong></div>
                    <div><small>Multiplier Poin:</small> <strong>{{ $m->points_multiplier }}x</strong></div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="modal fade show" :class="{ 'd-block': editing !== null }" tabindex="-1" x-show="editing !== null" x-cloak style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog">
            <div class="modal-content">
                <form :action="editing === 'new' ? '{{ route('master.memberships.store') }}' : '{{ url('master/memberships') }}/' + (editing?.id || '')" method="POST">
                    @csrf
                    <template x-if="editing !== 'new' && editing"><input type="hidden" name="_method" value="PUT"></template>
                    <div class="modal-header"><h5 class="modal-title" x-text="editing === 'new' ? 'Tambah' : 'Edit'"></h5><button type="button" class="btn-close" @click="editing = null"></button></div>
                    <div class="modal-body">
                        <div class="mb-2"><label class="form-label">Nama <span class="text-danger">*</span></label><input type="text" name="name" class="form-control" :value="editing?.name || ''" required></div>
                        <div class="row g-2">
                            <div class="col-6"><label class="form-label">Min Belanja</label><input type="number" name="min_spent" class="form-control" :value="editing?.min_spent || 0" min="0"></div>
                            <div class="col-6"><label class="form-label">Diskon %</label><input type="number" name="discount_percent" class="form-control" :value="editing?.discount_percent || 0" min="0" max="100" step="0.1"></div>
                            <div class="col-6"><label class="form-label">Multiplier Poin</label><input type="number" name="points_multiplier" class="form-control" :value="editing?.points_multiplier || 1" min="1"></div>
                            <div class="col-6"><label class="form-label">Warna</label><input type="color" name="color" class="form-control" :value="editing?.color || '#6366f1'"></div>
                        </div>
                        <div class="mb-2 mt-2"><label class="form-label">Deskripsi</label><textarea name="description" class="form-control" x-text="editing?.description || ''"></textarea></div>
                        <div class="form-check"><input type="checkbox" name="is_active" value="1" class="form-check-input" id="mActive" :checked="editing?.is_active ?? true"><label class="form-check-label" for="mActive">Aktif</label></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" @click="editing = null">Batal</button><button class="btn btn-primary" type="submit">Simpan</button></div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
