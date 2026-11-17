{{--
    View: livewire/admin/manajemen-akun.blade.php
    Komponen Livewire: App\Livewire\Admin\ManajemenAkun

    Tab: Guru | Siswa
    Fitur: pencarian, tabel dengan badge status, pagination,
           modal reset kredensial, modal konfirmasi nonaktifkan.
--}}
<div>
    {{-- Page Header Card --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm mb-6" style="border:1px solid #E5E7EB;">
        <h1 class="text-2xl font-bold mb-2" style="color:#1A2422;font-family:'Plus Jakarta Sans',sans-serif;">
            Manajemen Akun
        </h1>
        <p class="text-sm" style="color:#6B7280;font-family:'Inter',sans-serif;">
            Kelola akun guru, siswa, dan status akun pengguna
        </p>
    </div>

    {{-- ── Header: Tab Switcher + Search ───────────────────────────────────── --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        {{-- Tab Guru / Siswa / Admin --}}
        <div class="flex rounded-lg p-1 gap-1" style="background-color: #E5E7EB;">
            <button
                type="button"
                wire:click="$set('tabAktif', 'guru')"
                class="flex-1 rounded-md px-4 py-2 text-sm font-medium transition-colors focus:outline-none focus:ring-2"
                style="{{ $tabAktif === 'guru' ? 'background-color:#1B5E4F;color:#FFFFFF;' : 'background-color:transparent;color:#6B7280;' }}"
                role="tab"
            >
                Guru
            </button>
            <button
                type="button"
                wire:click="$set('tabAktif', 'siswa')"
                class="flex-1 rounded-md px-4 py-2 text-sm font-medium transition-colors focus:outline-none focus:ring-2"
                style="{{ $tabAktif === 'siswa' ? 'background-color:#1B5E4F;color:#FFFFFF;' : 'background-color:transparent;color:#6B7280;' }}"
                role="tab"
            >
                Siswa
            </button>
        </div>

    </div>
    {{-- ── /Header ──────────────────────────────────────────────────────────── --}}

    {{-- ── Tabel Akun (Guru / Siswa) ─────────────────────────────────────────── --}}

    {{-- Bulk Action Toolbar --}}
    <div class="mb-3 flex items-center gap-2 flex-wrap">
        {{-- Generate Password --}}
        @if(count($selectedIds) > 0)
            <button
                type="button"
                wire:click="bukaModalGenerate"
                class="inline-flex items-center gap-1.5 rounded-lg px-4 py-2.5 text-sm font-medium transition-colors focus:outline-none"
                style="background-color:#FFFFFF;color:#1A2422;border:1.5px solid #D1D5DB;"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                </svg>
                Generate Password
            </button>
        @else
            <button disabled
                class="inline-flex items-center gap-1.5 rounded-lg px-4 py-2.5 text-sm font-medium opacity-40 cursor-not-allowed"
                style="background-color:#FFFFFF;color:#9CA3AF;border:1.5px solid #E5E7EB;"
                title="Pilih minimal 1 akun untuk generate password"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                </svg>
                Generate Password
            </button>
        @endif

        {{-- Cetak Kartu Login --}}
        <button
            type="button"
            wire:click="cetakKartuLogin"
            class="inline-flex items-center gap-1.5 rounded-lg px-4 py-2.5 text-sm font-medium transition-colors focus:outline-none"
            style="background-color:#FFFFFF;color:#0369A1;border:1.5px solid #BAE6FD;"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            {{ count($selectedIds) > 0 ? 'Cetak ('.count($selectedIds).') Kartu' : 'Cetak Semua Kartu' }}
        </button>

        {{-- Nonaktifkan --}}
        @if($this->countAktif > 0)
            <button
                type="button"
                wire:click="bukaModalNonaktif"
                class="inline-flex items-center gap-1.5 rounded-lg px-4 py-2.5 text-sm font-medium transition-colors focus:outline-none"
                style="background-color:#FFFFFF;color:#9B3A3A;border:1.5px solid #F5C6C6;"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
                Nonaktifkan ({{ $this->countAktif }})
            </button>
        @else
            <button disabled
                class="inline-flex items-center gap-1.5 rounded-lg px-4 py-2.5 text-sm font-medium opacity-40 cursor-not-allowed"
                style="background-color:#FFFFFF;color:#9CA3AF;border:1.5px solid #E5E7EB;"
                title="Pilih minimal 1 akun aktif untuk nonaktifkan"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
                Nonaktifkan
            </button>
        @endif

        {{-- Aktifkan --}}
        @if($this->countNonaktif > 0)
            <button
                type="button"
                wire:click="bukaModalAktifkan"
                class="inline-flex items-center gap-1.5 rounded-lg px-4 py-2.5 text-sm font-medium transition-colors focus:outline-none"
                style="background-color:#FFFFFF;color:#1B5E4F;border:1.5px solid #A7D8C8;"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Aktifkan ({{ $this->countNonaktif }})
            </button>
        @else
            <button disabled
                class="inline-flex items-center gap-1.5 rounded-lg px-4 py-2.5 text-sm font-medium opacity-40 cursor-not-allowed"
                style="background-color:#FFFFFF;color:#9CA3AF;border:1.5px solid #E5E7EB;"
                title="Pilih minimal 1 akun nonaktif untuk mengaktifkan"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Aktifkan
            </button>
        @endif

        @if(count($selectedIds) > 0)
            <span class="text-xs font-medium px-2 py-1 rounded-full" style="background-color:#E8F5F1;color:#1B5E4F;">
                {{ count($selectedIds) }} dipilih
            </span>
        @endif
        
        <div class="flex-grow flex justify-end">
            <div class="w-full sm:w-64">
                <x-ui.search-field
                    wireModel="cari"
                    :placeholder="$tabAktif === 'guru' ? 'Cari nama / NIY…' : 'Cari nama / NIS…'"
                />
            </div>
        </div>
    </div>

    <div class="rounded-2xl bg-white shadow-sm overflow-hidden" style="border:1px solid #E5E7EB;">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y" style="divide-color:#E5E7EB;">
                <thead class="bg-[#1B5E4F] text-white">
                    <tr>
                        <th class="px-4 py-3 text-center" style="width:48px;">
                            <input type="checkbox" wire:model.live="selectAll"
                                class="w-4 h-4 rounded cursor-pointer"
                                style="color:#1B5E4F;border-color:#D1D5DB;">
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color:white;">No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color:white;">Username</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color:white;">Password</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color:white;">Nama</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color:white;">JK</th>
                        @if($tabAktif === 'guru')
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color:white;">Jabatan</th>
                        @else
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color:white;">Kelas</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color:white;">Kelompok</th>
                        @endif
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color:white;">Role</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color:white;">Akun</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color:white;">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="divide-color:#F3F4F6;">

                    @php
                        $items  = $tabAktif === 'guru' ? $gurus : $siswas;
                        $offset = ($items->currentPage() - 1) * $items->perPage();
                    @endphp

                    @forelse ($items as $index => $akun)
                        <tr wire:key="row-{{ $tabAktif }}-{{ $akun->id }}" class="transition-colors hover:bg-gray-50 {{ in_array($akun->id, $selectedIds) ? 'bg-green-50' : '' }}">

                            {{-- Checkbox --}}
                            <td class="px-4 py-3 text-center">
                                <input type="checkbox"
                                    wire:model.live="selectedIds"
                                    value="{{ $akun->id }}"
                                    class="w-4 h-4 rounded cursor-pointer"
                                    style="color:#1B5E4F;border-color:#D1D5DB;">
                            </td>

                            {{-- No --}}
                            <td class="px-4 py-3 text-sm tabular-nums" style="color:#9CA3AF;font-family:'Inter',sans-serif;">
                                {{ $offset + $loop->iteration }}
                            </td>

                            {{-- Username --}}
                            <td class="px-4 py-3 text-sm tabular-nums font-mono" style="color:#1A2422;">
                                {{ $tabAktif === 'guru' ? $akun->niy : $akun->nis }}
                            </td>

                            {{-- Password (Kredensial) --}}
                            <td class="px-4 py-3 text-sm tabular-nums font-mono" style="color:#1A2422;">
                                @if(empty($akun->password_text))
                                    @if(!empty($akun->password))
                                        <span class="text-xs font-medium px-2 py-1 rounded bg-blue-50 text-blue-600 font-sans border border-blue-100" title="Password telah diubah oleh pengguna sendiri">
                                            Diubah Pengguna
                                        </span>
                                    @else
                                        <span class="text-xs font-medium px-2 py-1 rounded bg-gray-100 text-gray-500 font-sans">
                                            Belum di-generate
                                        </span>
                                    @endif
                                @else
                                    <div x-data="{ show: false }" class="flex items-center gap-2">
                                        <span x-show="!show" class="tracking-widest">••••••••</span>
                                        <span x-show="show">{{ $akun->password_text }}</span>
                                        <button @click="show = !show" class="text-gray-400 hover:text-gray-600 focus:outline-none" title="Tampilkan/Sembunyikan Password">
                                            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                            </svg>
                                        </button>
                                    </div>
                                @endif
                            </td>

                            {{-- Nama --}}
                            <td class="px-4 py-3 text-sm font-medium" style="color:#1A2422;font-family:'Inter',sans-serif;">
                                {{ $akun->nama }}
                            </td>

                            {{-- JK --}}
                            <td class="px-4 py-3 text-center">
                                @if($akun->jenis_kelamin === 'Laki-laki')
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold" style="background-color:#E0F2FE;color:#0C4A6E;">L</span>
                                @elseif($akun->jenis_kelamin === 'Perempuan')
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold" style="background-color:#FCE7F3;color:#831843;">P</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>

                            @if($tabAktif === 'guru')
                                {{-- Jabatan --}}
                                <td class="px-4 py-3 text-sm" style="color:#6B7280;">
                                    {{ $akun->jabatan ?? '-' }}
                                </td>
                            @else
                                {{-- Kelas --}}
                                <td class="px-4 py-3 text-sm" style="color:#6B7280;">
                                    {{ $akun->kelas?->nama_kelas ?? '-' }}
                                </td>
                                {{-- Kelompok --}}
                                <td class="px-4 py-3 text-sm" style="color:#6B7280;">
                                    {{ $akun->kelompok?->nama ?? '-' }}
                                </td>
                            @endif

                            {{-- Role --}}
                            <td class="px-4 py-3">
                                @if($tabAktif === 'guru')
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium" style="background-color:#EFF6FF;color:#1D4ED8;">Guru</span>
                                @else
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium" style="background-color:#FEF3C7;color:#92400E;">Siswa</span>
                                @endif
                            </td>

                            {{-- Badge Status Akun --}}
                            <td class="px-4 py-3">
                                @php
                                    $hasPassword = !empty($akun->password);
                                    $isAktif     = $akun->akun_aktif;
                                    $sudahKeluar = isset($akun->status_akademik) && $akun->status_akademik !== 'Aktif';
                                @endphp

                                @if (!$hasPassword)
                                    {{-- Belum pernah digenerate sama sekali --}}
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium"
                                          style="background-color:#F3F4F6;color:#6B7280;border:1px solid #D1D5DB;"
                                          title="Akun belum pernah dibuat">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                        Belum Dibuat
                                    </span>
                                @elseif ($isAktif)
                                    {{-- Akun aktif & bisa login --}}
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium"
                                          style="background-color:#E8F5F1;color:#1B5E4F;">
                                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                        Aktif
                                    </span>
                                @elseif ($sudahKeluar)
                                    {{-- Sudah keluar / resign --}}
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium"
                                          style="background-color:#FEF3C7;color:#92400E;border:1px solid #FDE68A;"
                                          title="Pengguna sudah keluar dari institusi">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        Keluar
                                    </span>
                                @else
                                    {{-- Dinonaktifkan oleh admin --}}
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium"
                                          style="background-color:#FDEAEA;color:#9B3A3A;"
                                          title="Dinonaktifkan oleh admin">
                                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524L13.476 14.89zm1.414-1.414L6.524 5.11A6 6 0 0114.89 13.476zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"/></svg>
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            {{-- Keterangan Akun --}}
                            <td class="px-4 py-3 text-xs" style="color:#6B7280;max-width:180px;">
                                @if(!empty($akun->keterangan_akun))
                                    <span class="inline-flex items-center gap-1 rounded px-2 py-0.5 text-xs font-medium" style="background-color:#FEF3C7;color:#92400E;border:1px solid #FDE68A;">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" /></svg>
                                        {{ $akun->keterangan_akun }}
                                    </span>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $tabAktif === 'guru' ? 9 : 10 }}" class="px-4 py-10 text-center text-sm" style="color:#9CA3AF;font-family:'Inter',sans-serif;">
                                Tidak ada data {{ $tabAktif === 'guru' ? 'guru' : 'siswa' }} yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($items->hasPages())
            <div class="border-t px-4 py-3" style="border-color:#E5E7EB;">
                {{ $items->links() }}
            </div>
        @endif
    </div>
    {{-- ── /Tabel ───────────────────────────────────────────────────────────── --}}



    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL GENERATE PASSWORD                                               --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    @if ($showModalReset)
        <div
            class="fixed inset-0 z-50 flex items-center justify-center p-4 pointer-events-none"
            role="dialog"
            aria-modal="true"
            aria-labelledby="modal-generate-title"
        >
            {{-- Backdrop --}}
            <div
                class="absolute inset-0 bg-black/40"
                wire:click="tutupModalReset"
                aria-hidden="true"
            ></div>

            {{-- Panel --}}
            <div
                class="relative w-full max-w-md rounded-2xl bg-white shadow-xl"
                style="font-family:'Inter',sans-serif;"
            >
                {{-- Header --}}
                <div class="px-6 pt-6 pb-4">
                    <div class="flex items-start gap-4">
                        <div class="shrink-0 flex h-10 w-10 items-center justify-center rounded-full" style="background-color:#E8F5F1;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="#1B5E4F" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 id="modal-generate-title" class="text-base font-semibold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
                                Generate Password
                            </h3>
                            <div class="mt-2 text-sm" style="color:#4B5563;">
                                @if ($countBelumAda > 0 && $countSudahAda > 0)
                                    <p class="mb-3">
                                        Dari <strong>{{ count($selectedIds) }}</strong> akun yang dipilih:
                                        <br>• <strong>{{ $countBelumAda }}</strong> akun belum memiliki password.
                                        <br>• <strong>{{ $countSudahAda }}</strong> akun sudah memiliki password.
                                    </p>
                                    <p class="mb-2 font-medium" style="color:#1A2422;">Apa yang ingin Anda lakukan?</p>
                                    
                                    <div class="space-y-2 mt-2">
                                        <label class="flex items-start gap-2 cursor-pointer">
                                            <input type="radio" wire:model="modeGenerate" value="belum_saja" class="mt-0.5 border-gray-300 text-[#1B5E4F] focus:ring-[#1B5E4F]">
                                            <span class="text-sm">Hanya buatkan password untuk <strong>{{ $countBelumAda }}</strong> akun yang belum punya. <span class="text-xs text-gray-500">(Aman)</span></span>
                                        </label>
                                        <label class="flex items-start gap-2 cursor-pointer">
                                            <input type="radio" wire:model="modeGenerate" value="semua" class="mt-0.5 border-gray-300 text-[#1B5E4F] focus:ring-[#1B5E4F]">
                                            <span class="text-sm">Buat baru & reset password untuk <strong>semua ({{ count($selectedIds) }})</strong> akun.</span>
                                        </label>
                                    </div>
                                @elseif ($countBelumAda > 0)
                                    <p>
                                        Anda akan membuat password baru secara acak untuk <strong>{{ count($selectedIds) }}</strong> akun terpilih.
                                        Password ini nantinya bisa dilihat di tabel.
                                    </p>
                                @else
                                    <p>
                                        <strong>{{ count($selectedIds) }}</strong> akun terpilih sudah memiliki password. 
                                        Tindakan ini akan mereset (mengganti) password lama mereka dengan yang baru secara acak.
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-end gap-3 border-t px-6 py-4" style="border-color:#E5E7EB;">
                    <button
                        type="button"
                        wire:click="tutupModalReset"
                        class="inline-flex items-center rounded-lg px-4 py-2 text-sm font-medium transition-colors focus:outline-none"
                        style="background-color:#F4F5F4;color:#1A2422;border:1px solid #D1D5DB;"
                    >
                        Batalkan
                    </button>
                    <button
                        type="button"
                        wire:click="generateKredensial"
                        wire:loading.attr="disabled"
                        wire:target="generateKredensial"
                        class="inline-flex items-center justify-center gap-2 rounded-lg px-5 py-2 text-sm font-medium text-white transition-colors focus:outline-none disabled:opacity-70 min-w-[110px]"
                        style="background-color:#1B5E4F;"
                    >
                        <svg wire:loading wire:target="generateKredensial" class="h-4 w-4 animate-spin shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="generateKredensial">Generate</span>
                        <span wire:loading wire:target="generateKredensial">Memproses…</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
    {{-- ── /Modal Generate Password ──────────────────────────────────────────── --}}


    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL KONFIRMASI NONAKTIFKAN AKUN                                     --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    @if ($showModalNonaktif)
        <div
            class="fixed inset-0 z-50 flex items-center justify-center p-4 pointer-events-none"
            role="dialog"
            aria-modal="true"
            aria-labelledby="modal-nonaktif-title"
        >
            {{-- Backdrop --}}
            <div
                class="absolute inset-0 bg-black/40"
                wire:click="tutupModalNonaktif"
                aria-hidden="true"
            ></div>

            {{-- Panel --}}
            <div
                class="relative w-full max-w-md rounded-2xl bg-white shadow-xl"
                style="font-family:'Inter',sans-serif;"
            >
                {{-- Header --}}
                <div class="px-6 pt-6 pb-4">
                    <div class="flex items-start gap-4">
                        <div class="shrink-0 flex h-10 w-10 items-center justify-center rounded-full" style="background-color:#FDEAEA;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="#9B3A3A" aria-hidden="true">
                                <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 id="modal-nonaktif-title" class="text-base font-semibold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
                                Nonaktifkan Akun
                            </h3>
                            <p class="mt-1 text-sm" style="color:#4B5563;">
                                Apakah Anda yakin ingin menonaktifkan <strong>{{ count($selectedIds) }}</strong> akun terpilih?
                                Pengguna tidak akan dapat login setelah dinonaktifkan.
                            </p>

                            {{-- Peringatan guru pembimbing aktif --}}
                            @if ($guruPembimbingAktif)
                                <div class="mt-3 flex items-start gap-2 rounded-lg px-3 py-2.5 text-sm" style="background-color:#FDEAEA;border:1px solid #F5C6C6;color:#9B3A3A;">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mt-0.5 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Guru ini masih aktif sebagai guru pembimbing di satu atau lebih kelompok. Pastikan kelompok tersebut sudah memiliki pengganti sebelum menonaktifkan akun.</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-end gap-3 border-t px-6 py-4" style="border-color:#E5E7EB;">
                    <button
                        type="button"
                        wire:click="tutupModalNonaktif"
                        class="inline-flex items-center rounded-lg px-4 py-2 text-sm font-medium transition-colors focus:outline-none"
                        style="background-color:#F4F5F4;color:#1A2422;border:1px solid #D1D5DB;"
                    >
                        Batalkan
                    </button>
                    <button
                        type="button"
                        wire:click="nonaktifkanAkun"
                        wire:loading.attr="disabled"
                        wire:target="nonaktifkanAkun"
                        class="inline-flex items-center rounded-lg px-4 py-2 text-sm font-medium text-white transition-colors focus:outline-none disabled:opacity-70"
                        style="background-color:#9B3A3A;"
                    >
                        <span wire:loading.remove wire:target="nonaktifkanAkun">Nonaktifkan</span>
                        <span wire:loading wire:target="nonaktifkanAkun" class="flex items-center gap-2">
                            <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Memproses…
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL KONFIRMASI AKTIFKAN AKUN                                        --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    @if ($showModalAktifkan)
        <div
            class="fixed inset-0 z-50 flex items-center justify-center p-4 pointer-events-none"
            role="dialog"
            aria-modal="true"
            aria-labelledby="modal-aktifkan-title"
        >
            {{-- Backdrop --}}
            <div
                class="absolute inset-0 bg-black/40"
                wire:click="tutupModalAktifkan"
                aria-hidden="true"
            ></div>

            {{-- Panel --}}
            <div
                class="relative w-full max-w-md rounded-2xl bg-white shadow-xl"
                style="font-family:'Inter',sans-serif;"
            >
                {{-- Header --}}
                <div class="px-6 pt-6 pb-4">
                    <div class="flex items-start gap-4">
                        <div class="shrink-0 flex h-10 w-10 items-center justify-center rounded-full" style="background-color:#E8F5F1;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="#1B5E4F" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 id="modal-aktifkan-title" class="text-base font-semibold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
                                Aktifkan Akun
                            </h3>
                            <p class="mt-1 text-sm" style="color:#4B5563;">
                                Apakah Anda yakin ingin mengaktifkan <strong>{{ count($selectedIds) }}</strong> akun terpilih?
                                Pengguna akan dapat login kembali.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-end gap-3 border-t px-6 py-4" style="border-color:#E5E7EB;">
                    <button
                        type="button"
                        wire:click="tutupModalAktifkan"
                        class="inline-flex items-center rounded-lg px-4 py-2 text-sm font-medium transition-colors focus:outline-none"
                        style="background-color:#F4F5F4;color:#1A2422;border:1px solid #D1D5DB;"
                    >
                        Batalkan
                    </button>
                    <button
                        type="button"
                        wire:click="aktifkanAkun"
                        wire:loading.attr="disabled"
                        wire:target="aktifkanAkun"
                        class="inline-flex items-center rounded-lg px-4 py-2 text-sm font-medium text-white transition-colors focus:outline-none disabled:opacity-70"
                        style="background-color:#1B5E4F;"
                    >
                        <span wire:loading.remove wire:target="aktifkanAkun">Aktifkan</span>
                        <span wire:loading wire:target="aktifkanAkun" class="flex items-center gap-2">
                            <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Memproses…
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
