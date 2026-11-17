# Bugfix: Form Absensi Tidak Muncul Setelah Pindah Kelompok

## Masalah

Siswa Akmal mengalami 2 masalah di halaman setoran kelompok (`http://127.0.0.1:8000/guru/kelompok-tahfidz/2/setoran`):

1. **Tanggal 1-15 Agustus 2024 tidak bisa diabsen** (menampilkan tanda silang ❌)
2. **Klik tanggal 19 Agustus ke depan tidak muncul form absensi/setoran**

## Data Riwayat Siswa

```
Nama: Akmal
Kelompok saat ini: Kelompok B (ID: 2) - Tingkat Menengah
Riwayat:
- 2024-07-01: Masuk Kelompok A (Dasar)
- 2024-08-17: Keluar dari Kelompok A (naik tingkat)
- 2024-08-19: Masuk Kelompok B (Menengah)
```

## Analisis

### Masalah 1: Tanggal 1-15 Tidak Bisa Diabsen ✅ BUKAN BUG

**Status:** Ini adalah perilaku yang BENAR.

**Penjelasan:**
- Logika `SiswaKelompokIntervalService` membatasi tanggal valid berdasarkan `riwayat_kelompoks`
- Record menunjukkan Akmal masuk ke Kelompok B pada `2024-08-19`
- Sistem dengan benar membatasi absensi mulai dari tanggal penempatan tersebut
- Tanggal sebelum 19 Agustus tidak valid karena siswa belum bergabung

**Solusi jika ingin mengabsen sejak 1 Agustus:**
Ubah tanggal penempatan di tabel `riwayat_kelompoks`:
```sql
UPDATE riwayat_kelompoks 
SET tanggal = '2024-08-01' 
WHERE siswa_id = [ID_AKMAL] 
AND kelompok_ke_id = 2 
AND tanggal = '2024-08-19';
```

### Masalah 2: Form Tidak Muncul ❌ BUG - SUDAH DIPERBAIKI

**Status:** 2 BUG ditemukan dan diperbaiki

#### Bug 2A: Logika Pengecekan Target Salah

**Root Cause:**  
Method `SetoranService::hasCompletedAllTargetHafalan()` menggunakan logika yang salah:
- Mengecek semua **SURAH** yang ada di juz target kelompok
- Membandingkan dengan **semua setoran "Lanjut"** siswa (dari kelompok manapun)
- Akmal punya setoran "Lanjut" di surah-surah yang kebetulan ada di Juz 29 (target Menengah) dari kelompok DASAR sebelumnya
- Sistem salah mengira Akmal sudah selesai target Menengah, sehingga `$isTargetComplete = true`
- Kondisi di blade (`@if($isTargetComplete)`) memblokir tampilnya tombol klik untuk input absensi

**Contoh Kasus:**
```
Target Kelompok Menengah: Juz 29
Capaian Akmal:
- Juz 30: 100% Tuntas (dari Kelompok Dasar)
- Juz 29: 0% (belum mulai di Kelompok Menengah)

Logika LAMA (SALAH):
- Cari surah di Juz 29: QS. Al-Mulk, QS. Al-Qalam, dst.
- Cek setoran "Lanjut" Akmal: ternyata QS. Al-Mulk sudah pernah "Lanjut" (dari Kelompok Dasar)
- Kesimpulan SALAH: Akmal sudah selesai target Kelompok Menengah ❌

Logika BARU (BENAR):
- Target Kelompok Menengah: Juz 29
- Cek tabel CapaianJuz untuk Juz 29: status_tuntas = 0, persentase = 0%
- Kesimpulan BENAR: Akmal belum selesai target Kelompok Menengah ✅
```

#### Bug 2B: Komponen InputAbsensi Tidak Dimuat

**Root Cause:**
View blade `dashboard-kelompok.blade.php` tidak me-load komponen `<livewire:admin.absensi.input-absensi />`:
- Event `@click="$dispatch('openInputAbsensi', ...)"` di-dispatch saat klik cell
- Tidak ada listener karena komponen InputAbsensi tidak ada di halaman
- Modal tidak pernah muncul

**Bukti:**
```blade
{{-- Tombol klik di cell --}}
<button @click="$dispatch('openInputAbsensi', { ... })">
    ...
</button>

{{-- TAPI tidak ada komponen yang mendengarkan event ini! --}}
{{-- SEHARUSNYA ada: --}}
<livewire:admin.absensi.input-absensi />
```

