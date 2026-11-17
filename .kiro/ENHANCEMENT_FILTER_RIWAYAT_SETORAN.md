# Enhancement: Filter Riwayat Setoran dengan Shortcut Tanggal

## Masalah yang Diperbaiki

### 1. Bug: Date Picker Menyebabkan Filter Dropdown Hilang

**Kondisi Sebelum:**
- User klik tombol "Filter" → dropdown muncul
- User klik input "Tanggal Mulai" atau "Tanggal Selesai"
- Native date picker browser muncul
- ❌ **Dropdown filter hilang/tertutup otomatis** (bug)
- User harus buka filter lagi untuk melanjutkan

**Root Cause:**
- Alpine.js `@click.outside` atau `@click.away` mendeteksi klik di date picker (terutama dropdown month/year selector) sebagai "outside click"
- Dropdown langsung tertutup, terutama saat user klik selector bulan/tahun di calendar

**Solusi yang Dicoba:**
1. ❌ **Attempt 1:** `@click.away` + `@click.stop` di input → Masih gagal saat klik month/year selector
2. ✅ **Final Solution:** Backdrop approach (transparent overlay) dengan proper z-index layering

### 2. Enhancement: Tambah Shortcut Button untuk Rentang Tanggal

**Kondisi Sebelum:**
- User harus manual input tanggal mulai dan tanggal selesai
- Tidak user-friendly untuk filter cepat (misal: hari ini, 7 hari terakhir, dll)

**Kondisi Sesudah:**
- ✅ Tombol shortcut untuk rentang tanggal populer
- User bisa klik 1 tombol untuk set range langsung

## Perubahan

### 1. View Blade (Bug Fix + Enhancement)

**File:** `resources/views/livewire/shared/riwayat-setoran.blade.php`

#### A. Fix Alpine.js Event Handling dengan Backdrop

**Sebelum:**
```blade
<div x-data="{ open: false }" class="relative">
    <button @click="open = !open">...</button>
    
    <div x-show="open"
         @click.outside="open = false">  ← Bug: menutup saat klik date picker
         
        <input type="date" wire:model.live="tanggalMulai">  ← Trigger outside click
        <input type="date" wire:model.live="tanggalSelesai">
    </div>
</div>
```

**Sesudah (Backdrop Approach):**
```blade
<div x-data="{ open: false }" class="relative">
    <button @click="open = !open">...</button>
    
    {{-- Transparent backdrop untuk handle outside click --}}
    <div x-show="open"
         @click="open = false"
         class="fixed inset-0 z-40">  ← Backdrop layer di belakang dropdown
    </div>
    
    {{-- Dropdown content di atas backdrop --}}
    <div x-show="open"
         @click.stop  ← Prevent click propagation ke backdrop
         class="absolute z-50 ...">  ← Dropdown layer di atas backdrop
         
        {{-- Close button --}}
        <button @click="open = false">
            <svg><!-- X icon --></svg>
        </button>
         
        <input type="date" wire:model.live="tanggalMulai">
        <input type="date" wire:model.live="tanggalSelesai">
    </div>
</div>
```

**Penjelasan Arsitektur:**
1. **Backdrop Layer (z-40):** Transparent overlay yang menangkap klik di luar dropdown
2. **Dropdown Layer (z-50):** Dropdown content di atas backdrop
3. **`@click.stop` di dropdown:** Mencegah event propagasi ke backdrop saat klik di dalam dropdown
4. **Close button:** Alternatif cara menutup selain klik outside
5. **Date picker calendar:** Muncul native dari browser, tidak trigger close karena `@click.stop`

**Keuntungan Backdrop Approach:**
- ✅ Date picker tetap bisa dibuka dan tidak menutup dropdown
- ✅ Month/year selector di calendar tidak trigger close
- ✅ Klik di luar (backdrop) tetap bisa menutup dropdown
- ✅ Close button (X) sebagai alternatif
- ✅ Lebih robust daripada `@click.away` yang masih bisa ter-trigger date picker

#### B. Tambah Shortcut Buttons

**Ditambahkan sebelum input tanggal:**
```blade
{{-- Quick Date Range Shortcuts --}}
<div>
    <label class="block text-xs font-medium mb-2" style="color:#6B7280;">
        Rentang Cepat
    </label>
    <div class="grid grid-cols-2 gap-2">
        <button wire:click="setDateRange('today')">Hari Ini</button>
        <button wire:click="setDateRange('yesterday')">Kemarin</button>
        <button wire:click="setDateRange('last7days')">7 Hari</button>
        <button wire:click="setDateRange('last30days')">30 Hari</button>
        <button wire:click="setDateRange('thisMonth')">Bulan Ini</button>
        <button wire:click="setDateRange('lastMonth')">Bulan Lalu</button>
    </div>
</div>
```

### 2. Component PHP (Enhancement)

**File:** `app/Livewire/Shared/RiwayatSetoran.php`

**Method Baru:**
```php
/**
 * Set date range using shortcut buttons
 */
public function setDateRange(string $range): void
{
    switch ($range) {
        case 'today':
            $this->tanggalMulai = now()->format('Y-m-d');
            $this->tanggalSelesai = now()->format('Y-m-d');
            break;
        
        case 'yesterday':
            $this->tanggalMulai = now()->subDay()->format('Y-m-d');
            $this->tanggalSelesai = now()->subDay()->format('Y-m-d');
            break;
        
        case 'last7days':
            $this->tanggalMulai = now()->subDays(6)->format('Y-m-d');
            $this->tanggalSelesai = now()->format('Y-m-d');
            break;
        
        case 'last30days':
            $this->tanggalMulai = now()->subDays(29)->format('Y-m-d');
            $this->tanggalSelesai = now()->format('Y-m-d');
            break;
        
        case 'thisMonth':
            $this->tanggalMulai = now()->startOfMonth()->format('Y-m-d');
            $this->tanggalSelesai = now()->endOfMonth()->format('Y-m-d');
            break;
        
        case 'lastMonth':
            $this->tanggalMulai = now()->subMonth()->startOfMonth()->format('Y-m-d');
            $this->tanggalSelesai = now()->subMonth()->endOfMonth()->format('Y-m-d');
            break;
    }
    
    $this->resetPage();
}
```

