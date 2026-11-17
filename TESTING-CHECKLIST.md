# Testing Checklist - Sistem Informasi Tahfidz SMP

## 🎯 Testing Setelah Refactoring

Gunakan checklist ini untuk memverifikasi bahwa semua perubahan berfungsi dengan baik.

---

## 1. Master Data Siswa (`/admin/master-data/siswa`)

### A. Halaman List Siswa
- [ ] Halaman dapat diakses tanpa error
- [ ] Tabel siswa muncul dengan data yang benar
- [ ] Pagination berfungsi (klik halaman 2, 3, dst)
- [ ] Jumlah total siswa ditampilkan dengan benar

### B. Search & Filter
- [ ] **Search by NIS** - Ketik NIS siswa, hasil filter sesuai
- [ ] **Search by NISN** - Ketik NISN siswa, hasil filter sesuai
- [ ] **Search by Nama** - Ketik nama siswa, hasil filter sesuai
- [ ] **Filter Status Akademik** - Pilih status, hasil sesuai
- [ ] **Filter Status Tahfidz** - Pilih status, hasil sesuai
- [ ] **Filter Status Penempatan** - Pilih status, hasil sesuai
- [ ] **Filter Jenis Kelamin** - Pilih jenis kelamin, hasil sesuai
- [ ] **Filter Kelas** - Pilih kelas, hasil sesuai
- [ ] **Reset Filter** - Klik reset, semua filter kembali normal

### C. Selection & Checkbox
- [ ] **Select All** - Centang "Select All", semua siswa di halaman terpilih
- [ ] **Unselect All** - Uncheck "Select All", semua siswa di halaman tidak terpilih
- [ ] **Select Individual** - Centang siswa satu per satu, counter berubah
- [ ] **Cross-page Selection** - Pilih di halaman 1, pindah ke halaman 2, pilihan tetap ada
- [ ] Jumlah siswa terpilih ditampilkan dengan benar

### D. Form Tambah Siswa
- [ ] Klik tombol "Tambah", modal form muncul
- [ ] **Modal tanpa backdrop gelap** ✅
- [ ] Form memiliki semua field yang diperlukan
- [ ] Validation berfungsi (NIS required, format benar)
- [ ] Submit form berhasil, siswa baru masuk database
- [ ] Toast notification "Siswa berhasil didaftarkan" muncul
- [ ] Modal tertutup setelah submit
- [ ] Tabel refresh dan siswa baru muncul

### E. Form Edit Siswa
- [ ] Pilih 1 siswa, klik "Edit", modal form muncul
- [ ] **Modal tanpa backdrop gelap** ✅
- [ ] Form terisi dengan data siswa yang dipilih
- [ ] Ubah data, submit berhasil
- [ ] Toast notification "Data siswa berhasil diperbarui" muncul
- [ ] Modal tertutup, data di tabel terupdate

### F. Form Detail Siswa
- [ ] Klik nama siswa atau tombol detail
- [ ] **Modal detail tanpa backdrop gelap** ✅
- [ ] Semua informasi siswa ditampilkan lengkap
- [ ] Data kelompok dan kelas ditampilkan (jika ada)
- [ ] Tombol tutup berfungsi

### G. Hapus Siswa (Single)
- [ ] Pilih 1 siswa, klik "Hapus"
- [ ] Modal konfirmasi muncul
- [ ] Klik "Ya", siswa terhapus
- [ ] Toast notification "Siswa berhasil dihapus" muncul
- [ ] Siswa hilang dari tabel

### H. Hapus Bulk
- [ ] Pilih beberapa siswa (2+)
- [ ] Klik "Hapus"
- [ ] **Modal konfirmasi tanpa backdrop gelap** ✅
- [ ] Modal menampilkan jumlah siswa yang akan dihapus
- [ ] Klik "Ya", semua siswa terpilih terhapus
- [ ] Toast notification dengan jumlah siswa muncul

### I. Bulk Penempatan Kelompok
- [ ] Pilih beberapa siswa yang belum ditempatkan
- [ ] Klik "Tempatkan Kelompok"
- [ ] **Modal penempatan tanpa backdrop gelap** ✅
- [ ] Dropdown kelompok muncul dengan rekomendasi yang tepat
- [ ] Jika siswa satu tingkat, kelompok di-filter sesuai tingkat
- [ ] Pilih kelompok, pilih tanggal penempatan
- [ ] Submit berhasil, siswa ditempatkan ke kelompok
- [ ] Toast notification "X siswa berhasil ditempatkan" muncul
- [ ] Status penempatan siswa berubah

### J. Bulk Pindah Kelas
- [ ] Pilih beberapa siswa
- [ ] Klik "Pindah Kelas"
- [ ] **Modal pindah kelas tanpa backdrop gelap** ✅
- [ ] Pilih kelas tujuan
- [ ] Submit berhasil
- [ ] Toast notification muncul
- [ ] Kelas siswa berubah di tabel

### K. Registrasi Masuk (jika ada)
- [ ] Pilih 1 siswa aktif
- [ ] Klik "Registrasi Masuk"
- [ ] **Modal registrasi masuk tanpa backdrop gelap** ✅
- [ ] Pilih status masuk (Baru/Pindahan)
- [ ] Jika pindahan, field asal sekolah wajib
- [ ] Submit berhasil
- [ ] Data registrasi masuk tersimpan

### L. Registrasi Keluar
- [ ] Pilih siswa aktif
- [ ] Klik "Registrasi Keluar"
- [ ] Modal registrasi keluar muncul (bg-transparent, OK)
- [ ] Pilih jenis keluar (Lulus/Pindah/dll)
- [ ] Field conditional muncul sesuai jenis
- [ ] Submit, modal konfirmasi muncul (bg-transparent, OK)
- [ ] Konfirmasi, proses berhasil
- [ ] Status siswa berubah menjadi non-aktif

