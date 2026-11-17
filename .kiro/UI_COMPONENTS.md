# 📦 Panduan Komponen UI - Sistem Informasi Tahfidz

> **Status**: ✅ Phase 3 Completed  
> **Tanggal**: {{ now()->isoFormat('D MMMM Y') }}

## 📋 Daftar Komponen

### 1. **Status Badge** (`<x-ui.status-badge>`)
Badge untuk menampilkan status dengan warna otomatis berdasarkan status akademik, tahfidz, kehadiran, dll.

**Props:**
- `type`: Type status (default: 'default')
- `status`: Nilai status yang akan ditampilkan
- `size`: Ukuran badge ('sm', 'md', 'lg')
- `customColors`: Custom color array `['bg' => '#hex', 'text' => '#hex']`

**Contoh Penggunaan:**
```blade
{{-- Status Akademik --}}
<x-ui.status-badge status="Aktif" />
<x-ui.status-badge status="Lulus" size="lg" />

{{-- Status Kehadiran --}}
<x-ui.status-badge status="Hadir" />
<x-ui.status-badge status="Alfa" size="sm" />

{{-- Custom Colors --}}
<x-ui.status-badge 
    status="Custom Status" 
    :custom-colors="['bg' => '#D1FAE5', 'text' => '#065F46']" 
/>
```

**Status yang Didukung:**
- **Akademik**: Aktif, Cuti, Lulus, Keluar, Non-Aktif
- **Tahfidz**: Belum Mulai, Sedang Berjalan, Lulus Program Tahfidz
- **Kehadiran**: Hadir, Izin, Sakit, Alfa
- **Penempatan**: Belum Ditempatkan, Sudah Ditempatkan
- **Usulan**: Diajukan, Disetujui, Ditolak
- **Setoran**: Lanjut, Ulang
- **Tes**: Belum Tes, Sudah Tes, Perlu Review

---

### 2. **Progress Bar** (`<x-ui.progress-bar>`)
Progress bar dengan berbagai warna dan ukuran.

**Props:**
- `percentage`: Nilai progress (0-100)
- `color`: Warna bar ('primary', 'blue', 'green', 'purple', 'yellow', 'amber', 'red', 'custom')
- `size`: Ukuran bar ('sm', 'md', 'lg')
- `showLabel`: Tampilkan label dan persentase (boolean)
- `animate`: Animasi transisi (boolean, default: true)
- `customColor`: Custom hex color

**Contoh Penggunaan:**
```blade
{{-- Basic --}}
<x-ui.progress-bar :percentage="75" />

{{-- Dengan Label --}}
<x-ui.progress-bar :percentage="$progres" color="green" show-label>
    Progres Hafalan
</x-ui.progress-bar>

{{-- Custom Color --}}
<x-ui.progress-bar 
    :percentage="90" 
    size="lg" 
    custom-color="#1B5E4F" 
/>
```

---

### 3. **Stat Card** (`<x-ui.stat-card>`)
Card statistik dengan icon dan nilai.

**Props:**
- `title`: Judul card
- `value`: Nilai utama yang ditampilkan
- `subtitle`: Subtitle/keterangan tambahan
- `icon`: SVG icon (string)
- `iconColor`: Warna background icon ('blue', 'green', 'purple', 'yellow', 'amber', 'red', 'zamrud')
- `trend`: Trend indicator ('up', 'down', 'neutral')
- `trendValue`: Nilai trend

**Contoh Penggunaan:**
```blade
<x-ui.stat-card 
    title="Juz Tuntas" 
    :value="$juzTuntas"
    subtitle="Target: 30 Juz"
    icon-color="blue"
>
    <x-slot:icon>
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
        </svg>
    </x-slot:icon>
</x-ui.stat-card>

{{-- Dengan Trend --}}
<x-ui.stat-card 
    title="Setoran Bulan Ini" 
    value="45"
    trend="up"
    trend-value="+12%"
    icon-color="green"
/>
```

---

### 4. **Empty State** (`<x-ui.empty-state>`)
Tampilan untuk kondisi data kosong.

**Props:**
- `icon`: Icon type ('inbox', 'calendar', 'book', 'users', 'document', 'search', 'chart')
- `title`: Judul empty state
- `description`: Deskripsi tambahan
- `action`: Wire click action
- `actionText`: Text tombol action
- `actionRoute`: Route untuk link action
- `size`: Ukuran ('sm', 'md', 'lg')

