# Perbandingan Fitur Tempatkan Siswa

## 📋 Dua Jalur Penempatan Siswa

### 1️⃣ **Dari Halaman Siswa** (`/admin/master-data/siswa`)
**Flow:** Pilih Siswa → Pilih Kelompok

### 2️⃣ **Dari Halaman Kelompok** (`/admin/master-data/kelompok`)  
**Flow:** Pilih Kelompok → Pilih Siswa

---

## ⚙️ Mekanisme Backend (SUDAH SAMA ✅)

### Service Method yang Digunakan:
```php
// KEDUA FITUR MENGGUNAKAN METHOD YANG SAMA
\App\Services\MasterData\SiswaService::bulkPenempatan(
    $siswaIds,      // Array ID siswa
    $kelompokId,    // ID kelompok tujuan
    $tanggal,       // Tanggal penempatan
    $adminId        // ID admin yang melakukan
)
```

### Validasi yang Diterapkan (SAMA):
1. ✅ **Cek Kapasitas Kelompok**
   - Sisa slot harus >= jumlah siswa yang dipilih
   
2. ✅ **Cek Status Penempatan Siswa**
   - Siswa sudah di kelompok yang sama → ERROR: "Sudah ada di kelompok ini"
   - Siswa sudah di kelompok lain → ERROR: "Sudah ditempatkan di kelompok lain"
   
3. ✅ **Cek Jenis Kelamin**
   - Kelompok Banin → Hanya Laki-laki
   - Kelompok Banat → Hanya Perempuan
   
4. ✅ **Update Data**
   - Set `kelompok_id`, `status_penempatan`, `tanggal_penempatan`
   - Buat record di `riwayat_kelompoks`

---

## 🎯 Kriteria Filter Siswa (SUDAH SAMA ✅)

### Filter Otomatis (Diterapkan di Kedua Fitur):

| Kriteria | Halaman Siswa | Halaman Kelompok | Status |
|----------|---------------|------------------|--------|
| **Status Akademik** | `Aktif` | `Aktif` | ✅ SAMA |
| **Jenis Kelamin** | Auto-filter by kelompok | Auto-filter by kelompok | ✅ SAMA |
| **Status Penempatan (Default)** | Filter by tingkat siswa | `Belum Ditempatkan` | ⚠️ BEDA |

### Filter Manual (Opsional):

**Halaman Siswa:**
- Checkbox untuk pilih siswa
- Smart detection berdasarkan tingkat siswa
- Filter kelompok berdasarkan tingkat

**Halaman Kelompok:**
- Checkbox untuk pilih siswa
- Dropdown filter: "Belum Ditempatkan" / "Semua Siswa Aktif"
- Search: nama, NIS, NISN

---

## 🔍 Perbedaan Detail

### Halaman Siswa (Bulk Placement)

**Kelebihan:**
- Smart detection tingkat siswa
- Auto-filter kelompok berdasarkan tingkat
- Badge status per siswa
- Info warning jika siswa berbeda tingkat

**Logika:**
```php
// Di IndexSiswa.php - Render method
if (count($uniqueTingkat) === 1) {
    // Semua siswa sama tingkat → Filter kelompok by tingkat
    $kelompokListBulk = Kelompok::where('tingkat', $uniqueTingkat[0])->get();
} else {
    // Siswa beda tingkat → Tampilkan semua kelompok
    $kelompokListBulk = Kelompok::all();
}
```

### Halaman Kelompok (Tempatkan Siswa)

**Kelebihan:**
- Fokus pada 1 kelompok spesifik
- Info kelompok tujuan ditampilkan di atas
- Filter "Belum Ditempatkan" vs "Semua Siswa Aktif"
- Lebih cocok untuk workflow per-kelompok

**Logika:**
```php
// Di IndexKelompok.php - getAvailableSiswaQuery
$query = Siswa::where('status_akademik', 'Aktif');

// Default: Belum Ditempatkan saja
if ($this->filterStatusSiswa === 'belum_ditempatkan') {
    $query->where('status_penempatan', 'Belum Ditempatkan');
}

// Auto-filter jenis kelamin
if ($kelompok->jenis === 'Banin') {
    $query->where('jenis_kelamin', 'Laki-laki');
}
```

---

## ✅ Validasi Layer Ganda

### Layer 1: Filter di Frontend
- Hanya tampilkan siswa yang sesuai kriteria
- Filter jenis kelamin otomatis
- Filter status penempatan (opsional)

### Layer 2: Validasi di Backend (SiswaService)
- ✅ Cek siswa sudah di kelompok yang sama
- ✅ Cek siswa sudah di kelompok lain
- ✅ Cek jenis kelamin sesuai
- ✅ Cek kapasitas kelompok

**Kenapa 2 Layer?**
- Frontend: UX yang baik (user tidak lihat opsi invalid)
- Backend: Security & data integrity (mencegah bypass/manipulation)

---

## 🎯 Kesimpulan

### ✅ Yang Sudah SAMA:
1. ✅ Service method yang digunakan: `bulkPenempatan()`
2. ✅ Validasi backend: kapasitas, jenis kelamin, status penempatan
3. ✅ Update data: kelompok_id, status, tanggal, riwayat
4. ✅ Filter jenis kelamin otomatis sesuai kelompok
5. ✅ Hanya siswa aktif yang bisa ditempatkan

### ⚠️ Yang BERBEDA (By Design):
1. **Filter default:**
   - Siswa: Smart filter by tingkat
   - Kelompok: Default "Belum Ditempatkan"
   
2. **Use Case:**
   - Siswa: Tempatkan banyak siswa sekaligus
   - Kelompok: Tambah anggota ke kelompok spesifik

### 🛡️ Proteksi yang Sama:
- ✅ Tidak bisa tempatkan siswa ke kelompok yang sama (duplikasi)
- ✅ Tidak bisa tempatkan siswa yang sudah ada di kelompok lain
- ✅ Tidak bisa tempatkan siswa dengan jenis kelamin salah
- ✅ Tidak bisa tempatkan jika kapasitas penuh

---

## 📝 Rekomendasi Penggunaan

### Gunakan Halaman Siswa Jika:
- Ingin tempatkan banyak siswa baru sekaligus
- Siswa memiliki tingkat yang sama
- Workflow: Pendaftaran awal siswa

### Gunakan Halaman Kelompok Jika:
- Ingin menambah anggota ke kelompok tertentu
- Fokus pada 1 kelompok spesifik
- Workflow: Mengisi kelompok yang belum penuh

---

**Catatan:** Kedua fitur menggunakan validasi backend yang sama, sehingga data integrity terjaga dengan baik terlepas dari jalur mana yang digunakan.
