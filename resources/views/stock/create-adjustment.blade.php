@extends('layouts.app')
@section('title', 'Buat Penyesuaian Stok')
@section('content')
<div class="container-fluid" x-data="stockAdjustment()">
    <h3 class="mb-3"><i class="fas fa-adjust me-2 text-primary"></i>Buat Penyesuaian Stok</h3>
    <form method="POST" action="{{ route('stock.store-adjustment') }}">
        @csrf
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-md-3">
                        <label class="form-label">Alasan</label>
                        <select name="reason" class="form-select" required>
                            <option value="opname">Stock Opname</option>
                            <option value="damage">Barang Rusak</option>
                            <option value="expired">Kedaluwarsa</option>
                            <option value="lost">Hilang</option>
                            <option value="found">Ditemukan</option>
                            <option value="correction">Koreksi</option>
                            <option value="other">Lainnya</option>
                        </select>
                    </div>
                    <div class="col-md-9">
                        <label class="form-label">Catatan</label>
                        <input type="text" name="notes" class="form-control">
                    </div>
                </div>
            </div>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between">
                <h5 class="mb-0">Item Penyesuaian</h5>
                <button type="button" class="btn btn-sm btn-primary" @click="addItem()"><i class="fas fa-plus me-1"></i>Tambah Item</button>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr><th style="min-width: 300px;">Produk</th><th>Stok Sistem</th><th>Stok Aktual</th><th>Selisih</th><th></th></tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, idx) in items" :key="idx">
                            <tr>
                                <td>
                                    <select :name="'items['+idx+'][product_id]'" class="form-select" x-model="item.product_id" @change="updateProduct(idx)" required>
                                        <option value="">Pilih produk</option>
                                        @foreach ($products as $p)
                                        <option value="{{ $p->id }}" data-stock="{{ $p->stock }}" data-name="{{ $p->name }}">{{ $p->name }} ({{ $p->sku }}) — Stok: {{ $p->stock }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><span class="text-money" x-text="item.system_stock"></span></td>
                                <td><input type="number" :name="'items['+idx+'][actual_stock]'" class="form-control" x-model.number="item.actual_stock" min="0" required></td>
                                <td><span class="fw-bold" :class="(item.actual_stock - item.system_stock) === 0 ? '' : (item.actual_stock > item.system_stock ? 'text-success' : 'text-danger')" x-text="(item.actual_stock - item.system_stock)"></span></td>
                                <td><button type="button" class="btn btn-sm btn-danger" @click="items.splice(idx, 1)"><i class="fas fa-trash"></i></button></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white">
                <button class="btn btn-primary" type="submit"><i class="fas fa-save me-1"></i>Simpan Penyesuaian</button>
            </div>
        </div>
    </form>
</div>
<script>
function stockAdjustment() {
    return {
        items: [{ product_id: '', system_stock: 0, actual_stock: 0 }],
        addItem() { this.items.push({ product_id: '', system_stock: 0, actual_stock: 0 }); },
        updateProduct(idx) {
            const sel = document.querySelector(`[name="items[${idx}][product_id]"]`);
            const opt = sel.options[sel.selectedIndex];
            this.items[idx].system_stock = parseInt(opt.getAttribute('data-stock') || 0);
            this.items[idx].actual_stock = this.items[idx].system_stock;
        }
    };
}
</script>
@endsection
