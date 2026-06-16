# Sistem Kasir Online (POS)

Aplikasi Point of Sale / Kasir modern berbasis **Laravel 12** dengan arsitektur Service Layer + Repository Pattern. Dirancang untuk UMKM hingga retail menengah di Indonesia.

## ✨ Fitur Utama

### 🛒 Point of Sale (POS)
- Interface kasir responsif dengan cart real-time
- Barcode scanner support
- Multi metode pembayaran (Tunai, QRIS, Transfer Bank)
- Diskon per item / global
- Cetak struk thermal 80mm
- Hold transaction (tahan transaksi)

### 📦 Manajemen Produk
- CRUD produk dengan SKU, barcode, gambar
- Multi kategori & satuan
- Harga modal, jual, grosir
- Manajemen stok minimum (alert)
- Soft delete + activity log
- Image upload dengan preview

### 📊 Manajemen Stok
- **Immutable stock ledger** (audit trail lengkap)
- Barang masuk (dari pembelian)
- Barang keluar (manual / opname)
- Stock opname dengan adjustment
- History pergerakan per produk
- Alert stok minimum

### 👥 Pelanggan & Membership
- Database pelanggan dengan kode otomatis
- Tier membership (Bronze/Silver/Gold/Platinum)
- Poin reward otomatis
- Auto-upgrade tier berdasarkan total belanja
- Diskon member khusus

### 🏭 Supplier & Pembelian
- CRUD supplier
- Purchase Order workflow (draft → ordered → received → cancelled)
- Auto-update stok saat barang diterima
- Retur pembelian
- Tracking status PO

### 💰 Penjualan
- Riwayat transaksi lengkap
- Filter tanggal / status / customer
- Detail sale + payment breakdown
- Retur penjualan
- Struk cetak

### 📈 Laporan
- Laporan harian (omzet, transaksi, item terlaris)
- Laporan bulanan dengan chart tren
- Laporan produk terlaris
- Laporan laba-rugi (revenue - HPP)
- Laporan stok (current value, slow moving)
- Export CSV untuk semua laporan

### 🔐 Security & RBAC
- **4 roles**: Admin, Manager, Cashier, Inventory Staff
- **30+ permissions** granular per modul
- CSRF, XSS, SQL injection protection
- Password hashing (bcrypt)
- Activity log untuk audit trail
- Policy-based authorization

## 🛠️ Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 12.x |
| Language | PHP 8.3+ (tested on 8.5) |
| Database | MySQL 8.0+ / MariaDB 10.6+ |
| Frontend | Bootstrap 5.3, Alpine.js, FontAwesome 6 |
| Auth | Laravel Breeze + Spatie Permission |
| Barcode | Picqer PHP Barcode Generator |
| PDF | DomPDF (untuk laporan) |
| Testing | PHPUnit 11 |

## 📋 Requirements

- PHP 8.3 atau lebih tinggi
- Composer 2.x
- MySQL 8.0+ atau MariaDB 10.6+
- Node.js 18+ & NPM
- Extension PHP: `pdo_mysql`, `mbstring`, `gd`, `zip`, `intl`

## 🚀 Instalasi

### 1. Clone & Install Dependencies

```bash
git clone https://github.com/hapizddcr/Sistem-Kasir.git
cd Sistem-Kasir
composer install
npm install
```

### 2. Konfigurasi Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` dan sesuaikan:

```env
APP_NAME="Sistem Kasir"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kasir_db
DB_USERNAME=kasir
DB_PASSWORD=your_secure_password

# POS Settings
POS_TAX_PERCENT=11
POS_CURRENCY=IDR
```

### 3. Buat Database

```bash
mysql -u root -p
CREATE DATABASE kasir_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'kasir'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON kasir_db.* TO 'kasir'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 4. Migrate & Seed

```bash
php artisan migrate
php artisan db:seed
php artisan storage:link
```

Seeder akan membuat:

**Roles**: `admin`, `manager`, `cashier`, `inventory`
**Permissions**: 30+ permissions
**Master Data**: 8 satuan, 8 kategori, 4 membership tiers
**Demo Data**: 4 supplier, 20 produk, 8 kategori

### 5. Build Assets & Run

```bash
npm run build
php artisan serve
```

Buka `http://localhost:8000`

## 👤 Akun Demo

| Email | Password | Role |
|-------|----------|------|
| `admin@kasir.local` | `password` | Admin (full access) |
| `manager@kasir.local` | `password` | Manager (laporan + approval) |
| `kasir@kasir.local` | `password` | Cashier (POS + penjualan) |
| `gudang@kasir.local` | `password` | Inventory (stok + produk) |

⚠️ **Ganti password setelah login pertama kali di production!**

## 🧪 Testing

```bash
# Run all tests
php artisan test

# With coverage
php artisan test --coverage

# Specific suite
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature
```

**Test Coverage**:
- ✅ Stock service (immutable ledger math)
- ✅ Sale transaction flow (POS → stock deduction → invoice)
- ✅ Product CRUD dengan authorization
- ✅ Authentication & profile

## 📂 Arsitektur

```
app/
├── Http/
│   ├── Controllers/        # Thin controllers (delegasi ke Service)
│   ├── Requests/           # Form Request Validation
│   ├── Middleware/         # CheckRole, EnsureUserHasPermission
│   └── Resources/          # API Resources (optional)
├── Models/                 # Eloquent models dengan relasi
├── Services/               # Business logic layer
│   ├── StockService        # Immutable stock ledger
│   ├── SalesService        # POS transaction logic
│   ├── PurchaseService     # PO + auto-stock
│   ├── ProductService      # CRUD + barcode
│   ├── CustomerService     # Membership auto-upgrade
│   └── ReportService       # Analytics + CSV export
├── Policies/               # Authorization policies
└── Observers/              # Model observers
```

### Design Patterns

- **Service Layer**: Semua business logic di Services, Controller hanya orchestrate
- **Repository Pattern**: Query kompleks (reports) diisolasi
- **Form Request Validation**: Validation logic terpisah dari controller
- **Policy & Gate**: Authorization berbasis policy class
- **Immutable Stock Ledger**: Stok tidak pernah di-overwrite, selalu append movement
- **Activity Log**: Spatie ActivityLog untuk audit trail sensitif

## 🔒 Security

- ✅ **CSRF Protection**: otomatis via Laravel untuk semua form
- ✅ **XSS Prevention**: Blade `{{ }}` auto-escape output
- ✅ **SQL Injection**: Eloquent + Query Builder (no raw SQL di production)
- ✅ **Password Hashing**: bcrypt dengan cost 12
- ✅ **RBAC**: Spatie Permission dengan middleware
- ✅ **Audit Log**: Spatie ActivityLog untuk transaksi & perubahan data
- ✅ **Mass Assignment Protection**: `$fillable` di setiap model
- ✅ **Rate Limiting**: 60 req/menit untuk login

## 🌍 Localization

Aplikasi mendukung Bahasa Indonesia (default) & English.
File bahasa di `lang/id/` dan `lang/en/`.
Ganti bahasa di `.env`: `APP_LOCALE=id`

## 📝 License

MIT License - bebas digunakan untuk komersial & non-komersial.

## 🤝 Contributing

Pull request welcome! Untuk perubahan besar, buka issue dulu untuk diskusi.

## 📧 Support

- Issues: [GitHub Issues](https://github.com/hapizddcr/Sistem-Kasir/issues)
- Email: hapizddcr@gmail.com

---

**Dibuat dengan ❤️ untuk UMKM Indonesia**