**Contoh Penggunaan:**
```blade
{{-- Basic --}}
<x-ui.empty-state 
    icon="book" 
    title="Belum Ada Setoran"
    description="Belum ada data setoran untuk bulan ini"
/>

{{-- Dengan Action --}}
<x-ui.empty-state 
    icon="users" 
    title="Belum Ada Siswa"
    action-route="{{ route('admin.siswa.create') }}"
    action-text="Tambah Siswa"
/>

{{-- Custom Content --}}
<x-ui.empty-state icon="calendar" title="Belum Ada Jadwal">
    <a href="{{ route('admin.jadwal') }}" class="text-zamrud-600">
        Buat Jadwal Sekarang →
    </a>
</x-ui.empty-state>
```

---

### 5. **Alert Box** (`<x-ui.alert-box>`)
Alert box untuk notifikasi dengan berbagai type.

**Props:**
- `type`: Type alert ('success', 'warning', 'error', 'info')
- `title`: Judul alert
- `dismissible`: Dapat ditutup (boolean)
- `icon`: Tampilkan icon (boolean, default: true)

**Contoh Penggunaan:**
```blade
{{-- Success --}}
<x-ui.alert-box type="success" title="Berhasil Disimpan">
    Data siswa berhasil ditambahkan ke sistem.
</x-ui.alert-box>

{{-- Warning dengan Dismissible --}}
<x-ui.alert-box type="warning" title="Perhatian" :dismissible="true">
    Anda belum ditempatkan di kelompok tahfidz.
</x-ui.alert-box>

{{-- Error --}}
<x-ui.alert-box type="error" title="Gagal Upload">
    File yang diupload melebihi ukuran maksimal.
</x-ui.alert-box>
```

---

### 6. **Gradient Card** (`<x-ui.gradient-card>`)
Card dengan gradient background (khususnya untuk highlight informasi penting).

**Props:**
- `color`: Warna gradient ('zamrud', 'blue', 'purple', 'green')
- `icon`: SVG icon
- `title`: Judul card
- `subtitle`: Subtitle

**Contoh Penggunaan:**
```blade
<x-ui.gradient-card color="zamrud" title="Kelompok Tahfidz Saya" subtitle="Al-Kawtsar">
    <x-slot:icon>
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
        </svg>
    </x-slot:icon>
    
    {{-- Content --}}
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-white bg-opacity-70 rounded-lg p-3">
            <p class="text-xs text-gray-600">Guru Pembimbing</p>
            <p class="text-sm font-semibold text-gray-900">Ustadz Ahmad</p>
        </div>
    </div>
</x-ui.gradient-card>
```

---

### 7. **Quick Link Card** (`<x-ui.quick-link-card>`)
Card untuk quick links/shortcuts dengan hover effect.

**Props:**
- `href`: URL tujuan
- `icon`: SVG icon
- `iconColor`: Warna icon ('blue', 'green', 'purple', 'yellow', 'amber', 'red', 'zamrud')
- `title`: Judul link
- `description`: Deskripsi singkat

**Contoh Penggunaan:**
```blade
<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    <x-ui.quick-link-card 
        href="{{ route('siswa.progres') }}" 
        icon-color="blue"
        title="Progres Detail"
        description="Lihat detail hafalan"
    >
        <x-slot:icon>
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
        </x-slot:icon>
    </x-ui.quick-link-card>
</div>
```

---

### 8. **Loading Spinner** (`<x-ui.loading-spinner>`)
Loading indicator dengan berbagai ukuran.

**Props:**
- `size`: Ukuran spinner ('sm', 'md', 'lg', 'xl')
- `color`: Warna spinner ('primary', 'white', 'gray')
- `text`: Text dibawah spinner

**Contoh Penggunaan:**
```blade
{{-- Basic --}}
<x-ui.loading-spinner />

{{-- Dengan Text --}}
<x-ui.loading-spinner size="lg" text="Memuat data..." />

{{-- Di Button --}}
<button wire:loading.attr="disabled">
    <span wire:loading.remove>Simpan</span>
    <span wire:loading>
        <x-ui.loading-spinner size="sm" color="white" />
    </span>
</button>
```

---

### 9. **Info Grid** (`<x-ui.info-grid>`)
Grid untuk menampilkan informasi dalam format label-value.

**Props:**
- `items`: Array of items `[['label' => '', 'value' => ''], ...]`
- `columns`: Jumlah kolom (1, 2, 3, 4)
- `transparent`: Background transparent (boolean)