### M. Luluskan Kelas 9
- [ ] Klik "Luluskan Kelas 9"
- [ ] Sistem otomatis select semua siswa kelas 9 aktif
- [ ] Modal registrasi keluar muncul dengan siswa yang terpilih
- [ ] Proses registrasi keluar massal
- [ ] Semua siswa kelas 9 berubah status

---

## 2. Master Data Siswa Non-Aktif (`/admin/master-data/siswa/non-aktif`)

### A. Halaman List
- [ ] Halaman dapat diakses
- [ ] Hanya siswa non-aktif yang muncul
- [ ] Filter status akademik berfungsi
- [ ] Search berfungsi

### B. Batalkan Status Non-Aktif
- [ ] Pilih siswa non-aktif
- [ ] Klik "Batalkan"
- [ ] **Modal konfirmasi tanpa backdrop gelap** (already using bg-transparent) ✅
- [ ] Konfirmasi, siswa kembali aktif
- [ ] Siswa hilang dari list non-aktif
- [ ] Siswa muncul kembali di list aktif

---

## 3. Master Data Kelas (`/admin/master-data/kelas`)

### A. Form Tambah/Edit Kelas
- [ ] Klik "Tambah"
- [ ] **Modal form tanpa backdrop gelap** ✅
- [ ] Form memiliki field: Tingkat, Nama Kelas, Ruang, Wali Kelas
- [ ] Validation berfungsi
- [ ] Submit berhasil, kelas baru muncul
- [ ] Edit kelas berfungsi

### B. Hapus Kelas
- [ ] Pilih kelas, klik "Hapus"
- [ ] **Modal konfirmasi tanpa backdrop gelap** ✅
- [ ] Konfirmasi, kelas terhapus

### C. Anggota Kelompok
- [ ] Pilih 1 kelas, klik "Anggota Kelompok"
- [ ] **Modal anggota tanpa backdrop gelap** ✅
- [ ] List anggota kelas muncul
- [ ] Data ditampilkan dengan benar

---

## 4. Master Data Guru (`/admin/master-data/guru`)

### A. Form Tambah/Edit Guru
- [ ] Klik "Tambah"
- [ ] **Modal form tanpa backdrop gelap** ✅
- [ ] Form validation styling benar:
  - [ ] Field NIY border merah jika error
  - [ ] Field Nama border merah jika error
  - [ ] Field Tempat Lahir border merah jika error
  - [ ] Field Jabatan border merah jika error
  - [ ] Field No Telepon border merah jika error
  - [ ] Field Email border merah jika error
- [ ] Submit form berhasil
- [ ] Edit guru berfungsi

### B. Hapus Guru
- [ ] Hapus single guru berfungsi
- [ ] **Modal hapus bulk tanpa backdrop gelap** ✅

### C. Registrasi Keluar Guru
- [ ] Pilih guru aktif
- [ ] Klik "Registrasi Keluar"
- [ ] **Modal registrasi keluar tanpa backdrop gelap** ✅
- [ ] Proses berhasil

---

## 5. Master Data Kelompok (`/admin/master-data/kelompok`)

### Testing untuk halaman ini masih pending
- [ ] Modal tambah/edit kelompok
- [ ] Modal hapus kelompok
- [ ] Modal anggota kelompok
- [ ] Dropdown menu Pengaturan:
  - [ ] Kapasitas Kelompok
  - [ ] Target Hafalan
  - [ ] Komponen Penilaian
- [ ] Halaman Setoran Hafalan

---

## 6. Usulan Kenaikan Tingkat (`/admin/usulan-kenaikan-tingkat`)

### Testing untuk halaman ini masih pending
- [ ] Modal review usulan
- [ ] Modal detail usulan
- [ ] Modal cancel usulan
- [ ] Modal delete usulan

---

## 7. Kelulusan Program (`/admin/kelulusan-program`)

### Testing untuk halaman ini masih pending
- [ ] Modal-modal di halaman kelulusan

---

## 📊 Test Summary

| Module | Total Tests | Passed | Failed | Skipped |
|--------|-------------|--------|--------|---------|
| Siswa List | 13 | ⬜ | ⬜ | ⬜ |
| Siswa Forms | 4 | ⬜ | ⬜ | ⬜ |
| Siswa Bulk Actions | 4 | ⬜ | ⬜ | ⬜ |
| Siswa Non-Aktif | 2 | ⬜ | ⬜ | ⬜ |
| Kelas | 3 | ⬜ | ⬜ | ⬜ |
| Guru | 3 | ⬜ | ⬜ | ⬜ |
| Kelompok | 0 | ⬜ | ⬜ | ⬜ |
| Usulan | 0 | ⬜ | ⬜ | ⬜ |
| Kelulusan | 0 | ⬜ | ⬜ | ⬜ |

---

## 🐛 Bug Report Template

Jika menemukan bug, gunakan template ini:

```
**Halaman:** /admin/...
**Action:** Klik tombol ...
**Expected:** Seharusnya ...
**Actual:** Yang terjadi adalah ...
**Steps to Reproduce:**
1. Buka halaman ...
2. Klik ...
3. ...

**Screenshots:** (jika ada)
**Console Errors:** (jika ada)
```

---

## ✅ Sign-off

**Tested by:** ___________________  
**Date:** ___________________  
**Status:** [ ] PASSED / [ ] FAILED  
**Notes:** ___________________

---

**Important Notes:**
1. ✅ = Sudah diperbaiki, harusnya tidak ada backdrop
2. ⬜ = Belum dites
3. ✔️ = Test passed
4. ❌ = Test failed
5. ⚠️ = Test passed dengan catatan
