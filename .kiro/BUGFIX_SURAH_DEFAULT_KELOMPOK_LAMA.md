# Bugfix: Surah Default dari Kelompok Lama Ter-load Otomatis

## Masalah

Setelah siswa pindah kelompok, ketika membuka form setoran baru:
- **Surah default otomatis mengarah ke surah terakhir dari kelompok sebelumnya**
- Contoh: Akmal pindah dari Kelompok Dasar (target Juz 30) ke Kelompok Menengah (target Juz 29)
- Ketika buka form setoran di Kelompok Menengah, surah default = **An-Nas (Juz 30)** ❌
- Seharusnya: **kosong atau sesuai kelompok baru (Juz 29)** ✅

## Root Cause

Method `SetoranService::getLastSaranAyat()` mengambil setoran terakhir siswa **tanpa memfilter berdasarkan kelompok**.

### Kode Lama (Bermasalah)

```php
public function getLastSaranAyat(int $siswaId): ?array
{
    $lastSetoran = Setoran::whereHas('absensi', function ($query) use ($siswaId) {
        $query->where('siswa_id', $siswaId);
        // ❌ Tidak ada filter kelompok!
    })
    ->whereNotNull('saran_juz_id')
    ->orderBy('created_at', 'desc')
    ->first();
    
    // ...
}
```

**Akibatnya:**
- Query mengambil setoran terakhir dari **seluruh riwayat siswa** (semua kelompok)
- Jika siswa terakhir setoran di Kelompok Dasar (An-Nas), maka An-Nas ter-load sebagai default
- Tidak sesuai dengan target kelompok baru

## Perbaikan

### 1. Tambahkan Parameter `kelompokId` di SetoranService

**File:** `app/Services/SetoranService.php`  
**Method:** `getLastSaranAyat()`

**Perubahan:**
```php
// Signature method diubah (tambah parameter opsional)
public function getLastSaranAyat(int $siswaId, ?int $kelompokId = null): ?array

// Query ditambahkan filter kelompok
$query = Setoran::whereHas('absensi', function ($q) use ($siswaId, $kelompokId) {
    $q->where('siswa_id', $siswaId);
    
    // ✅ Filter by kelompok if provided
    if ($kelompokId !== null) {
        $q->where('kelompok_id', $kelompokId);
    }
})
->whereNotNull('saran_juz_id')
->orderBy('created_at', 'desc');

$lastSetoran = $query->first();
```

**Keuntungan:**
- Parameter opsional (`?int $kelompokId = null`) = **backward compatible**
- Jika `$kelompokId` tidak disertakan, behavior tetap sama (global)
- Jika `$kelompokId` disertakan, hanya ambil dari kelompok tersebut

### 2. Update Pemanggilan di InputSetoran

**File:** `app/Livewire/Admin/Setoran/InputSetoran.php`  
**Method:** `loadStudentData()`

**Sebelum:**
```php
$saran = $setoranService->getLastSaranAyat($this->currentSiswaId);
```

**Sesudah:**
```php
// Pass kelompokId to get suggestion only from current kelompok
$saran = $setoranService->getLastSaranAyat($this->currentSiswaId, $this->kelompokId);
```

### 3. Update Pemanggilan di ModalSetoran

**File:** `app/Livewire/Admin/Setoran/ModalSetoran.php`  
**Method:** (di dalam `openSetoranModal`)

**Sebelum:**
```php
$saran = $setoranService->getLastSaranAyat($this->siswaId);
```

**Sesudah:**
```php
// Pass kelompokId to get suggestion only from current kelompok
$saran = $setoranService->getLastSaranAyat($this->siswaId, $this->kelompokId);
```

## Testing

### Skenario Test 1: Siswa Pindah Kelompok

1. **Setup:**
   - Siswa A di Kelompok Dasar (Juz 30)
   - Terakhir setoran: An-Nas (114) ayat 1-6
   - Siswa A pindah ke Kelompok Menengah (Juz 29)

2. **Buka form setoran di Kelompok Menengah:**
   - ✅ Surah default harus **kosong** atau sesuai Juz 29
   - ❌ BUKAN An-Nas (Juz 30)

3. **Jika sudah ada setoran di Kelompok Menengah:**
   - ✅ Surah default = setoran terakhir **di Kelompok Menengah**

### Skenario Test 2: Siswa Belum Pindah Kelompok

1. **Setup:**
   - Siswa B di Kelompok Dasar (Juz 30)
   - Terakhir setoran: An-Nas (114) ayat 1-6

2. **Buka form setoran:**
   - ✅ Surah default = An-Nas (114) ayat 7-6 (saran lanjutan)
   - Behavior tetap sama seperti sebelumnya

### Skenario Test 3: Siswa Baru (Belum Ada Setoran)

1. **Setup:**
   - Siswa C baru masuk Kelompok Menengah
   - Belum pernah setoran

2. **Buka form setoran:**
   - ✅ Surah default = kosong (form kosong)
   - Behavior tetap sama seperti sebelumnya

## Dampak Perubahan

### Sebelum Fix:
- Form setoran menampilkan surah dari kelompok lama
- Guru/admin harus manual ganti surah sesuai target kelompok baru
- Rawan salah input (setoran surah yang tidak sesuai target)

### Setelah Fix:
- Form setoran hanya menampilkan saran dari kelompok yang sama
- Jika siswa baru pindah kelompok, form kosong (atau saran pertama dari kelompok baru)
- Lebih aman dan sesuai dengan target kelompok

### Backward Compatibility:
- ✅ Parameter `$kelompokId` bersifat **opsional**
- ✅ Jika tidak disertakan, behavior tetap sama (global)
- ✅ Tidak ada breaking change untuk pemanggilan lama

## File yang Diubah

1. `app/Services/SetoranService.php` - Method `getLastSaranAyat()`
2. `app/Livewire/Admin/Setoran/InputSetoran.php` - Method `loadStudentData()`
3. `app/Livewire/Admin/Setoran/ModalSetoran.php` - Event `openSetoranModal`

## Rekomendasi

1. **Selalu sertakan `kelompokId`** saat memanggil `getLastSaranAyat()` untuk context-aware suggestion
2. **Jangan hardcode surah default** di form - biarkan service yang tentukan berdasarkan riwayat
3. **Validasi surah dengan target kelompok** sebelum simpan setoran

## Tanggal
23 Agustus 2026
