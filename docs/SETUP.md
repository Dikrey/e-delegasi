# 📜 Aplikasi Surat Menyurat (Laravel) — Panduan Setup Lengkap

> Dokumentasi lengkap dari awal: penjelasan aplikasi, fitur, requirement, instalasi (Windows/Laragon & server produksi/Linux), migrasi database, seeder, dan cara kerja aplikasi.

---

## Daftar Isi

1. [Gambaran Aplikasi](#1-gambaran-aplikasi)
2. [Fitur-Fitur](#2-fitur-fitur)
3. [Kebutuhan Sistem (Requirement)](#3-kebutuhan-sistem-requirement)
4. [Struktur Database](#4-struktur-database)
5. [Instalasi di Windows (Laragon / XAMPP)](#5-instalasi-di-windows-laragon--xampp)
   - 5.1 Install Prasyarat (PHP, Composer, MySQL)
   - 5.2 Setup Aplikasi
   - 5.3 Membuat Database & File `.env`
   - 5.4 Migrasi & Seeder
   - 5.5 Menjalankan Aplikasi
6. [Instalasi di Server Produksi (Linux / VPS / Shared Hosting)](#6-instalasi-di-server-produksi-linux--vps--shared-hosting)
   - 6.1 Prasyarat Server
   - 6.2 Deploy & Setup
   - 6.3 Nginx + PHP-FPM
   - 6.4 Apache + VirtualHost (Shared Hosting)
   - 6.5 Security & Optimasi
7. [Migrasi Database: Step-by-Step](#7-migrasi-database-step-by-step)
   - 7.1 Daftar Migrasi
   - 7.2 Perintah Migrasi
   - 7.3 Seeder (Data Awal)
8. [Akun Default](#8-akun-default)
9. [Pengaturan Bahasa & Zona Waktu](#9-pengaturan-bahasa--zona-waktu)
10. [Cara Kerja Aplikasi](#10-cara-kerja-aplikasi)
    - 10.1 Autentikasi & Hak Akses
    - 10.2 Alur Surat Masuk
    - 10.3 Alur Surat Keluar (Transaksi Normal)
    - 10.4 Alur Booking / Permintaan Nomor Surat
    - 10.5 Disposisi
    - 10.6 Agenda & Cetak
    - 10.7 Galeri & Arsip
    - 10.8 Server Monitor & Backup
11. [Troubleshooting & FAQ](#11-troubleshooting--faq)
12. [Menjalankan Test (PHPUnit)](#12-menjalankan-test-phpunit)

---

## 1. Gambaran Aplikasi

**Aplikasi Surat Menyurat** adalah aplikasi web berbasis **Laravel 9** (PHP) yang dirancang untuk mengelola administrasi surat secara digital, efisien, dan terorganisir. Aplikasi mencakup:

- Pencatatan **surat masuk** dan **surat keluar**.
- **Disposisi** surat (penerusan ke bagian/lain dengan status & tenggat waktu).
- **Pencarian, agenda, cetak** agenda surat, **galeri** lampiran, dan **arsip** dengan filter tanggal + export.
- **Dashboard** interaktif dengan statistik real-time, grafik tren 7 hari, breakdown hari ini, dan **visualisasi 3D** (Three.js).
- Multi-peran (admin, sekretaris & staff), multi-device session management, dan multi-bahasa (Indonesia/Inggris).

UI memakai template **Sneat** (Bootstrap 5) dengan banyak kustomisasi CSS modern.

---

## 2. Fitur-Fitur

| Modul | Deskripsi | Hak Akses |
|---|---|---|
| **Autentikasi** | Login/logout, validasi session aktif | Semua (staff & admin) |
| **Dashboard** | Statistik hari ini (masuk/keluar/disposisi), tren 7 hari, breakdown modern, aktivitas terbaru, aksi cepat, 3D envelope Three.js | Semua |
| **Surat Masuk** | CRUD, upload lampiran (jpg/png/pdf), pencarian, filter agenda, cetak agenda | Semua |
| **Surat Keluar** | CRUD, upload lampiran, status transaksi (draft → final), pencarian, agenda & cetak | Semua |
| **Disposisi** | Tambah/ubah/hapus disposisi per surat (tujuan, tenggat, isi, sifat surat) | Semua |
| **Agenda** | Agenda surat masuk/keluar dengan filter tanggal & cetak | Semua |
| **Galeri** | Grid lampiran surat + preview popup (gambar) / unduh (PDF) | Semua |
| **Arsip** | Filter rentang tanggal (since/until) + export | Semua |
| **Referensi** | Klasifikasi surat & sifat surat (CRUD) | Admin |
| **Pengguna** | CRUD user, aktif/nonaktif, reset password (default dari Config) | Admin |
| **Profil** | Ubah nama/email/telepon, foto profil, ganti password, nonaktifkan akun | Semua |
| **Perangkat / Session** | Daftar perangkat login aktif, logout per perangkat / semua perangkat | Semua |
| **Pengaturan** | Konfigurasi: nama aplikasi, institusi, alamat, telepon, email, default password, page size, bahasa, PIC | Admin |

---

## 3. Kebutuhan Sistem (Requirement)

Bagian ini berlaku untuk Windows (dev) **dan** server/Linux (produksi).

| Komponen | Minimal | Disarankan |
|---|---|---|
| **PHP** | 8.0.2 | 8.1 / 8.2 / 8.3 |
| Ekstensi PHP wajib | `openssl`, `pdo`, `pdo_mysql`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `curl`, `fileinfo`, `gd` (optional), `zip` (optional) | — |
| **Composer** | 2.x | 2.5+ |
| **MySQL** | 5.7 / 8.0 | 8.0 |
| **Node.js** (optional) | 16+ (hanya jika perlu build asset; aset Sneat sudah include) | — |
| **Web Server** | Apache / Nginx | Nginx + PHP-FPM (produksi) |
| **RAM** | 512 MB | 1 GB+ |

> ⚠️ **Catatan penting tentang migrasi MySQL:** Beberapa migrasi memakai `DB::statement('ALTER TABLE letters MODIFY ...')` yang **spesifik MySQL/MariaDB**. Aplikasi ini **tidak** berjalan normal di SQLite. Pastikan koneksi database di `.env` mengarah ke MySQL/MariaDB.

---

## 4. Struktur Database

Ringkasan tabel utama (skema lengkap dapat dilihat di file migrasi):

| Tabel | Keterangan |
|---|---|
| `users` | Pengguna (role `admin`/`staff`, `is_active`, `profile_picture`, `session_version`) |
| `login_sessions` | Sesi login aktif per pengguna (device tracking) |
| `configs` | Pengaturan dinamis (`default_password`, `page_size`, identitas institusi, bahasa) |
| `classifications` | Klasifikasi surat (mis. `ADM` Administrasi, `UMUM` Umum) |
| `letter_statuses` | Sifat surat untuk disposisi (Rahasia, Segera, Biasa) |
| `letters` | Data surat (masuk/keluar). Punya `reference_number` UNIQUE, `type`, `status` (`waiting_for_final_file`/`final`), `classification_code` (FK) |
| `attachments` | Lampiran surat (filename, extension, path) |
| `dispositions` | Disposisi (tujuan, tenggat, isi, sifat surat, surat terkait) |
| `failed_jobs`, `personal_access_tokens`, `password_resets` | Tabel bawaan Laravel (opsional / jarang dipakai di aplikasi ini) |

**Format nomor surat:** `{kode_ruangan}/{nomor_urut 3 digit}/{kode}/{bulan romawi}/{tahun}`  
Contoh: `521.1/001/SK/VII/2026` — artinya ruang `521.1`, nomor urut `001`, kode `SK`, bulan `Juli` (VII), tahun `2026`.

---

## 5. Instalasi di Windows (Laragon / XAMPP)

### 5.1 Install Prasyarat (PHP, Composer, MySQL)

**Opsi A — Laragon (disarankan)**
1. Download & install [Laragon](https://laragon.org/download/) (Full).
2. Pastikan Laragon memakai PHP ≥ 8.0 (Laragon biasanya sudah menyertakan beberapa versi PHP). Anda bisa menambah tambahan PHP versi terbaru lewat menu *Menu → PHP → Version Manager*.
3. Pastikan **Composer** tersedia:
   - Jalankan `composer --version`. Jika belum ada, pasang [Composer Windows Installer](https://getcomposer.org/download/).
4. Laragon menjalankan MySQL otomatis. Untuk melakukan sesuatu, gunakan terminal Laragon: **Menu → Root → Start All** lalu klik **Terminal**.

**Opsi B — Manual (XAMPP)**
1. Install [XAMPP](https://www.apachefriends.org/) (PHP 8.x).
2. Install [Composer](https://getcomposer.org/download/).
3. Start module **Apache** dan **MySQL** dari XAMPP Control Panel.
4. Tambahkan path PHP XAMPP ke `PATH` (mis. `C:\xampp\php`).

### 5.2 Setup Aplikasi

Buka terminal (**Laragon Terminal** atau *PowerShell*), lalu:

```bash
# 1. Clone / salin project ke folder web Laragon (atau letakkan di path lain)
#    Jika pakai Laragon, umumnya: C:\laragon\www\
cd C:\laragon\www

# (Contoh) clone dari git repository
git clone <url-repository> laravel-surat
cd laravel-surat

# 2. Install dependency PHP dengan Composer
composer install

# 3. Salin file konfigurasi .env
#    Windows CMD/PowerShell:
copy .env.example .env
#    Atau Git Bash / WSL:
# cp .env.example .env
```

### 5.3 Membuat Database & File `.env`

1. **Buat database MySQL** (mis. `surat`). Bisa lewat:
   - phpMyAdmin: buka `http://localhost/phpmyadmin` → *New* → ketik nama `surat` → collation `utf8mb4_unicode_ci`.
   - Atau lewat terminal Laragon:
     ```bash
     mysql -u root -e "CREATE DATABASE surat CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
     ```

2. **Edit file `.env`** sesuai environment Anda:

   ```env
   APP_NAME="Surat"
   APP_ENV=local
   APP_KEY=
   APP_DEBUG=true
   APP_URL=http://laravel-surat.test        # sesuaikan (Laragon: http://laravel-surat.test)

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=surat
   DB_USERNAME=root
   DB_PASSWORD=                             # kosongkan jika root tanpa password (default Laragon)
   ```

   > 💡 **Laragon hint:** aplikasi otomatis terbuka di `http://laravel-surat.test` jika folder project bernama `laravel-surat` (auto virtual host). Anda juga bisa klik kanan icon Laragon → *Root* → *Site**.

3. **Generate application key:**

   ```bash
   php artisan key:generate
   ```

4. **Buat symbolic link storage** (agar file upload bisa diakses via URL):

   ```bash
   php artisan storage:link
   ```

   > Jalankan `php artisan storage:link` setiap kali environment baru; pastikan folder `storage/app/public` ada. Pada Windows, jika muncul error *privilege not held*, jalankan terminal **sebagai Administrator**.

### 5.4 Migrasi & Seeder

```bash
# 1. Jalankan semua migrasi database
php artisan migrate

# 2. Seeder data awal wajib (admin + konfigurasi + klasifikasi + sifat surat)
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=ConfigSeeder
php artisan db:seed --class=LetterStatusSeeder
php artisan db:seed --class=ClassificationSeeder

# Atau sekali jalan semua seeder datum awal:
php artisan db:seed

# 3. (Opsional) Isi data contoh / dummy:
php artisan db:seed --class=LetterSeeder
php artisan db:seed --class=DispositionSeeder
# atau reset + seed semuanya (dummy data):
php artisan migrate:fresh --seed
```

### 5.5 Menjalankan Aplikasi

```bash
php artisan serve
# Kemudian buka: http://127.0.0.1:8000
# UNTUK 1 JARINGAN 

php artisan serve --host=0.0.0.0 --port=8000

```

Ataupun pakai ide Laragon: klik kanan icon Laragon → **Web** → pilih `laravel-surat.test`. Pastikan MySQL sudah berjalan (*Start All*).

---

## 6. Instalasi di Server Produksi (Linux / VPS / Shared Hosting)

### 6.1 Prasyarat Server

```bash
# Ubuntu / Debian
sudo apt update
sudo apt install -y nginx mysql-server php8.1-fpm php8.1-cli \
  php8.1-mysql php8.1-mbstring php8.1-xml php8.1-curl php8.1-zip \
  php8.1-bcmath php8.1-gd php8.1-intl unzip curl git

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
composer --version
```

> Dengan syarat `php: ^8.0.2`. Versi 8.1 – 8.3 sangat direkomendasikan.

### 6.2 Deploy & Setup

```bash
# 1. Pindahkan project (mis. ke /var/www/laravel-surat) lalu:
cd /var/www/laravel-surat

# 2. Dependency (jika belum ada vendor)
composer install --no-dev --optimize-autoloader

# 3. Environment
cp .env.example .env
php artisan key:generate

# 4. Buat database & user MySQL
sudo mysql -u root -p
#   CREATE DATABASE surat CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
#   CREATE USER 'surat'@'localhost' IDENTIFIED BY 'SANGAT-RAHASIA';
#   GRANT ALL PRIVILEGES ON surat.* TO 'surat'@'localhost';
#   FLUSH PRIVILEGES;

# 5. Edit .env produksi
#    APP_ENV=production, APP_DEBUG=false, APP_URL=https://domain-anda.com
#    DB_DATABASE=surat, DB_USERNAME=surat, DB_PASSWORD=...

# 6. Migrasi + seeder
php artisan migrate --force
php artisan db:seed --force

# 7. Storage link + permission
php artisan storage:link
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# 8. Optimasi
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 6.3 Nginx + PHP-FPM

Buat file `/etc/nginx/sites-available/laravel-surat`:

```nginx
server {
    listen 80;
    server_name domain-anda.com www.domain-anda.com;
    root /var/www/laravel-surat/public;

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
sudo ln -sf /etc/nginx/sites-available/laravel-surat /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

Opsional pasang **Let's Encrypt / SSL**:

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d domain-anda.com
```

### 6.4 Apache + VirtualHost (Shared Hosting)

```apache
<VirtualHost *:80>
    ServerAdmin admin@domain-anda.com
    ServerName domain-anda.com
    DocumentRoot /var/www/laravel-surat/public

    <Directory /var/www/laravel-surat/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```

```bash
sudo a2ensite laravel-surat.conf
sudo a2enmod rewrite
sudo systemctl reload apache2
```

**Pada shared hosting (cPanel / hPanel / Plesk):**
1. Upload project via file manager / Git. Jalankan `composer install` via *Terminal* (jika tersedia) atau buat `vendor` di lokal lalu upload.
2. Pindahkan isi folder `public` ke `public_html` **ATAU** set *DocumentRoot*/domain path ke `.../public`. **Penting:** jangan expose folder root project; hanya folder `public` yang menjadi document root.
3. Edit `.env` sesuai kredensial DB hosting, jalankan migrasi via *Terminal*.
4. Pastikan folder upload/`storage` writable.

### 6.5 Security & Optimasi

- Konfigurasi `.env`: `APP_ENV=production`, `APP_DEBUG=false`.
- Aktifkan **HTTPS** (certbot / sertifikat hosting).
- Batasi upload: ukuran & ekstensi (png/jpg/pdf/doc/docx) sudah divalidasi di controller/request.
- Jalankan optimasi Laravel:
  ```bash
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache
  ```
- **Backup** rutin: aplikasi menyediakan *Server → Download Backup JSON* (admin). Untuk backup penuh, lakukan dump MySQL:
  ```bash
  mysqldump -u surat -p surat > backup-surat-$(date +%F).sql
  ```

---

## 7. Migrasi Database: Step-by-Step

### 7.1 Daftar Migrasi

Semua file berada di `database/migrations/` dan berjalan **sesuai urutan nama**:

| Migrasi | Deskripsi |
|---|---|
| `2014_10_12_000000_create_users_table` | Tabel `users` (+ `phone`, `role`, `is_active`, `profile_picture`) |
| `2014_10_12_100000_create_password_resets_table` | Reset password |
| `2014_10_12_200000_add_two_factor_columns_to_users` | Kolom 2FA (Fortify) |
| `2019_08_19_000000_create_failed_jobs_table` | Failed jobs |
| `2019_12_14_000001_create_personal_access_tokens_table` | Sanctum tokens |
| `2022_12_05_081849_create_configs_table` | Konfigurasi |
| `2022_12_05_083409_create_letter_statuses_table` | Sifat surat |
| `2022_12_05_083945_create_classifications_table` | Klasifikasi |
| `2022_12_05_084544_create_letters_table` | Surat (UNIQUE reference_number, FK classification & user) |
| `2022_12_05_092303_create_dispositions_table` | Disposisi |
| `2022_12_05_093329_create_attachments_table` | Lampiran |
| `2026_09_11_000001_create_login_sessions_table` | Sesi login & perangkat |
| `2026_09_11_000002_add_session_version_to_users` | `session_version` untuk "logout semua perangkat" |
| `2026_09_11_000002_update_letters_for_booking` | Kolom `status` di `letters`; jadikan `agenda_number` nullable |
| `2026_09_11_000003_add_code_part_and_unique_reference` | Pastikan index UNIQUE `reference_number` di `letters` |
| `2026_09_19_000001_drop_booking_nomor_surat_requests` | Hapus tabel `nomor_surat_requests` & kolom `letters.request_id` (fitur booking dihapus) |

> ⚠️ Migrasi `2026_09_11_000002_update_letters_for_booking` menggunakan `ALTER TABLE ... MODIFY` MySQL. Pastikan driver di `.env` adalah `mysql`.

### 7.2 Perintah Migrasi

```bash
php artisan migrate                # jalankan semua pending migration
php artisan migrate:status         # lihat status
php artisan migrate:rollback       # rollback batch terakhir
php artisan migrate:fresh          # drop semua tabel + migrasi ulang (HAPUS DATA)
php artisan migrate:fresh --seed   # drop + migrasi + seeder (dummy)
```

### 7.3 Seeder (Data Awal)

| Seeder | Isi |
|---|---|
| `UserSeeder` | Admin default: `admin@admin.com` / `admin` (role admin) |
| `ConfigSeeder` | `default_password=admin`, `page_size=5`, nama aplikasi, identitas institusi, `language=id`, `pic` |
| `LetterStatusSeeder` | Rahasia, Segera, Biasa |
| `ClassificationSeeder` | `ADM` (Administrasi) |
| `LetterSeeder` + `DispositionSeeder` | Data dummy surat & disposisi (opsional) |

Jalankan seeders dasar sekali:

```bash
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=ConfigSeeder
php artisan db:seed --class=LetterStatusSeeder
php artisan db:seed --class=ClassificationSeeder
```

Atau jalankan `ConfigSeeder` setelah aplikasi berjalan — nilai `page_size`, identitas, dsb, diubah juga lewat halaman **Pengaturan** (admin).

---

## 8. Akun Default

| Surel | Kata Sandi | Role |
|---|---|---|
| `admin@admin.com` | `admin` | admin |

Setelah login, segera ubah password lewat menu **Profil**. Password default pengguna baru juga ditentukan dari config `default_password` (awal: `admin`).

---

## 9. Pengaturan Bahasa & Zona Waktu

### Bahasa

- Aplikasi mendukung **Indonesia** (`id`) dan **Inggris** (`en`).
- Ubah di `config/app.php`:
  ```php
  'locale' => 'id',   // atau 'en'
  ```
- Ubah juga nilai config `language` pada tabel `configs` (lewat halaman Pengaturan) agar sisi backend ikut.
- File terjemahan: `lang/id/*.php` dan `lang/en/*.php`.

### Zona Waktu

Ubah di `config/app.php`:

```php
'timezone' => 'Asia/Jakarta',
```

Daftar zona waktu: https://www.php.net/manual/en/timezones.php

---

## 10. Cara Kerja Aplikasi

### 10.1 Autentikasi & Hak Akses

```
Browser ──> GET /login (guest) ──> POST login
             │
             ▼
        users check (email + password + is_active)
             │
             ▼
        login_sessions row dibuat (device/browser/IP ditebak)
             │
             ▼
   Middleware 'auth' + 'session.valid' (EnsureActiveSession)
        - session_id harus cocok dengan login_sessions
        - jika user dinonaktifkan / di-logout dari semua perangkat (session_version)
          → otomatis keluar
```

- **Role** dicek via middleware `role:admin` (di-routing) atau kontrol `auth()->user()->role`.
- `admin` bisa: kelola pengguna, referensi, pengaturan.
- `sekretaris` & `staff` bisa: semua transaksi surat, agenda, galeri, arsip, profil (dan menonaktifkan akun sendiri).

### 10.2 Alur Surat Masuk

1. **Buat**: *Transaksi → Surat Masuk → + Tambah* — isi nomor, dari, tanggal, klasifikasi, deskripsi, lampiran (jpg/png/pdf).
2. Data disimpan di tabel `letters` (`type='incoming'`, `user_id`=pembuat).
3. **Pencarian**: filter `search` mencocokkan `reference_number`, `agenda_number`, `from`, `description`, `note`, klasifikasi.
4. **Disposisi**: setiap surat masuk bisa punya banyak disposisi (sifat Rahasia/Segera/Biasa, tenggat, tujuan).
5. **Agenda & Galeri**: bisa dilihat lewat menu Agenda (filter tanggal + cetak) dan Galeri (grid lampiran).
6. **Hapus**: konfirmasi SweetAlert → hard delete (lampiran juga terhapus via cascade).

### 10.3 Alur Surat Keluar (Transaksi Normal)

1. **Buat** surat keluar seperti surat masuk (`type='outgoing'`), dengan kolom `to` (tujuan).
2. **Status transaksi** surat keluar:
   - `waiting_for_final_file` — draft, belum ada dokumen final.
   - `final` — dokumen final sudah diunggah & ditandai final (tombol *Mark as Final*).
3. Tombol **Finalize** hanya muncul jika ada minimal 1 lampiran; jika tidak, muncul pesan "upload final dulu".
4. Surat keluar juga bisa di-edit (menambah lampiran), dihapus, dicari, dilihat di agenda & galeri.

### 10.4 Halaman Tidak Ditemukan (404)

Halaman yang dihapus atau tidak tersedia (mis. halaman *Booking Nomor* dan *Server Info* yang sudah dihapus) dialihkan ke `resources/views/errors/404.blade.php` via `Route::fallback` — menampilkan kartu "404 – Halaman Tidak Ditemukan" dengan tombol kembali/ke beranda, diikuti oleh status HTTP 404.

### 10.5 Disposisi

- Route `transaction/{letter}/disposition` → CRUD disposisi per surat.
- Sifat surat diambil dari tabel `letter_statuses` (Rahasia/Segera/Biasa).
- Ada `due_date` (tenggat) dan `content`.
- Dashboard menghitung disposisi hari ini & tren 7 hari.

### 10.6 Agenda & Cetak

- `agenda/incoming` & `agenda/outgoing` → tabel agenda dengan filter `since`/`until` per kolom (`letter_date` atau `received_date`).
- Tombol **cetak** membuka halaman *printable* (title mengikuti bahasa: `Agenda Surat Masuk` / `Incoming Letter Agenda`).
- Jumlah data per halaman dari config `page_size`.

### 10.7 Galeri & Arsip

- **Galeri** (`gallery/incoming`, `gallery/outgoing`): kartu lampiran (gambar preview / PDF tile), klik → modal preview (gambar) atau buka PDF.
- **Arsip** (`archive`): filter rentang tanggal `since`/`until` + `export` (mengikuti filter yang sama). Export menghasilkan file (lihat `ArchiveController::export`) — tersedia untuk semua user.

### 10.8 Catatan Fitur yang Dihapus

- **Server Info (`/server`)** — halaman pemantauan sistem (*admin only*) beserta file pendukungnya (`ServerInfoController`, `Support/ServerProbe`) telah dihapus dari aplikasi.
- **Booking / Permintaan Nomor Surat (fitur booking nomor berjalan otomatis)** — tabel `nomor_surat_requests`, kolom `letters.request_id`, serta seluruh halaman & controller terkait telah dihapus. Masuk ke alamat halaman yang sudah dihapus kini menampilkan halaman **404**.

---

## 11. Troubleshooting & FAQ

### 1. `Target class [App\Models\Config] does not exist` / class not found
```bash
composer dump-autoload
```

### 2. Error file upload `storage` tidak muncul
Jalankan `php artisan storage:link`. Di Windows, buka terminal sebagai Administrator.

### 3. Migrasi gagal di `MODIFY agenda_number ... NULL`
Pastikan `DB_CONNECTION=mysql` di `.env`. Aplikasi ini tidak mendukung SQLite.

### 4. Halaman blank / 500 setelah deploy
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
# lalu optimasi ulang
php artisan config:cache && php artisan route:cache && php artisan view:cache
```
Dan pastikan permission: `sudo chmod -R 775 storage bootstrap/cache`.

### 5. Login berhasil tapi langsung logout
- Pastikan middleware `session.valid` konsisten dengan session cookie; sesi ditolak bila `session_id` tidak ada di `login_sessions`.
- Kemungkinan `session_version` user > dari yang tersimpan (ada yang klik *Logout dari semua perangkat*).

### 6. `user_id` pada disposisi / surat wajib terisi
Selalu login dengan user aktif; kolom `user_id` diisi otomatis dari `auth()->user()`.

### 7. Nomor surat sudah dipakai / duplicate
- Sistem sudah memakai UNIQUE index di `letters.reference_number` (menjamin nomor surat tidak ganda).
- Jika migration 000003 tidak jadi create index (karena ada data kosong/duplikat), bersihkan dahulu kemudian jalankan:
  ```sql
  ALTER TABLE letters ADD UNIQUE (reference_number);
  ```

### 8. Perubahan `page_size` tidak berefek
Pastikan config `page_size` terisi di tabel `configs` (ada di `ConfigSeeder`) dan simpan ulang dari halaman **Pengaturan**.

### 9. Three.js / ApexCharts tidak muncul
Butuh koneksi internet untuk CDN **Three.js** (https://cdnjs.cloudflare.com/ajax/libs/three.js/r134/three.min.js). ApexCharts sudah disertakan lokal di `sneat/vendor/libs/apex-charts/`.

### 10. Serve di port selain 8000
```bash
php artisan serve --port=8080
```

---

## 12. Menjalankan Test (PHPUnit)

Aplikasi memakai PHPUnit 9 & MySQL sebagai basis test.

```bash
# Jalankan semua test suite
vendor/bin/phpunit

# Jalankan tanpa coverage (lebih cepat & tidak butuh Xdebug)
vendor/bin/phpunit --no-coverage

# Hanya test tertentu
vendor/bin/phpunit tests/Feature/BookingFlowTest.php
```

> 💡 phpunit.xml memakai DB dari `.env` (mysql). Jika ingin SQLite, ubah blok `DB_CONNECTION`/`DB_DATABASE` pada phpunit.xml — namun perhatikan konten migrasi MySQL (`MODIFY`) pada `update_letters_for_booking`.

---

**Selamat mencoba!** Jika ada pertanyaan lebih lanjut seputar fitur atau setup, jangan ragu untuk bertanya.