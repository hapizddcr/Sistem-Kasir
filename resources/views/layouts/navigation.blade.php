<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">
            <i class="fas fa-cash-register me-2"></i>{{ config('app.name') }}
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('sales.pos') ? 'active' : '' }}" href="{{ route('sales.pos') }}">
                        <i class="fas fa-cash-register me-1"></i> POS
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('sales.*') && !request()->routeIs('sales.pos') ? 'active' : '' }}" href="{{ route('sales.index') }}">
                        <i class="fas fa-receipt me-1"></i> Penjualan
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#">
                        <i class="fas fa-box me-1"></i> Produk
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('products.index') }}">Daftar Produk</a></li>
                        <li><a class="dropdown-item" href="{{ route('products.create') }}">Tambah Produk</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('master.categories') }}">Kategori</a></li>
                        <li><a class="dropdown-item" href="{{ route('master.units') }}">Satuan</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#">
                        <i class="fas fa-warehouse me-1"></i> Stok
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('stock.history') }}">Riwayat Stok</a></li>
                        <li><a class="dropdown-item" href="{{ route('stock.low') }}">Stok Menipis</a></li>
                        <li><a class="dropdown-item" href="{{ route('stock.adjustments') }}">Penyesuaian</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('purchases.*') ? 'active' : '' }}" href="{{ route('purchases.index') }}">
                        <i class="fas fa-truck me-1"></i> Pembelian
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}" href="{{ route('suppliers.index') }}">
                        <i class="fas fa-handshake me-1"></i> Supplier
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index') }}">
                        <i class="fas fa-users me-1"></i> Pelanggan
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#">
                        <i class="fas fa-chart-bar me-1"></i> Laporan
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('reports.daily') }}">Penjualan Harian</a></li>
                        <li><a class="dropdown-item" href="{{ route('reports.monthly') }}">Penjualan Bulanan</a></li>
                        <li><a class="dropdown-item" href="{{ route('reports.top-products') }}">Produk Terlaris</a></li>
                        <li><a class="dropdown-item" href="{{ route('reports.profit-loss') }}">Laba Rugi</a></li>
                        <li><a class="dropdown-item" href="{{ route('reports.stock') }}">Stok Barang</a></li>
                        <li><a class="dropdown-item" href="{{ route('reports.by-category') }}">Per Kategori</a></li>
                    </ul>
                </li>
                @role('admin')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                        <i class="fas fa-user-shield me-1"></i> User
                    </a>
                </li>
                @endrole
            </ul>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#">
                        <i class="fas fa-user-circle me-1"></i> {{ auth()->user()->name }}
                        <span class="badge bg-warning text-dark ms-1">{{ auth()->user()->getRoleNames()->first() }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="dropdown-item" type="submit">Logout</button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

@if (session('success'))
<div class="container-fluid mt-3">
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
@endif

@if (session('error'))
<div class="container-fluid mt-3">
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
@endif
