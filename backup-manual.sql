-- =====================================================
-- MANUAL DATABASE BACKUP COMMANDS
-- Sistem Informasi Tahfidz SMP
-- Database: tahfidz_absensi_test
-- =====================================================

-- 1. BACKUP MENGGUNAKAN COMMAND LINE
-- Buka Command Prompt, navigate ke MySQL bin directory, lalu jalankan:

-- mysqldump -u root -p tahfidz_absensi_test > backup_tahfidz_$(date +%Y%m%d_%H%M%S).sql

-- 2. BACKUP MENGGUNAKAN MySQL Workbench
-- File > Data Export > Select 'tahfidz_absensi_test' > Export to Self-Contained File

-- 3. BACKUP MENGGUNAKAN phpMyAdmin (RECOMMENDED)
-- 1. Buka http://localhost/phpmyadmin
-- 2. Klik database 'tahfidz_absensi_test'
-- 3. Klik tab 'Export'
-- 4. Pilih 'Quick' method, format 'SQL'
-- 5. Klik 'Go' untuk download

-- 4. BACKUP SPECIFIC TABLES (if needed)
-- mysqldump -u root -p tahfidz_absensi_test admins gurus siswas kelompoks > backup_core_tables.sql

-- 5. RESTORE COMMANDS (for future use)
-- mysql -u root -p tahfidz_absensi_test < backup_file.sql
