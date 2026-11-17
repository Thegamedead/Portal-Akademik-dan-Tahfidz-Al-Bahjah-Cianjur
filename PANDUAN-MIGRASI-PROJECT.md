# PANDUAN MIGRASI PROJECT SISTEM INFORMASI TAHFIDZ SMP

## ⚠️ URGENT BACKUP GUIDE - KOMPUTER RUSAK

Panduan ini untuk memindahkan project dari komputer yang sedang rusak ke komputer baru.

---

## 📋 DAFTAR ISI

1. [Persiapan Backup (Komputer Lama)](#1-persiapan-backup-komputer-lama)
2. [Setup Environment (Komputer Baru)](#2-setup-environment-komputer-baru)
3. [Restore Project](#3-restore-project)
4. [Troubleshooting](#4-troubleshooting)

---

## 1. PERSIAPAN BACKUP (KOMPUTER LAMA)

### 1.1 Backup Files Project

**PRIORITAS TINGGI - Lakukan segera sebelum komputer semakin rusak:**

#### A. Backup Source Code

```bash
# Buat folder backup di external drive/cloud storage
mkdir "E:\Backup-Tahfidz-Project" # atau drive external lainnya

# Copy seluruh folder project
xcopy "C:\laragon\www\Sistem-Informasi-Tahfidz-SMP" "E:\Backup-Tahfidz-Project\" /E /H /C /I

# Atau menggunakan Explorer:
# 1. Buka Windows Explorer
# 2. Navigate ke C:\laragon\www\Sistem-Informasi-Tahfidz-SMP
# 3. Select All (Ctrl+A)
# 4. Copy (Ctrl+C)
# 5. Paste ke external drive atau cloud storage
```

#### B. Backup Database (SANGAT PENTING!)

**Cara 1: Menggunakan phpMyAdmin**
1. Buka `http://localhost/phpmyadmin`
2. Klik database `sistem_tahfidz_smp` (atau nama database yang Anda gunakan)
3. Klik tab **"Export"**
4. Pilih **"Quick"** export method
5. Format: **SQL**
6. Klik **"Go"**
7. Save file `.sql` ke external drive/cloud

**Cara 2: Menggunakan Command Line (jika MySQL masih berfungsi)**
```bash
# Buka Command Prompt as Administrator
cd C:\laragon\bin\mysql\mysql-8.0.30\bin

# Export database
mysqldump -u root -p sistem_tahfidz_smp > E:\Backup-Tahfidz-Project\database_backup.sql

# Masukkan password MySQL (biasanya kosong untuk Laragon)
```

**Cara 3: Copy Data Directory (jika MySQL tidak bisa dibuka)**
```bash
# Copy seluruh folder data MySQL
xcopy "C:\laragon\data\mysql" "E:\Backup-Tahfidz-Project\mysql-data\" /E /H /C /I
```

#### C. Backup Konfigurasi Laragon

```bash
# Backup virtual hosts
copy "C:\laragon\etc\apache2\sites-enabled\*" "E:\Backup-Tahfidz-Project\vhosts\"

# Backup Laragon settings
copy "C:\laragon\usr\laragon.ini" "E:\Backup-Tahfidz-Project\"
```

### 1.2 List File Penting yang WAJIB Di-backup

**✅ CHECKLIST BACKUP:**

- [ ] **Source Code**: Seluruh folder `Sistem-Informasi-Tahfidz-SMP`
- [ ] **Database**: File `.sql` export atau folder `mysql-data`
- [ ] **Environment**: File `.env` (berisi konfigurasi database)
- [ ] **Composer Dependencies**: Folder `vendor` (optional, bisa diinstall ulang)
- [ ] **Node Modules**: Folder `node_modules` (optional, bisa diinstall ulang)
- [ ] **Uploads**: Folder `storage/app` jika ada file upload
- [ ] **Logs**: Folder `storage/logs` untuk debug
- [ ] **Git Repository**: Folder `.git` jika menggunakan version control

### 1.3 Upload ke Cloud Storage (BACKUP CADANGAN)

**Pilihan Cloud Storage:**
- **Google Drive**: Upload folder backup ke Google Drive
- **OneDrive**: Sync folder dengan OneDrive
- **Dropbox**: Upload ke Dropbox
- **GitHub**: Push ke repository (jika sudah setup Git)

---

## 2. SETUP ENVIRONMENT (KOMPUTER BARU)

### 2.1 Install Software Pendukung

#### A. Download dan Install Laragon

1. **Download Laragon Full:**
   - Visit: https://laragon.org/download/
   - Download **"Laragon Full"** (sudah include PHP 8.3, MySQL 8.0, Apache)
   - File size: ~250MB

2. **Install Laragon:**
   ```
   1. Run installer sebagai Administrator
   2. Install di C:\laragon (default location)
   3. Selesai instalasi, jalankan Laragon
   4. Klik "Start All" untuk start Apache & MySQL
   ```

#### B. Verifikasi Installation

1. **Test Laragon:**
   - Buka browser: http://localhost
   - Harus muncul halaman Laragon

2. **Test PHP:**
   - Buka: http://localhost/phpinfo.php
   - Harus muncul info PHP 8.3

3. **Test MySQL:**
   - Buka: http://localhost/phpmyadmin
   - Login tanpa password (default Laragon)

#### C. Install Composer & Node.js

**Install Composer:**
```bash
# Download dari https://getcomposer.org/download/
# Atau jika Laragon sudah include Composer:
# Buka Terminal dari Laragon, test:
composer --version
```

**Install Node.js:**
```bash
# Download dari https://nodejs.org (LTS version)
# Setelah install, test:
node --version
npm --version
```

### 2.2 Setup Development Environment

#### A. Konfigurasi PHP

**Edit php.ini untuk project Laravel:**
```ini
# Buka C:\laragon\bin\php\php-8.3.x\php.ini
# Pastikan setting berikut:

max_execution_time = 300
memory_limit = 512M
post_max_size = 100M
upload_max_filesize = 100M
extension=pdo_mysql
extension=openssl
extension=mbstring
extension=tokenizer
extension=xml
extension=curl
extension=zip
extension=gd
```

#### B. Konfigurasi Apache

**Virtual Host akan otomatis dibuat oleh Laragon ketika menaruh project di www folder**

---

## 3. RESTORE PROJECT

### 3.1 Restore Source Code

#### A. Copy Project Files

```bash
# Copy project dari backup ke komputer baru
# 1. Copy folder backup ke C:\laragon\www\
# 2. Atau extract jika dalam bentuk zip/rar

# Struktur yang benar:
C:\laragon\www\Sistem-Informasi-Tahfidz-SMP\
├── app/
├── config/
├── database/
├── public/
├── resources/
├── routes/
├── storage/
├── vendor/ (akan diinstall ulang)
├── .env
├── composer.json
└── package.json
```

#### B. Set Permissions (Windows)

```bash
# Buka Command Prompt as Administrator
cd C:\laragon\www\Sistem-Informasi-Tahfidz-SMP

# Berikan permission untuk storage dan bootstrap/cache
icacls storage /grant Everyone:F /T
icacls bootstrap\cache /grant Everyone:F /T
```

### 3.2 Install Dependencies

#### A. Install PHP Dependencies

```bash
# Buka Terminal di folder project
cd C:\laragon\www\Sistem-Informasi-Tahfidz-SMP

# Install Composer dependencies
composer install

# Jika ada error, try:
composer install --ignore-platform-reqs
```

#### B. Install Node.js Dependencies

```bash
# Masih di folder project
npm install

# Jika ada error, try:
npm install --force
```

### 3.3 Konfigurasi Environment

#### A. Setup .env File

```bash
# Copy .env.example ke .env (jika .env tidak ada)
copy .env.example .env

# Edit .env file sesuai konfigurasi komputer baru
```

**Edit file .env:**
```env
APP_NAME="Sistem Informasi Tahfidz SMP"
APP_ENV=local
APP_KEY=base64:xxxx (akan digenerate)
APP_DEBUG=true
APP_URL=http://sistem-informasi-tahfidz-smp.test

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sistem_tahfidz_smp
DB_USERNAME=root
DB_PASSWORD=

# Session Configuration
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Other configurations...
```

#### B. Generate Application Key

```bash
# Generate APP_KEY baru
php artisan key:generate

# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### 3.4 Restore Database

#### A. Buat Database Baru

```sql
-- Buka phpMyAdmin (http://localhost/phpmyadmin)
-- Buat database baru:
CREATE DATABASE sistem_tahfidz_smp 
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

#### B. Import Database

**Cara 1: Menggunakan phpMyAdmin**
1. Buka phpMyAdmin
2. Select database `sistem_tahfidz_smp`
3. Klik tab **"Import"**
4. Choose File: pilih file `.sql` dari backup
5. Klik **"Go"**

**Cara 2: Menggunakan Command Line**
```bash
# Buka Command Prompt
cd C:\laragon\bin\mysql\mysql-8.0.30\bin

# Import database
mysql -u root -p sistem_tahfidz_smp < E:\Backup-Tahfidz-Project\database_backup.sql
```

#### C. Test Database Connection

```bash
# Test koneksi database
php artisan migrate:status

# Jika ada error, cek konfigurasi .env
# Jika perlu run migration (untuk database kosong):
# php artisan migrate
```

### 3.5 Build Assets

```bash
# Build CSS/JS assets
npm run build

# Untuk development:
npm run dev
```

### 3.6 Test Application

#### A. Start Local Server

```bash
# Method 1: Menggunakan Laragon (Recommended)
# 1. Start Laragon
# 2. Akses: http://sistem-informasi-tahfidz-smp.test

# Method 2: Menggunakan PHP built-in server
php artisan serve
# Akses: http://localhost:8000
```

#### B. Test Login

**Test dengan akun default:**
- **Admin**: username `admin`, password `password`
- **Guru**: username = NIY, password dari kartu login
- **Siswa**: username = NIS, password dari kartu login

---

## 4. TROUBLESHOOTING

### 4.1 Common Issues dan Solutions

#### Error: "Class not found" atau Autoload Issues

```bash
# Solution:
composer dump-autoload
php artisan clear-compiled
php artisan config:cache
```

#### Error: "No application encryption key"

```bash
# Solution:
php artisan key:generate
php artisan config:clear
```

#### Error: Database Connection

```bash
# Check:
1. MySQL service running? (Laragon -> MySQL -> Start)
2. Database exist? (Check phpMyAdmin)
3. .env configuration correct?
4. Test connection: php artisan tinker -> DB::connection()->getPdo();
```

#### Error: Storage Permission

```bash
# Windows Solution:
icacls storage /grant Everyone:F /T
icacls bootstrap\cache /grant Everyone:F /T

# Create missing directories:
mkdir storage\logs
mkdir storage\framework\cache
mkdir storage\framework\sessions
mkdir storage\framework\views
```

#### Error: NPM/Node Issues

```bash
# Clear npm cache:
npm cache clean --force

# Delete node_modules and reinstall:
rmdir /s node_modules
del package-lock.json
npm install
```

### 4.2 Verifikasi Migrasi Berhasil

**✅ CHECKLIST VERIFIKASI:**

- [ ] **Application Loading**: Homepage terbuka tanpa error
- [ ] **Database**: Data siswa, guru, kelompok tampil
- [ ] **Authentication**: Login admin, guru, siswa berhasil
- [ ] **File Permissions**: Storage folder writable
- [ ] **Assets**: CSS/JS loading dengan benar
- [ ] **Features**: Input absensi, setoran berfungsi
- [ ] **Reports**: Export Excel/PDF berfungsi

### 4.3 Performance Optimization

```bash
# Setelah migrasi berhasil, optimize:
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Install PHP extensions untuk performance:
# - OPcache (biasanya sudah enabled di Laragon)
# - Redis (optional, untuk caching)
```

---

## 5. BACKUP OTOMATIS (KOMPUTER BARU)

### 5.1 Setup Git Repository

```bash
# Initialize Git (jika belum):
git init
git add .
git commit -m "Initial commit after migration"

# Add remote repository:
git remote add origin https://github.com/username/sistem-tahfidz-smp.git
git push -u origin main
```

### 5.2 Database Backup Script

**Buat file `backup-database.bat`:**
```batch
@echo off
set MYSQL_PATH=C:\laragon\bin\mysql\mysql-8.0.30\bin
set BACKUP_PATH=D:\Backups\Database
set DATE=%date:~-4,4%%date:~-10,2%%date:~-7,2%

mkdir "%BACKUP_PATH%" 2>nul
"%MYSQL_PATH%\mysqldump.exe" -u root sistem_tahfidz_smp > "%BACKUP_PATH%\tahfidz_backup_%DATE%.sql"

echo Database backup completed: tahfidz_backup_%DATE%.sql
pause
```

### 5.3 Schedule Automated Backup

1. **Buka Task Scheduler** (Windows)
2. **Create Basic Task**:
   - Name: "Backup Database Tahfidz"
   - Trigger: Daily
   - Action: Start program `backup-database.bat`
3. **Set untuk run even if user not logged on**

---

## 6. TIPS PENTING

### 6.1 Preventive Measures

1. **Regular Backup**: Setup automated backup harian
2. **Version Control**: Selalu commit perubahan ke Git
3. **Cloud Storage**: Sync project folder dengan cloud
4. **Documentation**: Simpan panduan ini untuk referensi
5. **Test Restore**: Periodic test restore process

### 6.2 Emergency Contacts

**Jika ada masalah teknis:**
- **Laravel Documentation**: https://laravel.com/docs
- **Livewire Documentation**: https://livewire.laravel.com/docs
- **Stack Overflow**: Tag Laravel, Livewire, PHP
- **Laravel Community**: https://laracasts.com/discuss

---

## ⚠️ CRITICAL REMINDERS

1. **BACKUP DATABASE** adalah yang paling penting - tanpa ini data hilang
2. **File .env** berisi konfigurasi sensitif - jangan lupa backup
3. **Test semua fitur** setelah migrasi untuk memastikan tidak ada yang rusak
4. **Setup backup otomatis** segera setelah migrasi berhasil
5. **Simpan panduan ini** untuk migrasi future atau recovery

---

**🔥 URGENT: Jika komputer sudah sangat rusak dan tidak bisa booting:**

1. **Remove hard drive** dan pasang ke komputer lain sebagai external drive
2. **Copy files** langsung dari drive tersebut
3. **Priority order**: Database files > Source code > Config files
4. **Database location**: `C:\laragon\data\mysql\sistem_tahfidz_smp\` (folder database)

---

**Good luck dengan migrasi! Semoga project bisa diselamatkan dan berjalan lancar di komputer baru. 🙏**
