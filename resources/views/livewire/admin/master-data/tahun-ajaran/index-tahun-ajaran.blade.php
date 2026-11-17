{{--
    View: livewire/admin/master-data/tahun-ajaran/index-tahun-ajaran.blade.php
    Halaman daftar Tahun Ajaran — simplified UX
--}}

<div>
    <div class="container mx-auto px-4 py-6">

        {{-- Page Header --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm mb-6" style="border:1px solid #E5E7EB;">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold" style="color:#1A2422;font-family:'Plus Jakarta Sans',sans-serif;">
                        Tahun Ajaran
                    </h1>
                    <p class="text-sm mt-1" style="color:#6B7280;font-family:'Inter',sans-serif;">
                        Kelola periode akademik. Hanya satu tahun ajaran yang dapat aktif sekaligus.
                    </p>
                </div>
                <button
                    type="button"
                    wire:click="$dispatch('bukaModalTahunAjaran')"
                    class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold text-white transition-all hover:opacity-90 active:scale-95 shrink-0"
                    style="background-color:#1B5E4F;font-family:'Inter',sans-serif;"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                    </svg>
                    Tambah Tahun Ajaran
                </button>
            </div>
        </div>

        {{-- Daftar Tahun Ajaran --}}
        @if($tahunAjarans->isEmpty())
            {{-- Empty State --}}
            <div class="bg-white rounded-2xl p-12 shadow-sm text-center" style="border:1px solid #E5E7EB;">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl" style="background:#E0F2F1;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" style="color:#1B5E4F;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold mb-1" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
                    Belum ada tahun ajaran
                </h3>
                <p class="text-sm mb-5" style="color:#6B7280;">
                    Mulai dengan membuat tahun ajaran pertama untuk mengaktifkan sistem.
                </p>
                <button
                    type="button"
                    wire:click="$dispatch('bukaModalTahunAjaran')"
                    class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold text-white"
                    style="background:#1B5E4F;"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                    </svg>
                    Buat Tahun Ajaran Pertama
                </button>
            </div>
        @else
            <div class="space-y-3">
                @foreach($tahunAjarans as $ta)
                    <div
                        class="bg-white rounded-2xl px-5 py-4 shadow-sm flex flex-col sm:flex-row sm:items-center gap-4 transition-all"
                        style="border:2px solid {{ $ta->status ? '#1B5E4F' : '#E5E7EB' }};"
                    >
                        {{-- Left: icon + info --}}
                        <div class="flex items-center gap-4 flex-1 min-w-0">
                            {{-- Calendar icon with year --}}
                            <div
                                class="flex h-12 w-12 shrink-0 flex-col items-center justify-center rounded-xl text-center"
                                style="background:{{ $ta->status ? '#1B5E4F' : '#F3F4F6' }};"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" style="color:{{ $ta->status ? '#fff' : '#9CA3AF' }};" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                </svg>
                            </div>

                            {{-- Label + periode + semester --}}
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2 mb-0.5">
                                    <span class="text-base font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
                                        TA {{ $ta->label }}
                                    </span>
                                    @if($ta->status)
                                        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold text-white" style="background:#1B5E4F;">
                                            <span class="h-1.5 w-1.5 rounded-full bg-green-300 inline-block animate-pulse"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium" style="background:#F3F4F6;color:#6B7280;">
                                            Tidak Aktif
                                        </span>
                                    @endif
                                </div>
                                <div class="flex flex-wrap items-center gap-3 text-xs" style="color:#6B7280;">
                                    <span class="flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $ta->tanggal_mulai->tgl() }} — {{ $ta->tanggal_selesai->tgl() }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $ta->semesters_count }} semester
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Right: action buttons --}}
                        <div class="flex items-center gap-2 shrink-0">
                            {{-- Tombol "Jadikan Aktif" hanya muncul jika tidak aktif --}}
                            @if(!$ta->status)
                                <button
                                    type="button"
                                    wire:click="jadikanAktif({{ $ta->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="jadikanAktif({{ $ta->id }})"
                                    class="inline-flex min-w-[112px] items-center justify-center rounded-lg px-3 py-1.5 text-xs font-semibold text-white transition-all hover:opacity-90 active:scale-95 disabled:opacity-70"
                                    style="background:#1B5E4F;"
                                    title="Jadikan tahun ajaran ini aktif"
                                >
                                    {{-- Wrapper selalu flex row agar spinner tidak push ke bawah --}}
                                    <span class="flex items-center gap-1.5">
                                        {{-- Icon: centang --}}
                                        <svg wire:loading.remove wire:target="jadikanAktif({{ $ta->id }})" xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        {{-- Icon: spinner --}}
                                        <svg wire:loading wire:target="jadikanAktif({{ $ta->id }})" class="h-3.5 w-3.5 shrink-0 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="10" stroke="white" stroke-width="3" style="opacity:.2"/>
                                            <path fill="white" style="opacity:.9" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                        </svg>
                                        {{-- Teks --}}
                                        <span wire:loading.remove wire:target="jadikanAktif({{ $ta->id }})">Jadikan Aktif</span>
                                        <span wire:loading wire:target="jadikanAktif({{ $ta->id }})">Mengaktifkan…</span>
                                    </span>
                                </button>
                            @else
                                {{-- Label info bahwa ini yang aktif, tidak perlu tombol --}}
                                <span class="text-xs px-3 py-1.5 rounded-lg" style="background:#F0FDF9;color:#1B5E4F;font-weight:500;">
                                    ✓ Sedang Digunakan
                                </span>
                            @endif

                            {{-- Edit --}}
                            <button
                                type="button"
                                wire:click="$dispatch('bukaModalTahunAjaran', { id: {{ $ta->id }} })"
                                class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium transition-colors"
                                style="background:#F4F5F4;color:#374151;border:1px solid #E5E7EB;"
                                title="Edit tahun ajaran"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                </svg>
                                Edit
                            </button>

                            {{-- Hapus --}}
                            <button
                                type="button"
                                wire:click="bukaModalKonfirmasi({{ $ta->id }})"
                                class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium text-white transition-colors hover:opacity-90"
                                style="background:#9B3A3A;"
                                title="Hapus tahun ajaran"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                Hapus
                            </button>
                        </div>
                    </div>
                @endforeach

                {{-- Footer count --}}
                <p class="text-xs text-right" style="color:#9CA3AF;font-family:'Inter',sans-serif;">
                    Total {{ $tahunAjarans->count() }} tahun ajaran
                </p>
            </div>
        @endif
    </div>

    {{-- Modal Tambah/Edit --}}
    <livewire:admin.master-data.tahun-ajaran.modal-tahun-ajaran />

    {{-- Modal Konfirmasi Hapus --}}
    @if($showModalKonfirmasi)
        <div
            class="fixed inset-0 z-50 flex items-center justify-center p-4 pointer-events-none"
            role="dialog"
            aria-modal="true"
            aria-labelledby="modal-hapus-title"
        >
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" wire:click="tutupModalKonfirmasi" aria-hidden="true"></div>

            <div class="relative w-full max-w-md rounded-2xl bg-white shadow-xl" style="font-family:'Inter',sans-serif;">
                <div class="flex items-start gap-4 px-6 pt-6 pb-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full" style="background:#FEE2E2;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" style="color:#9B3A3A;" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 id="modal-hapus-title" class="text-base font-semibold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
                            Hapus Tahun Ajaran?
                        </h3>
                        <p class="mt-2 text-sm" style="color:#4B5563;">
                            Anda akan menghapus <strong>TA {{ $tahunAjaranHapus?->label }}</strong>.
                            @if($tahunAjaranHapus?->semesters_count > 0)
                                Sebanyak <strong>{{ $tahunAjaranHapus->semesters_count }} semester</strong> terkait juga akan terhapus.
                            @endif
                        </p>
                        <div class="mt-3 rounded-lg p-3 text-xs" style="background:#FEF3C7;border-left:3px solid #C9A227;color:#4B5563;">
                            Kelompok dalam tahun ajaran ini akan kehilangan referensi periode. Tindakan ini tidak dapat dibatalkan.
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 border-t px-6 py-4" style="border-color:#E5E7EB;">
                    <button
                        type="button"
                        wire:click="tutupModalKonfirmasi"
                        class="inline-flex items-center rounded-lg px-4 py-2 text-sm font-medium transition-colors"
                        style="background:#F4F5F4;color:#1A2422;border:1px solid #D1D5DB;"
                    >
                        Batal
                    </button>
                    <button
                        type="button"
                        wire:click="delete"
                        wire:loading.attr="disabled"
                        wire:target="delete"
                        class="inline-flex items-center rounded-lg px-4 py-2 text-sm font-semibold text-white transition-colors disabled:opacity-70"
                        style="background:#9B3A3A;"
                    >
                        <span wire:loading.remove wire:target="delete">Ya, Hapus</span>
                        <span wire:loading wire:target="delete" class="flex items-center gap-2">
                            <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Menghapus…
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
