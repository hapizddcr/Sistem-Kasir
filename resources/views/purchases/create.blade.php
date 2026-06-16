@extends('layouts.app')
@section('title', 'PO Baru')
@section('content')
<div class="container-fluid" x-data="purchaseForm()">
    <h3 class="mb-3"><i class="fas fa-truck me-2 text-primary"></i>PO Baru</h3>
    <form method="POST" action="{{ route('purchases.store') }}">
        @csrf
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-md-4">
                        <label class="form-label">Supplier <span class="text-danger">*</span></label>
                        <select name="supplier_id" class="form-select" required>
                            <option value="">Pilih</option>
                            @foreach ($suppliers as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3"><label class="form-label">Tanggal <span class="text-danger">*</span></label><input type="date" name="purchase_date" class="form-control" value="{{ date('Y-m-d') }}" required></div>
                    <div class="col-md-3"><label class="form-label">Expected</label><input type="date" name="expected_date" class="form-control"></div>
                    <div class="col-md-2"><label class="form-label">Ongkir</label><input type="number" name="shipping_cost" class="form-control text-money" x-model.number="shippingCost" min="0"></div>
                    <div class="col-md-2"><label class="form-label">Diskon</label><input type="number" name="discount" class="form-control text-money" x-model.number="discount" min="0"></div>
                    <div class="col-md-2"><label class="form-label">Pajak</label><input type="number" name="tax" class="form-control text-money" x-model.number="tax" min="0"></div>
                    <div class="col-md-8"><label class="form-label">Catatan</label><input type="text" name="notes" class="form-control"></div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between">
                <h5 class="mb-0">Item</h5>
                <button type="button" class="btn btn-sm btn-primary" @click="addItem()"><i class="fas fa-plus me-1"></i>Tambah</button>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr><th style="min-width: 280px;">Produk</th><th>Qty</th><th>Harga</th><th>Diskon</th><th>Subtotal</th><th></th></tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, idx) in items" :key="idx">
                            <tr>
                                <td>
                                    <select :name="'items['+idx+'][product_id]'" class="form-select" x-model="item.product_id" @change="updateProduct(idx)" required>
                                        <option value="">Pilih</option>
                                        @foreach ($products as $p)
                                        <option value="{{ $p->id }}" data-price="{{ $p->purchase_price }}" data-name="{{ $p->name }}">{{ $p->name }} ({{ $p->sku }})</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="number" :name="'items['+idx+'][quantity]'" class="form-control" x-model.number="item.quantity" min="1" required></td>
                                <td><input type="number" :name="'items['+idx+'][unit_price]'" class="form-control text-money" x-model.number="item.unit_price" min="0" required></td>
                                <td><input type="number" :name="'items['+idx+'][discount]'" class="form-control text-money" x-model.number="item.discount" min="0"></td>
                                <td class="text-money fw-bold" x-text="formatRupiah((item.quantity * item.unit_price) - item.discount)"></td>
                                <td><button type="button" class="btn btn-sm btn-danger" @click="items.splice(idx, 1)"><i class="fas fa-trash"></i></button></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white">
                <div class="d-flex justify-content-between">
                    <div><strong>Total: </strong><span class="text-money fw-bold fs-5" x-text="formatRupiah(grandTotal)"></span></div>
                    <button class="btn btn-primary" type="submit"><i class="fas fa-save me-1"></i>Simpan PO</button>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
function purchaseForm() {
    return {
        items: [{ product_id: '', quantity: 1, unit_price: 0, discount: 0 }],
        discount: 0, tax: 0, shippingCost: 0,
        addItem() { this.items.push({ product_id: '', quantity: 1, unit_price: 0, discount: 0 }); },
        updateProduct(idx) {
            const sel = document.querySelector(`[name="items[${idx}][product_id]"]`);
            const opt = sel.options[sel.selectedIndex];
            this.items[idx].unit_price = parseFloat(opt.getAttribute('data-price') || 0);
        },
        get subtotal() { return this.items.reduce((s, i) => s + (i.quantity * i.unit_price - i.discount), 0); },
        get grandTotal() { return this.subtotal - this.discount + this.tax + this.shippingCost; },
        formatRupiah(n) { return 'Rp ' + (n || 0).toLocaleString('id-ID'); }
    };
}
</script>
@endsection
