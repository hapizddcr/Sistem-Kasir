@extends('layouts.app')
@section('title', 'Satuan')
@section('content')
<div class="container-fluid" x-data="{ editing: null }">
    <div class="d-flex justify-content-between mb-3">
        <h3><i class="fas fa-ruler me-2 text-primary"></i>Satuan</h3>
        <button class="btn btn-primary" @click="editing = 'new'"><i class="fas fa-plus me-1"></i>Tambah</button>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr><th>Nama</th><th>Singkatan</th><th>Deskripsi</th><th>Status</th><th class="text-end">Aksi</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($units as $u)
                        <tr>
                            <td>{{ $u->name }}</td>
                            <td><code>{{ $u->short_name }}</code></td>
                            <td>{{ $u->description }}</td>
                            <td><span class="badge bg-{{ $u->is_active ? 'success' : 'secondary' }}">{{ $u->is_active ? 'Aktif' : 'Non' }}</span></td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-warning" @click='editing = @json($u)'><i class="fas fa-edit"></i></button>
                                <form method="POST" action="{{ route('master.units.destroy', $u) }}" class="d-inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $units->links() }}
        </div>
    </div>
    <div class="modal fade show" :class="{ 'd-block': editing !== null }" tabindex="-1" x-show="editing !== null" x-cloak style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog">
            <div class="modal-content">
                <form :action="editing === 'new' ? '{{ route('master.units.store') }}' : '{{ url('master/units') }}/' + (editing?.id || '')" method="POST">
                    @csrf
                    <template x-if="editing !== 'new' && editing"><input type="hidden" name="_method" value="PUT"></template>
                    <div class="modal-header"><h5 class="modal-title" x-text="editing === 'new' ? 'Tambah' : 'Edit'"></h5><button type="button" class="btn-close" @click="editing = null"></button></div>
                    <div class="modal-body">
                        <div class="mb-2"><label class="form-label">Nama <span class="text-danger">*</span></label><input type="text" name="name" class="form-control" :value="editing?.name || ''" required></div>
                        <div class="mb-2"><label class="form-label">Singkatan <span class="text-danger">*</span></label><input type="text" name="short_name" class="form-control" :value="editing?.short_name || ''" required></div>
                        <div class="mb-2"><label class="form-label">Deskripsi</label><textarea name="description" class="form-control" x-text="editing?.description || ''"></textarea></div>
                        <div class="form-check"><input type="checkbox" name="is_active" value="1" class="form-check-input" id="uActive" :checked="editing?.is_active ?? true"><label class="form-check-label" for="uActive">Aktif</label></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" @click="editing = null">Batal</button><button class="btn btn-primary" type="submit">Simpan</button></div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
