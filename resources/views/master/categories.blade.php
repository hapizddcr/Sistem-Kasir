@extends('layouts.app')
@section('title', 'Kategori')
@section('content')
<div class="container-fluid" x-data="{ editing: null }">
    <div class="d-flex justify-content-between mb-3">
        <h3><i class="fas fa-tags me-2 text-primary"></i>Kategori</h3>
        <button class="btn btn-primary" @click="editing = 'new'"><i class="fas fa-plus me-1"></i>Tambah</button>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr><th>Nama</th><th>Slug</th><th>Icon</th><th>Produk</th><th>Status</th><th class="text-end">Aksi</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $c)
                        <tr>
                            <td><i class="fas {{ $c->icon ?? 'fa-tag' }} me-2 text-primary"></i>{{ $c->name }}</td>
                            <td><code>{{ $c->slug }}</code></td>
                            <td><code>{{ $c->icon }}</code></td>
                            <td>{{ $c->products->count() ?? $c->products()->count() }}</td>
                            <td><span class="badge bg-{{ $c->is_active ? 'success' : 'secondary' }}">{{ $c->is_active ? 'Aktif' : 'Non' }}</span></td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-warning" @click='editing = @json($c)'><i class="fas fa-edit"></i></button>
                                <form method="POST" action="{{ route('master.categories.destroy', $c) }}" class="d-inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $categories->links() }}
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade show" :class="{ 'd-block': editing !== null }" tabindex="-1" x-show="editing !== null" x-cloak style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog">
            <div class="modal-content">
                <form :action="editing === 'new' ? '{{ route('master.categories.store') }}' : '{{ url('master/categories') }}/' + (editing?.id || '')" method="POST">
                    @csrf
                    <template x-if="editing !== 'new' && editing"><input type="hidden" name="_method" value="PUT"></template>
                    <div class="modal-header">
                        <h5 class="modal-title" x-text="editing === 'new' ? 'Tambah' : 'Edit'"></h5>
                        <button type="button" class="btn-close" @click="editing = null"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-2"><label class="form-label">Nama <span class="text-danger">*</span></label><input type="text" name="name" class="form-control" :value="editing?.name || ''" required></div>
                        <div class="mb-2"><label class="form-label">Icon (FontAwesome class)</label><input type="text" name="icon" class="form-control" :value="editing?.icon || ''" placeholder="fa-tag"></div>
                        <div class="mb-2"><label class="form-label">Deskripsi</label><textarea name="description" class="form-control" x-text="editing?.description || ''"></textarea></div>
                        <div class="form-check"><input type="checkbox" name="is_active" value="1" class="form-check-input" id="catActive" :checked="editing?.is_active ?? true"><label class="form-check-label" for="catActive">Aktif</label></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="editing = null">Batal</button>
                        <button class="btn btn-primary" type="submit">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
