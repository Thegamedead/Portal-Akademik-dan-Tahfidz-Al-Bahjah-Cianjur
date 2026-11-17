# 🚀 SETUP PROJECT DI KOMPUTER BARU

## ✅ LANGKAH-LANGKAH SETUP (FRESH INSTALL)

### 1. 📋 PERSIAPAN ENVIRONMENT

**A. Pastikan Laragon Sudah Install:**
- Download Laragon Full dari https://laragon.org
- Start Apache + MySQL
- Test: http://localhost harus bisa dibuka

**B. Pastikan PHP & Composer Ready:**
```bash
# Test di terminal Laragon:
php --version    # Harus PHP 8.3+
composer --version
```

### 2. 💻 SETUP PROJECT

**A. Install Dependencies PHP:**
```bash
# Buka terminal di folder project
cd C:\laragon\www\Sistem-Informasi-Tahfidz-SMP

# Install dependencies
composer install
```

**B. Install Dependencies JavaScript:**
```bash
# Install Node modules
npm install
```

### 3. ⚙️ KONFIGURASI ENVIRONMENT

**A. Setup .env File:**
```bash
# Copy .env template
copy .env.example .env
```

**B. Edit .env dengan konfigurasi berikut:**
```env
APP_NAME="Sistem Informasi Tahfidz SMP"
APP_ENV=local
APP_KEY=                    # Akan digenerate
APP_DEBUG=true
APP_URL=http://sistem-informasi-tahfidz-smp.test

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tahfidz_smp_fresh    # Database baru
DB_USERNAME=root
DB_PASSWORD=                     # Kosong untuk Laragon

# Session & Cache
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

### 4. 🗄️ SETUP DATABASE FRESH

**A. Buat Database Baru:**
```sql
-- Buka phpMyAdmin: http://localhost/phpmyadmin
-- Buat database baru:
CREATE DATABASE tahfidz_smp_fresh 
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**B. Generate App Key:**
```bash
# Generate application key
php artisan key:generate
```

**C. Run Migration (Database Fresh):**
```bash
# Jalankan migration untuk buat tabel
php artisan migrate

# Seed data referensi Al-Quran (30 Juz, 114 Surah)
php artisan db:seed --class=JuzSeeder
php artisan db:seed --class=SurahSeeder
php artisan db:seed --class=JuzSurahSeeder

# Buat admin default
php artisan db:seed --class=AdminSeeder

# Seed tahun ajaran aktif
php artisan db:seed --class=TahunAjaranSeeder

# Parameter konfigurasi
php artisan db:seed --class=ParameterKonfigurasiSeeder
```

### 5. 🎨 BUILD ASSETS

```bash
# Build CSS/JS assets
npm run build

# Atau untuk development:
npm run dev
```

### 6. 🔧 SET PERMISSIONS & CLEAR CACHE

```bash
# Set storage permissions (Windows)
# Buka Command Prompt as Administrator
icacls storage /grant Everyone:F /T
icacls bootstrap\cache /grant Everyone:F /T

# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### 7. 🌐 TEST APPLICATION

**A. Start Server:**
- Method 1 (Laragon): Akses http://sistem-informasi-tahfidz-smp.test
- Method 2 (Manual): `php artisan serve` → http://localhost:8000

**B. Test Login Admin:**
- URL: http://sistem-informasi-tahfidz-smp.test/admin/login
- Username: `admin`
- Password: `password`

## ✅ VERIFICATION CHECKLIST

Pastikan semua berfungsi:

- [ ] **Homepage** loading tanpa error
- [ ] **Admin login** berhasil
- [ ] **Dashboard admin** tampil
- [ ] **Menu master data** bisa diakses
- [ ] **Database** terkoneksi (cek di tab guru/siswa)

## 🎯 LANGKAH SELANJUTNYA

Setelah setup berhasil, Anda bisa:

### 1. **Input Data Master:**
- Buat tahun ajaran baru
- Input data kelas (7A, 7B, 8A, dst)
- Input data guru pembimbing
- Input data siswa
- Buat kelompok tahfidz

### 2. **Setup Akun Pengguna:**
- Generate password untuk guru
- Generate password untuk siswa
- Cetak kartu login

### 3. **Mulai Input Data:**
- Input absensi harian
- Input setoran hafalan
- Monitor progres siswa

## 🚨 TROUBLESHOOTING

**Jika ada error:**

### Error: "Class not found"
```bash
composer dump-autoload
php artisan clear-compiled
```

### Error: "No application encryption key"
```bash
php artisan key:generate
```

### Error: Database connection
```bash
# Check:
1. MySQL running di Laragon?
2. Database tahfidz_smp_fresh sudah dibuat?
3. .env config benar?
```

### Error: Storage permission
```bash
# Windows (as Administrator):
icacls storage /grant Everyone:F /T
```

## 🎊 SUCCESS INDICATORS

**✅ Setup berhasil jika:**
- Login admin success
- Dashboard tampil data kosong (fresh start)
- Menu master data bisa diakses
- Bisa input data guru/siswa baru

---

## 📱 QUICK START CHECKLIST

Centang satu per satu:

- [ ] Laragon running
- [ ] `composer install` done
- [ ] `npm install` done  
- [ ] `.env` configured
- [ ] Database created
- [ ] `php artisan migrate` done
- [ ] Seeders run
- [ ] `npm run build` done
- [ ] Admin login works

**🎉 SELAMAT! Project siap digunakan dengan database fresh!**
