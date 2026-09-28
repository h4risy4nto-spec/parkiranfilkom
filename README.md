# 🅿️ Digitalisasi Parkiran FILKOM UB
**Sistem Manajemen Parkir Digital Fakultas Ilmu Komputer – Universitas Brawijaya**

> Proyek Praktikum Pemrograman Web — Kelompok 1 PTI D

---

## 📋 Deskripsi Proyek

Aplikasi **Digitalisasi Parkiran FILKOM UB** adalah sistem manajemen parkir berbasis web yang dibangun menggunakan **Laravel 12** dengan stack teknologi modern untuk mendukung operasional parkir Fakultas Ilmu Komputer Universitas Brawijaya.

### ✨ Fitur Utama
- **Dashboard Real-time** — Pemantauan ketersediaan slot parkir motor & mobil secara langsung
- **Denah Slot Visual** — Peta visual posisi dan status setiap slot parkir per area
- **Tiket QR Code Digital** — Penerbitan tiket parkir dengan QR Code otomatis via kamera
- **Scanner QR Kamera** — Pemindaian QR Code menggunakan webcam/kamera untuk kendaraan keluar
- **Kalkulasi Tarif Otomatis** — Perhitungan durasi dan biaya parkir berdasarkan skema per jam
- **Denda Tiket Hilang** — Penerapan denda tiket hilang secara otomatis
- **Transaksi Database (ACID)** — Penggunaan DB Transaction untuk mencegah race condition slot
- **Admin Panel Lengkap** — Kelola area, slot, tarif, tiket aktif, riwayat, dan pengguna
- **Laporan Resmi** — Cetak laporan rekapitulasi transaksi berformat surat resmi
- **Multi-role Authentication** — Login Admin & Operator dengan middleware pembatasan akses

---

## 🛠️ Teknologi yang Digunakan

| Komponen | Teknologi |
|---|---|
| Backend Framework | Laravel 12 (PHP 8.2) |
| Database | MySQL (MariaDB via XAMPP) |
| ORM | Laravel Eloquent ORM |
| Frontend | Blade Template + Bootstrap 5.3 |
| Styling | Custom CSS (dark theme, neon green/orange) |
| QR Code | `simplesoftwareio/simple-qrcode` v4.2 |
| QR Scanner | `html5-qrcode` v2.3.8 (CDN) |
| Ikon | Bootstrap Icons 1.11 |
| Font | Plus Jakarta Sans (Google Fonts) |
| Authentication | Laravel Auth + Middleware |

---

## 👥 Struktur Pembagian Tugas (4 Anggota)

| Anggota | Role | Tanggung Jawab |
|---|---|---|
| **Anggota 1** | Admin/Full-stack | Dashboard, Peta Slot Visual, API Stats |
| **Anggota 2** | Operator/Backend | Fitur Check-In, Penerbitan Tiket, Validasi Form |
| **Anggota 3** | Operator/Frontend | Fitur Check-Out, Scanner QR Kamera, Kalkulasi Tarif |
| **Anggota 4** | Admin/Backend | Admin Panel (Area, Tarif, Pengguna), Migration, Seeder |

---

## 📁 Struktur Direktori Utama

```
parkiran-filkom/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/AuthController.php
│   │   │   ├── Admin/
│   │   │   │   ├── AdminAreaController.php
│   │   │   │   ├── AdminTicketController.php
│   │   │   │   ├── AdminHistoryController.php
│   │   │   │   ├── AdminRateController.php
│   │   │   │   └── AdminUserController.php
│   │   │   ├── CheckInController.php
│   │   │   ├── CheckOutController.php
│   │   │   └── DashboardController.php
│   │   └── Middleware/AdminMiddleware.php
│   └── Models/
│       ├── User.php
│       ├── ParkingArea.php
│       ├── ParkingSlot.php
│       ├── ParkingRate.php
│       └── ParkingTicket.php
├── database/
│   ├── migrations/
│   │   ├── ..._create_users_table.php
│   │   ├── ..._create_parking_areas_table.php
│   │   ├── ..._create_parking_slots_table.php
│   │   ├── ..._create_parking_rates_table.php
│   │   └── ..._create_parking_tickets_table.php
│   └── seeders/DatabaseSeeder.php
├── resources/views/
│   ├── layouts/app.blade.php
│   ├── auth/login.blade.php
│   ├── dashboard/
│   │   ├── index.blade.php
│   │   └── slots-map.blade.php
│   ├── checkin/
│   │   ├── index.blade.php
│   │   ├── ticket.blade.php
│   │   └── print.blade.php
│   ├── checkout/
│   │   ├── index.blade.php
│   │   └── receipt.blade.php
│   └── admin/
│       ├── areas/index.blade.php
│       ├── tickets/active.blade.php
│       ├── history/index.blade.php & report.blade.php
│       ├── rates/index.blade.php
│       └── users/index.blade.php
├── routes/web.php
├── public/css/parkiran.css
└── legacy/          ← Versi awal frontend-only (arsip)
```

---

## ⚙️ Instalasi & Konfigurasi

