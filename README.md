# SIMRS Medika (Sistem Informasi Manajemen Rumah Sakit) - MVP

[![Laravel 12](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP 8.2+](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4.0-06B6D4?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![Alpine.js](https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=for-the-badge&logo=alpinedotjs&logoColor=white)](https://alpinejs.dev)
[![Tests Passing](https://img.shields.io/badge/Tests-14%20Passed%20(77%20Assertions)-emerald?style=for-the-badge)](tests/)

Aplikasi **Sistem Informasi Manajemen Rumah Sakit (SIMRS) MVP** skala enterprise yang dirancang khusus sebagai proyek portofolio berstandar industri (*healthcare grade*). Sistem ini mengimplementasikan alur operasional esensial rumah sakit dari pendaftaran pasien, rekam medis elektronik oleh dokter, dispensing farmasi, hingga pembayaran kasir tunai.

---

## 🌟 Fitur Utama & Alur Kerja Bisnis (Core Workflow)

```
[ Front Office / Admin ]        [ Dokter Spesialis ]          [ Farmasi / Apotek ]         [ Kasir / Billing ]
  Registrasi Pasien Baru          Panggilan Pasien              Antrean Resep Masuk          Kalkulasi Total Tagihan
            ↓                            ↓                              ↓                             ↓
  Pendaftaran Antrean Poli  →     Pemeriksaan RME &        →    Validasi & Pemotongan   →    Pembayaran Tunai (Cash)
  (Nomor RM & Antrean)           Peresepan Elektronik           Stok Obat Atomik             Kalkulator Kembalian
                                  (ICD-10 & Tanda Vital)        (Pessimistic Locking)        Cetak Bukti Kwitansi (PDF)
```

1. **Front Office / Admin**:
   - Registrasi Pasien Baru dengan nomor Rekam Medis unik seumur hidup format otomatis (`RM-YYYYMM-XXXX`) dan validasi NIK 16 digit.
   - Pendaftaran kunjungan pasien ke Poliklinik tujuan dan alokasi nomor antrean harian terurut.
2. **Dokter Spesialis (RME / EMR)**:
   - Dashboard antrean poliklinik hari ini.
   - Pemeriksaan rekam medis elektronik: pencatatan tanda vital (tensi, nadi, suhu), anamnesis keluhan utama, pemeriksaan fisik, serta kode diagnosa standar **ICD-10**.
   - *Builder E-Resep Dinamis*: peresepan obat multi-item interaktif berbasis **Alpine.js**.
   - *Medical Record Immutability*: status RME terkunci permanen (`finalized_at`) setelah dokter menyelesaikan konsultasi.
3. **Instalasi Farmasi**:
   - Monitoring antrean resep masuk secara real-time.
   - Validasi resep dan penyerahan obat (*dispensing*).
   - Pengurangan stok fisik obat menggunakan transaksi ACID dan **Pessimistic Locking** (`lockForUpdate`) guna mencegah *race condition* stok minus.
   - Manajemen inventori obat dan peringatan dini stok menipis (< 50 unit).
4. **Kasir & Billing**:
   - Agregasi otomatis tagihan medis (Biaya Konsultasi Dokter + Total Biaya Obat Resep).
   - Kalkulator pembayaran tunai instan (*change calculator*).
   - Pencetakan Kwitansi / Struk Pembayaran Lunas resmi (*print-ready layout*).

---

## 🛡️ Keamanan & Rekayasa Perangkat Lunak (Engineering Highlights)

* **Dual Authentication & Role-Based Access Control (RBAC)**:
  * Autentikasi ganda: Email/Password (Bcrypt) dan **Google OAuth 2.0** via Laravel Socialite.
  * **Enterprise Whitelist Policy**: Login Google hanya diizinkan untuk staf medis yang alamat emailnya telah didaftarkan sebelumnya oleh Administrator rumah sakit.
  * Proteksi route modular menggunakan `spatie/laravel-permission` (`role:admin`, `role:doctor`, `role:pharmacist`, `role:cashier`).
* **Concurrency Safety**:
  * Pengurangan stok obat dikawal oleh `DB::transaction` dan `Medicine::where('id', ...)->lockForUpdate()` dengan penanganan pengecualian kustom `InsufficientStockException`.
* **Kepatuhan Regulasi Rekam Medis (Permenkes No. 24/2022)**:
  * Pencatatan NIK 16 digit terindeks, Nomor RM unik permanen, format ICD-10, serta tanda tangan digital/audit trail penguncian rekam medis.

---

## 👥 Akun Demo Siap Pakai (Database Seeder)

Untuk keperluan evaluasi dan peninjauan cepat, sistem telah dilengkapi dengan kredensial demo untuk masing-masing peran:

| Peran (Role) | Alamat Email | Password | Modul Akses |
| :--- | :--- | :--- | :--- |
| **Admin / Front Office** | `admin@simrs.test` | `password` | Manajemen Pasien & Pendaftaran Poli |
| **Dokter Spesialis** | `dokter@simrs.test` | `password` | Antrean Poli, RME, & E-Resep |
| **Apoteker (Farmasi)** | `farmasi@simrs.test` | `password` | Validasi Resep, Dispensing & Stok Obat |
| **Kasir Billing** | `kasir@simrs.test` | `password` | Billing Tagihan, Bayar Tunai & Kwitansi |

> [!TIP]
> Pada halaman login (`/login`), tersedia panel **⚡ Quick Demo Login (1-Klik Isi Form)** untuk mempermudah evaluasi tanpa perlu mengetik manual.

---

## 🚀 Panduan Instalasi & Menjalankan Proyek

### Prasyarat
* PHP >= 8.2
* Composer >= 2.0
* Node.js >= 18 & NPM
* SQLite / MySQL / PostgreSQL (PostgreSQL 17 kompatibel)

### Langkah Setup
1. **Clone & Masuk Direktori**:
   ```bash
   cd D:\projek\SIMRS
   ```
2. **Install Dependensi PHP & Frontend**:
   ```bash
   composer install
   npm install
   npm run build
   ```
3. **Konfigurasi Environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4. **Migrasi Database & Seed Data Demo**:
   ```bash
   php artisan migrate:fresh --seed
   ```
5. **Jalankan Development Server**:
   ```bash
   php artisan serve
   ```
   Akses aplikasi di peramban Anda melalui: **`http://localhost:8000`**

---

## 🧪 Pengujian Otomatis (Automated Testing)

Aplikasi memiliki cakupan pengujian lengkap mencakup Unit Test (business logic & exceptions) dan Feature Test (HTTP endpoints & simulasi alur end-to-end):

```bash
php artisan test
```

### Hasil Test Suite Terkini:
```text
PASS  Tests\Unit\ExampleTest
✓ that true is true

PASS  Tests\Unit\SimrsBusinessLogicTest
✓ generate medical record number action creates sequential numbers
✓ dispense prescription action deducts stock and updates status
✓ dispense prescription action fails when stock is insufficient

PASS  Tests\Feature\AuthenticationTest
✓ login screen can be rendered
✓ users can authenticate using the login screen
✓ users can not authenticate with invalid password
✓ inactive users can not authenticate
✓ users can logout
✓ dashboard renders for all roles

PASS  Tests\Feature\ExampleTest
✓ the application returns a successful response

PASS  Tests\Feature\SimrsCoreArchitectureTest
✓ roles and demo users are correctly seeded
✓ end to end simrs workflow from registration to cashier

PASS  Tests\Feature\SimrsFullHttpWorkflowTest
✓ complete end to end hospital workflow via http

Tests:    14 passed (77 assertions)
Duration: 1.26s
```

---

## 📂 Struktur Direktori Utama

```
app/
├── Actions/
│   ├── Pharmacy/
│   │   └── DispensePrescriptionAction.php      # Pessimistic locking stok obat
│   └── Registration/
│       └── GenerateMedicalRecordNumberAction.php # Auto No. RM unik
├── Exceptions/
│   └── InsufficientStockException.php
├── Http/Controllers/
│   ├── Auth/
│   │   ├── AuthenticatedSessionController.php
│   │   ├── GoogleAuthController.php            # Socialite Whitelist Auth
│   │   └── PasswordResetController.php
│   ├── FrontOffice/
│   │   ├── PatientController.php
│   │   └── RegistrationController.php
│   ├── Doctor/
│   │   └── ConsultationController.php          # RME & Prescriptions
│   ├── Pharmacy/
│   │   └── PharmacyController.php              # Dispensing & Stock
│   └── Cashier/
│       └── BillingController.php               # Invoice & Cash Receipt
└── Models/                                     # 10 Domain Models terelasi
```
