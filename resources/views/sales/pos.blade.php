@extends('layouts.app')
@section('title', 'POS')

@section('content')
<div class="container-fluid" x-data="posApp()" x-init="init()">
    <div class="row g-3">
        <!-- Product Search & List -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control form-control-lg" x-model="search" @input.debounce.300ms="searchProducts()" placeholder="Cari produk (nama/SKU/barcode)..." autofocus>
                        <button class="btn btn-outline-secondary" @click="search = ''; searchProducts()"><i class="fas fa-times"></i></button>
                    </div>

                    <div class="row g-2" style="max-height: 70vh; overflow-y: auto;">
                        <template x-for="product in products" :key="product.id">
                            <div class="col-md-4 col-6">
                                <div class="card product-card h-100" :class="{'border-success': product.stock > 0, 'opacity-50': product.stock === 0}" @click="addToCart(product)" style="cursor: pointer;">
                                    <div class="card-body p-2 text-center">
                                        <div class="mb-1" style="height: 60px; display: flex; align-items: center; justify-content: center; background: #f1f5f9; border-radius: 0.25rem;">
                                            <i class="fas fa-box fa-2x text-secondary opacity-50"></i>
                                        </div>
                                        <small class="fw-bold d-block text-truncate" x-text="product.name"></small>
                                        <small class="text-muted d-block" x-text="product.sku"></small>
                                        <div class="text-success fw-bold text-money" x-text="formatRupiah(product.selling_price)"></div>
                                        <small class="badge" :class="product.stock > 0 ? 'bg-success' : 'bg-danger'" x-text="'Stok: ' + product.stock"></small>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <div x-show="products.length === 0" class="col-12 text-center text-muted py-4">
                            <i class="fas fa-search fa-2x mb-2"></i><br>Tidak ada produk
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cart -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Keranjang</h5>
                        <span class="badge bg-light text-dark" x-text="cart.length + ' item'"></span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <!-- Customer -->
                    <div class="p-3 border-bottom">
                        <label class="form-label small">Pelanggan</label>
                        <select class="form-select" x-model="customerId">
                            <option value="">Umum (Tanpa Member)</option>
                            @foreach ($customers as $c)
                                <option value="{{ $c->id }}">{{ $c->code }} - {{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Cart Items -->
                    <div style="max-height: 35vh; overflow-y: auto;">
                        <template x-for="(item, idx) in cart" :key="item.id">
                            <div class="p-2 border-bottom">
                                <div class="d-flex justify-content-between">
                                    <div class="flex-grow-1">
                                        <small class="fw-bold d-block" x-text="item.name"></small>
                                        <small class="text-muted" x-text="formatRupiah(item.unit_price)"></small>
                                    </div>
                                    <button class="btn btn-sm text-danger" @click="removeFromCart(idx)"><i class="fas fa-trash"></i></button>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-1">
                                    <div class="input-group input-group-sm" style="width: 110px;">
                                        <button class="btn btn-outline-secondary" @click="updateQty(idx, -1)">-</button>
                                        <input type="number" class="form-control text-center" x-model.number="item.quantity" @change="recalculate()">
                                        <button class="btn btn-outline-secondary" @click="updateQty(idx, 1)">+</button>
                                    </div>
                                    <div class="text-money fw-bold" x-text="formatRupiah(item.subtotal)"></div>
                                </div>
                            </div>
                        </template>
                        <div x-show="cart.length === 0" class="text-center text-muted py-4">
                            <i class="fas fa-shopping-cart fa-2x mb-2 opacity-50"></i><br>
                            Keranjang kosong
                        </div>
                    </div>

                    <!-- Totals -->
                    <div class="p-3 bg-light">
                        <div class="d-flex justify-content-between"><span>Subtotal</span><span class="text-money" x-text="formatRupiah(totals.subtotal)"></span></div>
                        <div class="d-flex justify-content-between text-danger"><span>Diskon</span><input type="number" class="form-control form-control-sm text-end" style="width: 120px;" x-model.number="globalDiscount" @input="recalculate()"></span></div>
                        <div class="d-flex justify-content-between"><span>Pajak (11%)</span><span class="text-money" x-text="formatRupiah(totals.tax)"></span></div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between fs-5 fw-bold"><span>TOTAL</span><span class="text-money text-success" x-text="formatRupiah(totals.total)"></span></div>
                    </div>

                    <!-- Payment -->
                    <div class="p-3">
                        <label class="form-label small">Metode Pembayaran</label>
                        <div class="btn-group w-100 mb-2" role="group">
                            <input type="radio" class="btn-check" name="payment" id="cash" value="cash" x-model="paymentMethod">
                            <label class="btn btn-outline-primary btn-sm" for="cash"><i class="fas fa-money-bill"></i> Tunai</label>
                            <input type="radio" class="btn-check" name="payment" id="qris" value="qris" x-model="paymentMethod">
                            <label class="btn btn-outline-primary btn-sm" for="qris"><i class="fas fa-qrcode"></i> QRIS</label>
                            <input type="radio" class="btn-check" name="payment" id="transfer" value="transfer" x-model="paymentMethod">
                            <label class="btn btn-outline-primary btn-sm" for="transfer"><i class="fas fa-university"></i> Transfer</label>
                            <input type="radio" class="btn-check" name="payment" id="card" value="card" x-model="paymentMethod">
                            <label class="btn btn-outline-primary btn-sm" for="card"><i class="fas fa-credit-card"></i> Kartu</label>
                        </div>
                        <div x-show="paymentMethod === 'cash'">
                            <label class="form-label small">Tunai Diterima</label>
                            <input type="number" class="form-control form-control-lg text-money" x-model.number="cashReceived" @input="cashReceived = Math.max(0, cashReceived)">
                            <div class="mt-2 d-flex justify-content-between">
                                <span>Kembali:</span>
                                <span class="text-money fw-bold" :class="change < 0 ? 'text-danger' : 'text-success'" x-text="formatRupiah(Math.max(0, change))"></span>
                            </div>
                            <div class="btn-group btn-group-sm w-100 mt-2">
                                <template x-for="amount in quickAmounts" :key="amount">
                                    <button class="btn btn-outline-secondary" @click="cashReceived = amount" x-text="formatRupiah(amount)"></button>
                                </template>
                            </div>
                        </div>
                        <div x-show="paymentMethod !== 'cash'">
                            <label class="form-label small">Referensi (opsional)</label>
                            <input type="text" class="form-control" x-model="paymentRef" placeholder="No. referensi / struk">
                        </div>
                    </div>

                    <div class="card-footer p-3">
                        <button class="btn btn-success btn-lg w-100" @click="checkout()" :disabled="cart.length === 0 || (paymentMethod === 'cash' && cashReceived < totals.total) || processing">
                            <span x-show="!processing"><i class="fas fa-check-circle me-1"></i>BAYAR & CETAK</span>
                            <span x-show="processing"><i class="fas fa-spinner fa-spin me-1"></i>Memproses...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function posApp() {
    return {
        search: '',
        products: @json($featuredProducts),
        cart: [],
        customerId: '',
        paymentMethod: 'cash',
        cashReceived: 0,
        paymentRef: '',
        globalDiscount: 0,
        processing: false,
        quickAmounts: [50000, 100000, 200000, 500000],
        totals: { subtotal: 0, discount: 0, tax: 0, total: 0 },

        async init() {
            await this.loadAll();
        },

        async loadAll() {
            const res = await fetch('{{ route("products.search") }}?q=');
            const data = await res.json();
            this.products = data.data || [];
        },

        async searchProducts() {
            const res = await fetch('{{ route("products.search") }}?q=' + encodeURIComponent(this.search));
            const data = await res.json();
            this.products = data.data || [];
        },

        addToCart(product) {
            if (product.stock === 0) { alert('Stok habis!'); return; }
            const existing = this.cart.find(i => i.id === product.id);
            if (existing) {
                if (existing.quantity + 1 > product.stock) { alert('Stok tidak cukup!'); return; }
                existing.quantity++;
            } else {
                this.cart.push({
                    id: product.id,
                    name: product.name,
                    sku: product.sku,
                    unit_price: parseFloat(product.selling_price),
                    quantity: 1,
                    stock: product.stock,
                });
            }
            this.recalculate();
        },

        removeFromCart(idx) {
            this.cart.splice(idx, 1);
            this.recalculate();
        },

        updateQty(idx, delta) {
            const item = this.cart[idx];
            const newQty = item.quantity + delta;
            if (newQty < 1) { this.removeFromCart(idx); return; }
            if (newQty > item.stock) { alert('Stok tidak cukup! Stok: ' + item.stock); return; }
            item.quantity = newQty;
            this.recalculate();
        },

        async recalculate() {
            if (this.cart.length === 0) {
                this.totals = { subtotal: 0, discount: 0, tax: 0, total: 0 };
                return;
            }
            const res = await fetch('{{ route("sales.preview") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' },
                body: JSON.stringify({
                    items: this.cart.map(i => ({ product_id: i.id, quantity: i.quantity, unit_price: i.unit_price })),
                    global_discount: this.globalDiscount,
                }),
            });
            const data = await res.json();
            this.totals = data;
        },

        get change() {
            return this.cashReceived - this.totals.total;
        },

        formatRupiah(n) {
            return 'Rp ' + (n || 0).toLocaleString('id-ID');
        },

        async checkout() {
            if (!confirm('Konfirmasi pembayaran ' + this.formatRupiah(this.totals.total) + '?')) return;
            this.processing = true;
            try {
                const res = await fetch('{{ route("sales.store") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' },
                    body: JSON.stringify({
                        customer_id: this.customerId || null,
                        items: this.cart.map(i => ({ product_id: i.id, quantity: i.quantity, unit_price: i.unit_price })),
                        payments: [{ method: this.paymentMethod, amount: this.totals.total, reference: this.paymentRef }],
                    }),
                });
                const data = await res.json();
                if (data.success) {
                    window.open(data.redirect, '_blank');
                    this.cart = [];
                    this.cashReceived = 0;
                    this.globalDiscount = 0;
                    this.paymentRef = '';
                    this.totals = { subtotal: 0, discount: 0, tax: 0, total: 0 };
                    await this.loadAll();
                } else {
                    alert('Error: ' + (data.error || 'Unknown error'));
                }
            } catch (e) {
                alert('Error: ' + e.message);
            } finally {
                this.processing = false;
            }
        },
    };
}
</script>
<style>
.product-card { transition: all 0.15s; }
.product-card:hover { transform: translateY(-2px); box-shadow: 0 0.25rem 0.5rem rgba(0,0,0,0.1); }
</style>
@endpush