## Testing

### Test 1: Bug Fix - Date Picker Tidak Menutup Filter

**Scenario A: Klik Date Input**
1. **Buka:** `http://127.0.0.1:8000/siswa/hasil-hafalan`
2. Klik tombol "Filter"
3. Klik input "Tanggal Mulai"
4. Date picker calendar muncul
5. ✅ **Dropdown filter tetap terbuka** (tidak hilang)
6. Pilih tanggal
7. ✅ **Filter masih terbuka, bisa lanjut pilih tanggal selesai**

**Scenario B: Klik Month/Year Selector di Calendar (Critical Test)**
1. Buka filter
2. Klik input "Tanggal Mulai"
3. Date picker calendar muncul
4. **Klik dropdown selector bulan** (atas calendar)
5. ✅ **Dropdown filter TETAP TERBUKA** (ini yang paling sering jadi masalah)
6. **Klik dropdown selector tahun** (atas calendar)
7. ✅ **Dropdown filter TETAP TERBUKA**
8. Pilih bulan/tahun
9. ✅ **Filter masih terbuka**

**Scenario C: Close Dropdown**
1. Buka filter
2. **Klik area di luar dropdown (backdrop transparan)**
3. ✅ **Dropdown tertutup**
4. Buka filter lagi
5. **Klik tombol X (close button) di header filter**
6. ✅ **Dropdown tertutup**

### Test 2: Shortcut Buttons

1. Buka filter
2. **Klik "Hari Ini":**
   - ✅ Tanggal Mulai = hari ini
   - ✅ Tanggal Selesai = hari ini
   - ✅ Data ter-filter otomatis

3. **Klik "7 Hari":**
   - ✅ Tanggal Mulai = 7 hari yang lalu
   - ✅ Tanggal Selesai = hari ini
   - ✅ Tampilkan setoran 7 hari terakhir

4. **Klik "Bulan Ini":**
   - ✅ Tanggal Mulai = tanggal 1 bulan ini
   - ✅ Tanggal Selesai = tanggal akhir bulan ini
   - ✅ Tampilkan semua setoran bulan ini

5. **Test untuk semua shortcut:**
   - Kemarin
   - 30 Hari
   - Bulan Lalu

### Test 3: Manual Input Masih Berfungsi

1. Buka filter
2. Klik input "Tanggal Mulai" → pilih tanggal manual
3. ✅ Filter tidak tertutup
4. Klik input "Tanggal Selesai" → pilih tanggal manual
5. ✅ Filter tidak tertutup
6. ✅ Data ter-filter sesuai range yang dipilih

## Keuntungan Enhancement

| Fitur | Sebelum | Sesudah |
|-------|---------|---------|
| **Filter Dropdown** | Hilang saat klik date picker ❌ | Tetap terbuka ✅ |
| **Input Tanggal** | Manual input saja | Manual + Shortcut ✅ |
| **User Experience** | Harus buka filter berulang | Sekali buka, semua selesai ✅ |
| **Kecepatan** | Lambat (manual input) | Cepat (1 klik) ✅ |

## Shortcut yang Tersedia

| Tombol | Rentang Tanggal | Kegunaan |
|--------|-----------------|----------|
| **Hari Ini** | Hari ini saja | Lihat setoran hari ini |
| **Kemarin** | Kemarin saja | Lihat setoran kemarin |
| **7 Hari** | 7 hari terakhir (termasuk hari ini) | Review mingguan |
| **30 Hari** | 30 hari terakhir (termasuk hari ini) | Review bulanan |
| **Bulan Ini** | 1 - 31 bulan berjalan | Laporan bulanan |
| **Bulan Lalu** | 1 - 31 bulan sebelumnya | Laporan bulan lalu |

## Rekomendasi

1. **Untuk developer lain:**
   - **Backdrop approach adalah solusi terbaik** untuk dropdown yang berisi interactive elements (date picker, select, dll)
   - Jangan gunakan `@click.away` atau `@click.outside` langsung jika ada native date picker di dalamnya
   - Pattern yang benar:
     - Backdrop layer (z-40) dengan `@click="close"`
     - Content layer (z-50) dengan `@click.stop`
     - Tambahkan close button (X) sebagai alternatif
   - Shortcut buttons sangat membantu UX untuk filter tanggal

2. **Untuk user:**
   - Gunakan shortcut button untuk filter cepat
   - Manual input tetap tersedia untuk custom range
   - Filter tidak akan tertutup saat interact dengan date picker (termasuk pilih bulan/tahun)
   - Klik di luar dropdown atau tombol X untuk menutup filter

## File yang Diubah

1. `resources/views/livewire/shared/riwayat-setoran.blade.php` - Fix Alpine.js + tambah shortcut buttons
2. `app/Livewire/Shared/RiwayatSetoran.php` - Method `setDateRange()`

## Aplikasi di Halaman

Perubahan ini berlaku untuk semua role yang menggunakan component ini:
- ✅ Siswa: `http://127.0.0.1:8000/siswa/hasil-hafalan`
- ✅ Guru: `http://127.0.0.1:8000/guru/riwayat-setoran`
- ✅ Admin: `http://127.0.0.1:8000/admin/riwayat-setoran`

## Tanggal
23 Agustus 2026
