# Bug Fix - Notifikasi Absensi Tersimpan Muncul Sebelum Waktunya

**Tanggal:** 23 Agustus 2026  
**File Diubah:** `app/Livewire/Admin/Absensi/InputAbsensi.php`  
**Severity:** Medium - UX Issue

---

## 🐛 Masalah yang Ditemukan

### Gejala:
Pada form input absensi dan setoran **individual** (per siswa):

**Skenario:**
1. ✅ Buka form untuk **Siswa A** → Input absensi → Simpan
2. ❌ Buka form untuk **Siswa B** → **Notifikasi langsung muncul**
3. ❌ Notifikasi: *"✔ Absensi tersimpan. Klik tombol kuning untuk input setoran siswa yang hadir."*
4. ❌ Padahal absensi **Siswa B belum disimpan**!

### Root Cause:

**State Variable:** `$justSaved`
```php
public bool $justSaved = false; // Flag untuk tracking apakah baru saja save
```

**Masalah:**
- ✅ Di-set `true` setelah simpan (di method `saveAbsensi`)
- ❌ **TIDAK di-reset** ke `false` saat modal dibuka untuk siswa berikutnya
- ❌ **TIDAK di-reset** saat modal ditutup
- ❌ **TIDAK di-reset** saat tanggal berubah

**Flow Bug:**
```
1. Siswa A: openInputAbsensi() → justSaved = false (default)
2. Siswa A: saveAbsensi() → justSaved = true ✓
3. Siswa A: closeModal() → justSaved = true (TIDAK DI-RESET!) ✗
4. Siswa B: openInputAbsensi() → justSaved = true (MASIH TRUE!) ✗
5. Siswa B: Notifikasi muncul padahal belum simpan ✗
```

---

## ✅ Solusi yang Diterapkan

### 1. Reset di `openInputAbsensi()`
**Kapan:** Setiap kali modal dibuka (untuk siswa baru atau siswa berbeda)

```php
public function openInputAbsensi(int $kelompokId, ?string $tanggal = null, ?int $siswaId = null): void
{
    $this->kelompokId = $kelompokId;
    $this->tanggal = $tanggal ?? today()->format('Y-m-d');
    $this->targetSiswaId = $siswaId;
    $this->isFlexibleDate = is_null($tanggal);
    
    $this->kelompok = Kelompok::findOrFail($this->kelompokId);
    
    $this->setoranSiswa = [];
    $this->justSaved = false; // ✅ RESET setiap kali modal dibuka
    
    // ... rest of code
}
```

**Alasan:** Setiap kali modal dibuka untuk siswa baru, state harus fresh/bersih.

---

### 2. Reset di `updatedTanggal()`
**Kapan:** Saat user mengganti tanggal di datepicker

```php
public function updatedTanggal()
{
    $this->justSaved = false; // ✅ RESET saat tanggal berubah
    $this->isMinggu = Carbon::parse($this->tanggal)->isSunday();
    
    // ... rest of code
}
```

**Alasan:** Ganti tanggal = load data absensi yang berbeda, notifikasi tidak relevan lagi.

---

### 3. Reset di `closeModal()`
**Kapan:** Saat modal ditutup (button X atau klik di luar modal)

```php
public function closeModal(): void
{
    $this->isOpen = false;
    $this->kelompok = null;
    $this->justSaved = false; // ✅ RESET saat modal ditutup
    $this->absensiSudahDisimpan = false; // ✅ RESET juga flag ini
}
```

**Alasan:** Cleanup state saat modal ditutup, siap untuk sesi berikutnya.

---

## 🔍 Kondisi Notifikasi (Logic di Blade)

### Di View: `input-absensi.blade.php`

```blade
@if($isLiburPilihan)
    <span class="text-purple-700 font-medium">
        🗓️ Hari libur telah tersimpan. Matikan toggle untuk mengisi absensi normal.
    </span>
@elseif($absensiSudahDisimpan && $justSaved)
    <span class="text-green-700 font-medium">
        ✔ Absensi tersimpan. Klik tombol kuning untuk input setoran siswa yang hadir.
    </span>
@else
    <span class="text-amber-600 font-medium">
        ⚡ Pilih status siswa untuk langsung aktifkan setoran.
    </span>
@endif
```

