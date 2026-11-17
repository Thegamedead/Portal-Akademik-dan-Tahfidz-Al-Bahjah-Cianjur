# Penghilangan Backdrop/Overlay pada Modal

## Masalah
Saat modal/popup muncul, terdapat backdrop/overlay gelap yang menutupi background.

## Solusi yang Sudah Diterapkan

Telah dihapus backdrop gelap dari file-file berikut:

### Master Data Siswa
- ✅ `resources/views/livewire/admin/master-data/siswa/index-siswa.blade.php`
  - Modal Bulk Delete
  - Modal Form (Tambah/Edit)
  - Modal Detail
  - Modal Bulk Penempatan
- ✅ `resources/views/livewire/admin/master-data/siswa/partials/modal-bulk-pindah-kelas.blade.php`
- ✅ `resources/views/livewire/admin/master-data/siswa/partials/modal-registrasi-masuk.blade.php`
- ⚪ `modal-registrasi-keluar.blade.php` - Sudah menggunakan `bg-transparent` (tidak perlu diubah)
- ⚪ `modal-konfirmasi-keluar.blade.php` - Sudah menggunakan `bg-transparent` (tidak perlu diubah)

### Master Data Siswa Non-Aktif
- ⚪ `resources/views/livewire/admin/master-data/siswa/siswa-non-aktif.blade.php` - Sudah menggunakan `bg-transparent` (tidak perlu diubah)

### Master Data Kelas
- ✅ `resources/views/livewire/admin/master-data/kelas/index-kelas.blade.php`
  - Modal Bulk Delete
  - Modal Anggota
- ✅ `resources/views/livewire/admin/master-data/kelas/partials/modal-kelas.blade.php`
- ✅ `resources/views/livewire/admin/master-data/kelas/partials/modal-konfirmasi-hapus.blade.php`

### Master Data Guru
- ✅ `resources/views/livewire/admin/master-data/guru/index-guru.blade.php`
  - Modal Bulk Delete
  - Modal Registrasi Keluar
  - Modal Form

## File yang Masih Perlu Diperbaiki

Untuk menghilangkan backdrop pada file lain, gunakan pola berikut:

### Pola Lama (dengan backdrop):
```blade
<div class="fixed inset-0 z-50 overflow-y-auto">
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-[#1A2422] bg-opacity-60" wire:click="closeModal"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <!-- Modal content -->
    </div>
</div>
```

### Pola Baru (tanpa backdrop):
```blade
<div class="fixed inset-0 z-50 overflow-y-auto" style="pointer-events: none;">
    <div class="flex min-h-full items-center justify-center p-4" style="pointer-events: auto;">
        <!-- Modal content -->
    </div>
</div>
```

### Penjelasan:
- `pointer-events: none` pada container luar membuat background tidak dapat diklik
- `pointer-events: auto` pada container modal membuat modal tetap dapat diklik
- Backdrop gelap dihapus sepenuhnya

## File Lain yang Mungkin Perlu Diperiksa

1. `resources/views/livewire/admin/master-data/kelompok/index-kelompok.blade.php`
2. `resources/views/livewire/admin/usulan-kenaikan-tingkat/index-usulan.blade.php`
3. `resources/views/livewire/admin/usulan-kenaikan-tingkat/review-usulan.blade.php`
4. `resources/views/livewire/admin/usulan-kenaikan-tingkat/detail-usulan.blade.php`
5. `resources/views/livewire/admin/usulan-kenaikan-tingkat/penempatan-siswa.blade.php`
6. `resources/views/livewire/admin/kelulusan-program/**/*.blade.php`
7. `resources/views/livewire/admin/manajemen-akun.blade.php`
8. `resources/views/livewire/admin/manajemen-akun-admin.blade.php`

## Cara Mengganti Secara Otomatis

Gunakan Find & Replace (Ctrl+H) di editor dengan pattern regex:

**Find:**
```regex
{{--\s*Backdrop\s*--}}\s*<div\s+class="(fixed|absolute)\s+inset-0\s+bg-\[#1A2422\]\s+bg-opacity-60[^"]*"[^>]*></div>
```

**Replace dengan:** (kosongkan)

Atau gunakan PowerShell:
```powershell
$content = Get-Content $file -Raw
$content = $content -replace '{{--\s*Backdrop\s*--}}\s*<div\s+class="(fixed|absolute)\s+inset-0\s+bg-\[#1A2422\]\s+bg-opacity-60[^"]*"[^>]*></div>\s*', ''
Set-Content $file -Value $content -NoNewline
```

## Catatan
- Perubahan ini menghilangkan overlay gelap di belakang modal
- Modal tetap dapat ditutup dengan tombol close atau tombol batal
- Jika ingin modal dapat ditutup dengan klik diluar area modal, gunakan Alpine.js `@click.outside="closeModal()"`