**Contoh Penggunaan:**
```blade
{{-- Dengan Items Array --}}
<x-ui.info-grid 
    :columns="4"
    :items="[
        ['label' => 'Tingkat', 'value' => 'Pemula'],
        ['label' => 'Guru Pembimbing', 'value' => 'Ustadz Ahmad'],
        ['label' => 'Jadwal', 'value' => 'Senin 07:00-09:00'],
        ['label' => 'Anggota', 'value' => '15/20 siswa'],
    ]"
/>

{{-- Dengan Slot --}}
<x-ui.info-grid :columns="3" transparent>
    <div class="bg-white bg-opacity-70 rounded-lg p-3">
        <p class="text-xs text-gray-600">NIS</p>
        <p class="text-sm font-semibold text-gray-900">{{ $siswa->nis }}</p>
    </div>
    <div class="bg-white bg-opacity-70 rounded-lg p-3">
        <p class="text-xs text-gray-600">Kelas</p>
        <p class="text-sm font-semibold text-gray-900">{{ $siswa->kelas->nama }}</p>
    </div>
</x-ui.info-grid>
```

---

### 10. **Setoran Card** (`<x-siswa.setoran-card>`) - Komponen Khusus
Card untuk menampilkan detail setoran hafalan.

**Props:**
- `setoran`: Model Setoran

**Contoh Penggunaan:**
```blade
<div class="space-y-3">
    @foreach($setoranTerbaru as $setoran)
        <x-siswa.setoran-card :setoran="$setoran" />
    @endforeach
</div>
```

---

### 11. **Absensi Card** (`<x-siswa.absensi-card>`) - Komponen Khusus
Card untuk menampilkan detail absensi.

**Props:**
- `absensi`: Model Absensi

**Contoh Penggunaan:**
```blade
<div class="space-y-3">
    @foreach($riwayatAbsensi as $absensi)
        <x-siswa.absensi-card :absensi="$absensi" />
    @endforeach
</div>
```

---

## 🎨 Color Palette

### Primary Colors
- **Zamrud** (Primary): `#1B5E4F`
- **Delima** (Danger): `#9B3A3A`

### Status Colors
- **Success/Hadir**: Green (`#065F46` on `#D1FAE5`)
- **Warning/Izin**: Yellow (`#92400E` on `#FEF3C7`)
- **Info/Sakit**: Blue (`#1E40AF` on `#DBEAFE`)
- **Danger/Alfa**: Red (`#991B1B` on `#FEE2E2`)
- **Neutral**: Gray (`#374151` on `#F3F4F6`)

---

## 📝 Best Practices

### 1. **Gunakan Komponen untuk Konsistensi**
```blade
<!-- ❌ Bad: Inline styling -->
<span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs">
    Aktif
</span>

<!-- ✅ Good: Menggunakan komponen -->
<x-ui.status-badge status="Aktif" />
```

### 2. **Manfaatkan Slot untuk Fleksibilitas**
```blade
<x-ui.stat-card title="Total Siswa" value="150">
    <x-slot:icon>
        <!-- Custom icon -->
    </x-slot:icon>
    
    <!-- Custom content di bawah -->
    <div class="mt-2">
        <x-ui.progress-bar :percentage="75" />
    </div>
</x-ui.stat-card>
```

### 3. **Kombinasi Komponen**
```blade
<x-ui.gradient-card color="zamrud">
    <x-ui.info-grid :columns="3" transparent :items="$kelompokInfo" />
    
    <div class="mt-4">
        <x-ui.progress-bar 
            :percentage="$progres" 
            show-label 
            color="primary"
        >
            Progres Hafalan
        </x-ui.progress-bar>
    </div>
</x-ui.gradient-card>
```

### 4. **Responsive Design**
Semua komponen sudah responsive by default. Gunakan grid untuk layout:
```blade
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
    <x-ui.stat-card ... />
    <x-ui.stat-card ... />
    <x-ui.stat-card ... />
    <x-ui.stat-card ... />
</div>
```

---

## 🚀 Performance Tips

1. **Lazy Loading**: Gunakan wire:loading untuk menampilkan spinner
2. **Caching**: Data yang jarang berubah sudah di-cache di service layer
3. **Eager Loading**: Gunakan QueryOptimizationService untuk preload relasi
4. **Alpine.js**: Komponen menggunakan Alpine.js untuk interaktivitas ringan

---

## 📚 Related Documentation

- [REFACTORING_SUMMARY.md](.kiro/REFACTORING_SUMMARY.md) - Phase 1: Foundation
- [PERFORMANCE_BOOST.md](.kiro/PERFORMANCE_BOOST.md) - Phase 2: Performance
- [QUICK_REFERENCE.md](.kiro/QUICK_REFERENCE.md) - Quick reference services & traits

---

**Dibuat**: Phase 3 - UI Components & Blade Optimization  
**Terakhir Diupdate**: {{ now()->isoFormat('D MMMM Y') }}