## Perbaikan

### Perbaikan 2A: SetoranService

**File:** `app/Services/SetoranService.php`  
**Method:** `hasCompletedAllTargetHafalan()`

**Perubahan:**
1. Ganti logika dari **"cek setoran per surah"** menjadi **"cek CapaianJuz per juz"**
2. Gunakan tabel `capaian_juzs` yang sudah menghitung progres per juz per siswa
3. Cek apakah semua juz target punya `status_tuntas = 1`

**Kode Lama:**
```php
// Cari semua surah di juz target
$allTargetSurahs = \App\Models\Surah::whereHas('juzs', function($q) use ($targetJuzIds) {
    $q->whereIn('juzs.id', $targetJuzIds);
})->pluck('id')->toArray();

// Cek surah yang sudah "Lanjut" (dari kelompok manapun!)
$completedSurahs = Setoran::join('absensis', 'setorans.absensi_id', '=', 'absensis.id')
    ->where('absensis.siswa_id', $siswaId)
    ->where('setorans.keputusan', 'Lanjut')
    ->pluck('setorans.surah_id')
    ->unique()
    ->toArray();

return empty(array_diff($allTargetSurahs, $completedSurahs));
```

**Kode Baru:**
```php
// Get progres hafalan untuk siswa ini
$progresHafalan = \App\Models\ProgresHafalan::where('siswa_id', $siswaId)->first();
if (!$progresHafalan) {
    return false;
}

// Cek berapa juz target yang sudah tuntas (status_tuntas = 1)
$completedJuzCount = \App\Models\CapaianJuz::where('progres_hafalan_id', $progresHafalan->id)
    ->whereIn('juz_id', $targetJuzIds)
    ->where('status_tuntas', 1)
    ->count();

// Semua juz target harus tuntas
return $completedJuzCount === count($targetJuzIds);
```

### Perbaikan 2B: Tambahkan Komponen InputAbsensi

**File:** `resources/views/livewire/admin/kelompok/dashboard-kelompok.blade.php`

**Penambahan di akhir file (sebelum `</div>` penutup):**
```blade
{{-- Modal Input Absensi & Setoran --}}
@if(auth()->guard('admin')->check())
    <livewire:admin.absensi.input-absensi />
@elseif(auth()->guard('guru')->check())
    <livewire:admin.absensi.input-absensi />
@endif
```

**Keuntungan:**
- Event `openInputAbsensi` sekarang ada listener-nya
- Modal akan muncul ketika cell diklik
- Support untuk admin dan guru (multi-guard)

## Testing

Setelah perbaikan, test berikut:

1. **Akmal di Kelompok B:**
   - Akses: `http://127.0.0.1:8000/guru/kelompok-tahfidz/2/setoran`
   - Klik tanggal 19 Agustus ke depan
   - ✅ Modal input absensi dan setoran harus muncul sekarang

2. **Siswa yang benar-benar tuntas:**
   - Pilih siswa dengan Juz 29 = 100% tuntas
   - ✅ Cell disabled dengan centang hijau (benar)

3. **Siswa pindah kelompok lain:**
   - ✅ Tidak ada regresi

4. **Test klik cell lain:**
   - Klik cell siswa lain yang belum tuntas
   - ✅ Modal harus muncul

## Dampak Perubahan

**Sebelum Fix:**
- Siswa yang pindah kelompok dengan overlap surah tidak bisa input setoran (form tidak muncul)
- Sistem salah menganggap target selesai padahal belum
- Event click tidak ditangani karena komponen tidak dimuat

**Setelah Fix:**
- Pengecekan berdasarkan juz spesifik kelompok (dari CapaianJuz)
- Lebih akurat karena CapaianJuz sudah diupdate oleh ProgresHafalanService
- Tidak terpengaruh oleh setoran di kelompok sebelumnya
- Modal muncul ketika cell diklik

## Rekomendasi

1. **Gunakan CapaianJuz sebagai sumber kebenaran** untuk status progres siswa
2. **Hindari query langsung ke tabel Setoran** untuk menentukan status tuntas
3. **Service ProgresHafalanService** sudah menangani kalkulasi kompleks antar juz/kelompok
4. **Pastikan komponen Livewire yang dibutuhkan selalu dimuat** di view

## Tanggal
23 Agustus 2026