### Prasyarat
Pastikan sudah terinstall di komputer:
- **XAMPP** (PHP 8.2 + MariaDB) — [https://www.apachefriends.org](https://www.apachefriends.org)
- **Composer** — [https://getcomposer.org](https://getcomposer.org)
- **Git** — [https://git-scm.com](https://git-scm.com)

> ℹ️ Jika Composer belum ada di PATH, gunakan `C:\xampp\php\php.exe composer.phar` sebagai gantinya.

---

### 1. Clone Repository

```bash
git clone https://github.com/[username]/parkiran-filkom.git
cd parkiran-filkom
```

---

### 2. Install Dependensi PHP

```bash
composer install
```

Atau menggunakan PHP dari XAMPP:
```bash
C:\xampp\php\php.exe composer.phar install
```

---

### 3. Salin & Konfigurasi File `.env`

```bash
cp .env.example .env
```

Edit file `.env` sesuai konfigurasi lokal Anda:

```env
APP_NAME="Digitalisasi Parkiran FILKOM"
APP_ENV=local
APP_TIMEZONE=Asia/Jakarta
APP_URL=http://localhost:8000

APP_LOCALE=id
APP_FALLBACK_LOCALE=id
APP_FAKER_LOCALE=id_ID

# ================================================
# DATABASE — Sesuaikan dengan konfigurasi XAMPP Anda
# ================================================
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=parkiran_filkom
DB_USERNAME=root
DB_PASSWORD=           # Kosongkan jika XAMPP tanpa password

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

---

### 4. Generate Application Key

```bash
php artisan key:generate
```

---

### 5. Buat Database MySQL

Pastikan MySQL/MariaDB (XAMPP) sedang **berjalan**, kemudian buat database:

```sql
-- Jalankan di phpMyAdmin atau MySQL CLI:
CREATE DATABASE parkiran_filkom CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Atau via command line XAMPP:
```bash
C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS parkiran_filkom CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

---

### 6. Jalankan Migrasi Database

```bash
php artisan migrate
```

Perintah ini akan membuat tabel:
- `users` — Pengguna (Admin & Operator)
- `parking_areas` — Master area parkir
- `parking_slots` — Slot parkir per area
- `parking_rates` — Skema tarif kendaraan
- `parking_tickets` — Riwayat tiket masuk/keluar
- `sessions`, `cache`, `jobs` (Laravel internals)

---

### 7. Jalankan Seeder (Data Awal)

```bash
php artisan db:seed
```

Seeder akan mengisi data:
- **6 akun pengguna** (1 admin, 1 operator, 4 anggota kelompok)
- **3 area parkir** (Area A: 20 slot motor, Area B: 15 slot motor, Area C: 10 slot mobil)
- **45 slot parkir** (dibuat otomatis per area)
- **2 tarif** (Motor: Rp 2.000/jam, Mobil: Rp 5.000/jam)
- **7 tiket sampel** (4 aktif, 3 riwayat selesai)

---

### 8. Jalankan Server Lokal

```bash
php artisan serve
```

Atau dengan port kustom:
```bash
php artisan serve --host=127.0.0.1 --port=8000
```

Buka browser dan akses: **http://localhost:8000**

---

## 🔑 Akun Login Bawaan (Seeder)

| Nama | Email | Password | Role |
|---|---|---|---|
| Administrator FILKOM | `admin@filkom.ub.ac.id` | `password123` | **Admin** |
| Petugas Gerbang | `operator@filkom.ub.ac.id` | `password123` | Operator |
| Anggota 1 | `anggota1@filkom.ub.ac.id` | `password123` | Admin |
| Anggota 2 | `anggota2@filkom.ub.ac.id` | `password123` | Operator |
| Anggota 3 | `anggota3@filkom.ub.ac.id` | `password123` | Operator |
| Anggota 4 | `anggota4@filkom.ub.ac.id` | `password123` | Admin |

> ⚠️ Ganti password default sebelum deploy ke lingkungan produksi!

---

## 🗺️ Peta Rute Aplikasi

| URL | Deskripsi | Akses |
|---|---|---|
| `/dashboard` | Beranda & statistik parkir | Publik |
| `/dashboard/slots` | Denah slot visual interaktif | Publik |
| `/checkin` | Form kendaraan masuk & tiket QR | Publik |
| `/checkin/ticket/{kode}` | Detail tiket + QR Code | Publik |
| `/checkout` | Scanner QR & kendaraan keluar | Publik |
| `/checkout/receipt/{kode}` | Struk pembayaran | Publik |
| `/login` | Login petugas/admin | Guest only |
| `/admin/areas` | Kelola area & slot parkir | Admin |
| `/admin/tickets/active` | Kendaraan sedang parkir | Admin |
| `/admin/history` | Riwayat & laporan transaksi | Admin |
| `/admin/rates` | Pengaturan tarif & denda | Admin |
| `/admin/users` | Kelola pengguna sistem | Admin |

---

## 🔄 Reset Data (Opsional)

Jika ingin mengulang dari awal:

```bash
php artisan migrate:fresh --seed
```

> ⚠️ Perintah ini akan **menghapus semua data** dan mengisi ulang dengan data seeder.

---

## 🐛 Troubleshooting

### MySQL tidak bisa connect
1. Pastikan XAMPP MySQL **sudah dijalankan** via XAMPP Control Panel
2. Periksa port 3306 tidak bentrok dengan software lain
3. Pastikan `.env` memiliki `DB_PASSWORD=` kosong (bukan dihapus)

### Error 500 di browser
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### Error "Class not found"
```bash
composer dump-autoload
```

### Session/Database error
```bash
php artisan migrate:fresh --seed
```

---

## 📄 Lisensi

Proyek ini dibuat untuk keperluan akademis praktikum pemrograman web.
**© 2026 Kelompok 1 PTI D — Fakultas Ilmu Komputer Universitas Brawijaya**