**Kondisi untuk Notifikasi Hijau:**
```php
$absensiSudahDisimpan && $justSaved
```

Kedua variable harus `true`:
1. `$absensiSudahDisimpan` → Data absensi ada di database
2. `$justSaved` → Baru saja simpan di sesi ini

---

## 🧪 Testing Skenario

### Skenario 1: Absensi Individual - Multiple Siswa
```
✅ Siswa A:
   - Buka form → Notifikasi: "Pilih status siswa..." (kuning)
   - Simpan absensi → Notifikasi: "Absensi tersimpan" (hijau)
   - Tutup form

✅ Siswa B:
   - Buka form → Notifikasi: "Pilih status siswa..." (kuning) ✓ BENAR
   - (TIDAK ADA notifikasi hijau sebelum simpan) ✓ FIXED
   - Simpan absensi → Notifikasi: "Absensi tersimpan" (hijau)
```

### Skenario 2: Ganti Tanggal
```
✅ Siswa A:
   - Buka form tanggal 23 Agustus → Simpan
   - Notifikasi hijau muncul
   - Ganti tanggal ke 24 Agustus
   - Notifikasi kembali ke kuning (karena belum simpan untuk tanggal 24) ✓ BENAR
```

### Skenario 3: Tutup dan Buka Kembali
```
✅ Siswa A:
   - Buka form → Simpan → Notifikasi hijau
   - Tutup form (X)
   - Buka form lagi untuk siswa yang sama
   - Jika ada data: Notifikasi kuning (karena justSaved = false)
   - Jika tidak ada data: Notifikasi kuning ✓ BENAR
```

---

## 📊 State Lifecycle

### Before (BUG):
```
[Open Siswa A] justSaved = false
[Save Siswa A]  justSaved = true
[Close Modal]   justSaved = true  ← NOT RESET!
[Open Siswa B]  justSaved = true  ← BUG! Shows wrong message
```

### After (FIXED):
```
[Open Siswa A] justSaved = false  ← RESET
[Save Siswa A]  justSaved = true
[Close Modal]   justSaved = false ← RESET
[Open Siswa B]  justSaved = false ← CORRECT! Fresh state
```

---

## 🎯 Kesimpulan

### ✅ Masalah Terselesaikan:
1. ✅ Notifikasi "Absensi tersimpan" tidak muncul sebelum waktunya
2. ✅ Setiap siswa mendapat state fresh saat modal dibuka
3. ✅ Ganti tanggal me-reset notifikasi dengan benar
4. ✅ Tutup modal me-cleanup state dengan sempurna

### ✅ Tempat Reset Ditambahkan:
1. ✅ `openInputAbsensi()` - Saat modal dibuka
2. ✅ `updatedTanggal()` - Saat tanggal berubah
3. ✅ `closeModal()` - Saat modal ditutup

### 🛡️ Proteksi State:
- State `justSaved` sekarang ter-manage dengan baik
- Lifecycle state lebih predictable
- UX lebih konsisten dan tidak membingungkan

---

## 📝 Catatan Tambahan

**Kenapa ada 2 variable?**
- `$absensiSudahDisimpan` → Data memang ada di database (bisa dari sesi sebelumnya)
- `$justSaved` → Baru saja simpan **di sesi ini** (untuk menampilkan notifikasi sukses)

**Kombinasi keduanya:**
- `absensiSudahDisimpan && justSaved` → Baru simpan, tampilkan notif hijau
- `absensiSudahDisimpan && !justSaved` → Data lama ada, tapi tidak tampilkan notif (normal flow)

**UX yang Diinginkan:**
- Notifikasi hijau **hanya** muncul **setelah** user klik simpan
- Notifikasi **tidak** muncul saat buka modal untuk siswa baru
- Notifikasi **tidak** muncul saat ganti tanggal sebelum simpan

---

**Status:** ✅ Bug Fixed - Notifikasi sekarang muncul pada kondisi yang tepat.
