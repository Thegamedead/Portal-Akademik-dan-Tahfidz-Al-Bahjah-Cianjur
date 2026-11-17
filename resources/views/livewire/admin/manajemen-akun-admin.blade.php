{{--
    View: livewire/admin/manajemen-akun-admin.blade.php
    Komponen Livewire: App\Livewire\Admin\ManajemenAkunAdmin

    Fitur: pencarian admin, tombol tambah, tabel dengan badge status,
           pagination, modal form tambah admin (≤6 field),
           nonaktifkan admin dengan toast error minimal-admin.
--}}
<div>

    {{-- ── Header: Search + Tombol Tambah ─────────────────────────────────── --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        {{-- Search --}}
        <div class="w-full sm:w-64">
            <x-ui.search-field
                wireModel="cari"
                placeholder="Cari nama atau email admin…"
            />
        </div>

        {{-- Tombol Tambah Admin --}}
        <button
            type="button"
            wire:click="bukaModalTambah"
            class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-white transition-colors focus:outline-none focus:ring-2 shrink-0"
            style="background-color:#1B5E4F;"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Admin
        </button>
    </div>
    {{-- ── /Header ──────────────────────────────────────────────────────────── --}}

    {{-- ── Tabel Admin ─────────────────────────────────────────────────────── --}}
    <div class="rounded-2xl bg-white shadow-sm overflow-hidden" style="border:1px solid #E5E7EB;">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y" style="divide-color:#E5E7EB;">
                <thead class="bg-[#1B5E4F] text-white">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color:white;font-family:'Inter',sans-serif;">No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color:white;font-family:'Inter',sans-serif;">Nama</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color:white;font-family:'Inter',sans-serif;">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color:white;font-family:'Inter',sans-serif;">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color:white;font-family:'Inter',sans-serif;">Dibuat</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide" style="color:white;font-family:'Inter',sans-serif;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="divide-color:#F3F4F6;">

                    @php
                        $offset = ($admins->currentPage() - 1) * $admins->perPage();
                    @endphp

                    @forelse ($admins as $index => $admin)
                        <tr class="transition-colors">

                            {{-- No --}}
                            <td class="px-4 py-3 text-sm tabular-nums" style="color:#1A2422;font-family:'Inter',sans-serif;">
                                {{ $offset + $loop->iteration }}
                            </td>

                            {{-- Nama / Akun --}}
                            <td class="px-4 py-3 text-sm font-medium" style="color:#1A2422;font-family:'Inter',sans-serif;">
                                <div class="flex items-center gap-2.5">
                                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-white text-xs font-bold"
                                         style="background-color:#1B5E4F;font-family:'Plus Jakarta Sans',sans-serif;">
                                        {{ strtoupper(substr($admin->nama ?? $admin->email ?? 'A', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium" style="color:#1A2422;">{{ $admin->nama ?: '—' }}</p>
                                        <p class="text-xs" style="color:#6B7280;">{{ $admin->email }}</p>
                                    </div>
                                    @if ($admin->id === auth('admin')->id())
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                                              style="background-color:#E8F5F1;color:#1B5E4F;">Anda</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Email --}}
                            <td class="px-4 py-3 text-sm" style="color:#6B7280;font-family:'Inter',sans-serif;">
                                {{ $admin->email }}
                            </td>

                            {{-- Badge Status --}}
                            <td class="px-4 py-3">
                                @if ($admin->status_aktif)
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                          style="background-color:#E8F5F1;color:#1B5E4F;">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                          style="background-color:#FDEAEA;color:#9B3A3A;">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            {{-- Dibuat --}}
                            <td class="px-4 py-3 text-sm tabular-nums" style="color:#1A2422;font-family:'Inter',sans-serif;">
                                {{ $admin->created_at?->tgl() ?? '—' }}
                            </td>

                            {{-- Aksi --}}
                            <td class="px-4 py-3 text-right">
                                @if ($admin->status_aktif && $admin->id !== auth('admin')->id())
                                    <button
                                        type="button"
                                        wire:click="nonaktifkanAdmin({{ $admin->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="nonaktifkanAdmin({{ $admin->id }})"
                                        class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium transition-colors focus:outline-none focus:ring-2 disabled:opacity-70"
                                        style="background-color:#FDEAEA;color:#9B3A3A;border:1px solid #F5C6C6;"
                                        title="Nonaktifkan Admin"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                        </svg>
                                        <span wire:loading.remove wire:target="nonaktifkanAdmin({{ $admin->id }})">Nonaktifkan</span>
                                        <span wire:loading wire:target="nonaktifkanAdmin({{ $admin->id }})">
                                            <svg class="h-3.5 w-3.5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </span>
                                    </button>
                                @elseif ($admin->id === auth('admin')->id())
                                    <span class="text-xs" style="color:#9CA3AF;font-family:'Inter',sans-serif;">Akun Anda</span>
                                @else
                                    <span class="text-xs" style="color:#9CA3AF;font-family:'Inter',sans-serif;">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-sm" style="color:#9CA3AF;font-family:'Inter',sans-serif;">
                                Tidak ada data admin yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($admins->hasPages())
            <div class="border-t px-4 py-3" style="border-color:#E5E7EB;">
                {{ $admins->links() }}
            </div>
        @endif
    </div>
    {{-- ── /Tabel ───────────────────────────────────────────────────────────── --}}


    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL TAMBAH ADMIN (≤6 field → modal sesuai aturan UI design system)  --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    @if ($showModal)
        <div
            class="fixed inset-0 z-50 flex items-center justify-center p-4 pointer-events-none"
            role="dialog"
            aria-modal="true"
            aria-labelledby="modal-tambah-admin-title"
        >
            {{-- Backdrop --}}
            <div
                class="absolute inset-0 bg-black/40"
                wire:click="tutupModal"
                aria-hidden="true"
            ></div>

            {{-- Panel --}}
            <div
                class="relative w-full max-w-md rounded-2xl bg-white shadow-xl"
                style="font-family:'Inter',sans-serif;"
            >
                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl flex-shrink-0" style="background-color:#1B5E4F;">
                    <h3 id="modal-tambah-admin-title" class="text-base font-semibold"
                        style="font-family:'Plus Jakarta Sans',sans-serif;color:white;">
                        Tambah Admin Baru
                    </h3>
                    <button
                        type="button"
                        wire:click="tutupModal"
                        class="rounded-lg p-1.5 transition-colors hover:bg-white/20 focus:outline-none focus:ring-2" style="color:white;"
                        aria-label="Tutup modal"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                {{-- Body --}}
                <div class="px-6 py-4 space-y-4">

                    {{-- Field: Nama --}}
                    <div>
                        <label for="admin-nama" class="mb-1.5 block text-sm font-medium" style="color:#1A2422;">
                            Nama <span style="color:#9B3A3A;" aria-hidden="true">*</span>
                        </label>
                        <input
                            id="admin-nama"
                            type="text"
                            wire:model="formData.nama"
                            class="w-full rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2"
                            style="background-color:#FFFFFF;border:1.5px solid {{ $errors->has('formData.nama') ? '#9B3A3A' : '#D1D5DB' }};color:#1A2422;"
                            placeholder="Nama lengkap admin"
                            autocomplete="off"
                        >
                        @error('formData.nama')
                            <p class="mt-1.5 text-xs" style="color:#9B3A3A;" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Field: Email --}}
                    <div>
                        <label for="admin-email" class="mb-1.5 block text-sm font-medium" style="color:#1A2422;">
                            Email <span style="color:#9B3A3A;" aria-hidden="true">*</span>
                        </label>
                        <input
                            id="admin-email"
                            type="email"
                            wire:model="formData.email"
                            class="w-full rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2"
                            style="background-color:#FFFFFF;border:1.5px solid {{ $errors->has('formData.email') ? '#9B3A3A' : '#D1D5DB' }};color:#1A2422;"
                            placeholder="email@contoh.com"
                            autocomplete="off"
                        >
                        @error('formData.email')
                            <p class="mt-1.5 text-xs" style="color:#9B3A3A;" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Field 2: Password --}}
                    <div x-data="{ showPwd: false }">
                        <label for="admin-password" class="mb-1.5 block text-sm font-medium" style="color:#1A2422;">
                            Password <span style="color:#9B3A3A;" aria-hidden="true">*</span>
                        </label>
                        <div class="relative">
                            <input
                                id="admin-password"
                                :type="showPwd ? 'text' : 'password'"
                                wire:model="formData.password"
                                class="w-full rounded-lg px-3.5 py-2.5 pr-10 text-sm focus:outline-none focus:ring-2"
                                style="background-color:#FFFFFF;border:1.5px solid {{ $errors->has('formData.password') ? '#9B3A3A' : '#D1D5DB' }};color:#1A2422;"
                                placeholder="Tepat 8 karakter"
                                autocomplete="new-password"
                                aria-required="true"
                            >
                            <button
                                type="button"
                                @click="showPwd = !showPwd"
                                class="absolute inset-y-0 right-3 flex items-center focus:outline-none transition-colors"
                                style="color:#6B7280;"
                                :aria-label="showPwd ? 'Sembunyikan password' : 'Tampilkan password'"
                            >
                                <svg x-show="!showPwd" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showPwd" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true" style="display:none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                        @error('formData.password')
                            <p class="mt-1.5 text-xs" style="color:#9B3A3A;" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Field 3: Konfirmasi Password --}}
                    <div x-data="{ showPwdKonfirm: false }">
                        <label for="admin-password-confirmation" class="mb-1.5 block text-sm font-medium" style="color:#1A2422;">
                            Konfirmasi Password <span style="color:#9B3A3A;" aria-hidden="true">*</span>
                        </label>
                        <div class="relative">
                            <input
                                id="admin-password-confirmation"
                                :type="showPwdKonfirm ? 'text' : 'password'"
                                wire:model="formData.password_confirmation"
                                class="w-full rounded-lg px-3.5 py-2.5 pr-10 text-sm focus:outline-none focus:ring-2"
                                style="background-color:#FFFFFF;border:1.5px solid #D1D5DB;color:#1A2422;"
                                placeholder="Ulangi password"
                                autocomplete="new-password"
                                aria-required="true"
                            >
                            <button
                                type="button"
                                @click="showPwdKonfirm = !showPwdKonfirm"
                                class="absolute inset-y-0 right-3 flex items-center focus:outline-none transition-colors"
                                style="color:#6B7280;"
                                :aria-label="showPwdKonfirm ? 'Sembunyikan konfirmasi password' : 'Tampilkan konfirmasi password'"
                            >
                                <svg x-show="!showPwdKonfirm" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showPwdKonfirm" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true" style="display:none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Field 4: Status Aktif --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-medium" style="color:#1A2422;">Status Akun</label>
                        <div class="flex items-center gap-6 mt-1">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input
                                    type="radio"
                                    wire:model="formData.status_aktif"
                                    value="1"
                                    class="h-4 w-4 focus:ring-2"
                                    style="accent-color:#1B5E4F;"
                                >
                                <span class="text-sm" style="color:#1A2422;font-family:'Inter',sans-serif;">Aktif</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input
                                    type="radio"
                                    wire:model="formData.status_aktif"
                                    value="0"
                                    class="h-4 w-4 focus:ring-2"
                                    style="accent-color:#9B3A3A;"
                                >
                                <span class="text-sm" style="color:#1A2422;font-family:'Inter',sans-serif;">Nonaktif</span>
                            </label>
                        </div>
                    </div>

                </div>
                {{-- /Body --}}

                {{-- Footer --}}
                <div class="flex items-center justify-end gap-3 px-6 py-4" style="border-top:1px solid #E5E7EB; background-color: #F9FAFB;">
                    <button
                        type="button"
                        wire:click="tutupModal"
                        class="inline-flex items-center rounded-lg px-4 py-2 text-sm font-medium transition-colors focus:outline-none"
                        style="background-color:#F4F5F4;color:#1A2422;border:1px solid #D1D5DB;"
                    >
                        Batalkan
                    </button>
                    <button
                        type="button"
                        wire:click="simpanAkun"
                        wire:loading.attr="disabled"
                        wire:target="simpanAkun"
                        class="inline-flex items-center rounded-lg px-4 py-2 text-sm font-medium text-white transition-colors focus:outline-none disabled:opacity-70"
                        style="background-color:#1B5E4F;"
                    >
                        <span wire:loading.remove wire:target="simpanAkun">Simpan</span>
                        <span wire:loading wire:target="simpanAkun" class="flex items-center gap-2">
                            <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Menyimpan…
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif
    {{-- ── /Modal Tambah Admin ──────────────────────────────────────────────── --}}

</div>
