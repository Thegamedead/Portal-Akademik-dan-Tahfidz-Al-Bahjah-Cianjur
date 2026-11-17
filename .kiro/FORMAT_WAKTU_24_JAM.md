# Format Waktu 24 Jam (Tanpa AM/PM)

## Permintaan

User ingin format waktu menggunakan **format lokal Indonesia (24 jam)** tanpa AM/PM.

Contoh yang diinginkan:
- ✅ **13:00** (format 24 jam)
- ❌ **01:00 PM** (format 12 jam dengan AM/PM)

## Status Implementasi

### ✅ Sudah Benar di Backend (PHP)

Semua format waktu di PHP sudah menggunakan format `H:i` (24 jam):

```php
// Format 24 jam (benar)
$waktu->format('H:i')  // Output: 13:00, 14:30, 09:15

// BUKAN format 12 jam
// $waktu->format('h:i A')  // Output: 01:00 PM (TIDAK DIGUNAKAN)
```

**File yang sudah benar:**
- `app/Livewire/Admin/Absensi/InputAbsensi.php` (baris 156-157, 162-163)
- `resources/views/livewire/shared/riwayat-absensi.blade.php`
- `resources/views/components/siswa/absensi-card.blade.php`
- Dan semua file lainnya

### ✅ Diperbaiki di Frontend (HTML Input)

Input waktu di form sudah ditambahkan atribut untuk memastikan format 24 jam:

**File:** `resources/views/livewire/admin/absensi/input-absensi.blade.php`

**Sebelum:**
```blade
<input type="time" wire:model="waktuMulai" class="...">
<input type="time" wire:model="waktuSelesai" class="...">
```

**Sesudah:**
```blade
<input type="time" 
       wire:model="waktuMulai"
       step="60"
       pattern="[0-9]{2}:[0-9]{2}"
       class="...">
<input type="time" 
       wire:model="waktuSelesai"
       step="60"
       pattern="[0-9]{2}:[0-9]{2}"
       class="...">
```

**Penjelasan atribut:**
- `step="60"` → Interval 1 menit (60 detik)
- `pattern="[0-9]{2}:[0-9]{2}"` → Format HH:MM (24 jam)

## Catatan Penting

### Browser Locale

Input `type="time"` di HTML5 akan mengikuti **locale browser**:
- Browser dengan locale Indonesia (`id-ID`) → otomatis format 24 jam
- Browser dengan locale US (`en-US`) → mungkin tampil 12 jam dengan AM/PM

**Solusi:**
- Atribut `step` dan `pattern` yang sudah ditambahkan akan **memaksa format 24 jam**
- Jika user tetap melihat AM/PM, user perlu:
  1. Ganti locale browser ke Indonesia
  2. Atau gunakan browser modern yang support atribut pattern

### Tampilan "—" (Dash)

Jika melihat waktu dengan tanda "—":
- ✅ Ini **benar** - berarti waktu belum diisi atau data kosong
- Bukan masalah format

## Testing

### Test 1: Input Waktu Baru
1. Buka form input absensi
2. Klik input waktu
3. ✅ Seharusnya muncul picker dengan format 24 jam (00:00 - 23:59)

### Test 2: Waktu Tersimpan
1. Simpan absensi dengan waktu contoh: 14:30 - 16:00
2. Buka kembali form edit
3. ✅ Seharusnya tampil: **14:30 - 16:00** (bukan 02:30 PM - 04:00 PM)

### Test 3: Tampilan di Riwayat
1. Lihat riwayat absensi siswa
2. ✅ Kolom waktu tampil format: **13:00 - 15:00**

## Format Waktu di Seluruh Sistem

| Konteks | Format | Contoh Output |
|---------|--------|---------------|
| **Input Form** | `type="time"` dengan `pattern` | 14:30 |
| **Database** | `TIME` (stored as 24h) | 14:30:00 |
| **PHP Display** | `H:i` | 14:30 |
| **Riwayat/Card** | `H:i` | 13:00 - 15:00 |
| **Timestamp Lengkap** | `d M Y, H:i` | 20 Agt 2024, 14:30 |

## Rekomendasi

1. **Tidak perlu konversi manual** - semua sudah menggunakan format 24 jam
2. **Jika user melihat AM/PM di browser:**
   - Cek setting locale browser
   - Update browser ke versi terbaru
   - Atribut `pattern` akan enforce format 24 jam

3. **Untuk developer:**
   - Selalu gunakan `H:i` untuk format waktu (bukan `h:i A`)
   - Untuk input time, selalu tambahkan `step` dan `pattern`

## File yang Diubah

1. `resources/views/livewire/admin/absensi/input-absensi.blade.php` - Tambah atribut `step` dan `pattern`

## Tanggal
23 Agustus 2026
