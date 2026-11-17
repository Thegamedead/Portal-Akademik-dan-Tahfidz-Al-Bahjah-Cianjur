{{--
    View: livewire/admin/master-data/semester/index-semester.blade.php
    Komponen Livewire: App\Livewire\Admin\MasterData\Semester\IndexSemester

    Halaman daftar Semester dengan filter tahun ajaran dan aksi CRUD
--}}

<div>
    <div class="container mx-auto px-4 py-6">

        {{-- Page Header Card --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm mb-6" style="border:1px solid #E5E7EB;">
            <h1 class="text-2xl font-bold mb-2" style="color:#1A2422;font-family:'Plus Jakarta Sans',sans-serif;">
                Semester
            </h1>
            <p class="text-sm" style="color:#6B7280;font-family:'Inter',sans-serif;">
                Kelola periode semester dalam tahun ajaran
            </p>
        </div>

        {{-- Filters and Action Button --}}
        <div class="mb-6 flex items-center justify-between">
            <button
                type="button"
                wire:click="$dispatch('bukaModalSemester')"
                class="inline-flex items-center gap-2 rounded-lg px-4 py-2.5 text-sm font-medium text-white transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2"
                style="background-color:#1B5E4F;font-family:'Inter',sans-serif;"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Tambah Semester
            </button>
        </div>

        {{-- Filter --}}
        <div class="mb-4 flex items-center gap-4">
            <div class="w-64">
                <label for="filter-tahun-ajaran" class="mb-1.5 block text-sm font-medium" style="color:#1A2422;font-family:'Inter',sans-serif;">
                    Filter Tahun Ajaran
                </label>
                <select
                    id="filter-tahun-ajaran"
                    wire:model.live="filterTahunAjaran"
                    class="w-full rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2"
                    style="background-color:#FFFFFF;border:1.5px solid #D1D5DB;color:#1A2422;font-family:'Inter',sans-serif;"
                >
                    <option value="">Semua Tahun Ajaran</option>
                    @foreach($tahunAjarans as $tahunAjaran)
                        <option value="{{ $tahunAjaran->id }}">{{ $tahunAjaran->label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" style="font-family: 'Inter', sans-serif;">
                    <thead class="bg-[#1B5E4F] text-white">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                                Tahun Ajaran
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                                Jenis
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                                Periode
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($semesters as $index => $semester)
                            <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-gray-100 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-[#1A2422]">
                                        {{ $semester->tahunAjaran->label }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium"
                                        style="background-color:{{ $semester->jenis === 'Ganjil' ? '#E0F2F1' : '#FEF3C7' }};color:{{ $semester->jenis === 'Ganjil' ? '#1B5E4F' : '#92400E' }};"
                                    >
                                        {{ $semester->jenis }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $semester->tanggal_mulai->tgl() }} — {{ $semester->tanggal_selesai->tgl() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($semester->status_aktif)
                                        <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold text-white" style="background-color:#1B5E4F;">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium" style="background-color:#F3F4F6;color:#6B7280;">
                                            Tidak Aktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- Edit Button --}}
                                        <button
                                            type="button"
                                            wire:click="$dispatch('bukaModalSemester', { id: {{ $semester->id }} })"
                                            class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium transition-colors focus:outline-none focus:ring-2"
                                            style="background-color:#F4F5F4;color:#1A2422;border:1px solid #D1D5DB;"
                                            title="Edit semester"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                            </svg>
                                            Edit
                                        </button>

                                        {{-- Delete Button --}}
                                        <button
                                            type="button"
                                            wire:click="bukaModalKonfirmasi({{ $semester->id }})"
                                            class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium text-white transition-colors focus:outline-none focus:ring-2"
                                            style="background-color:#9B3A3A;"
                                            title="Hapus semester"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <p class="text-lg font-medium text-gray-700 mb-1">Belum ada semester</p>
                                        <p class="text-sm text-gray-500">Klik tombol "Tambah Semester" untuk membuat periode semester baru</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer info --}}
            @if($semesters->isNotEmpty())
                <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
                    <p class="text-sm text-gray-600">
                        Total: <span class="font-medium text-[#1A2422]">{{ $semesters->count() }}</span> semester
                    </p>
                </div>
            @endif
        </div>
    </div>

    {{-- Modal Semester Component --}}
    <livewire:admin.master-data.semester.modal-semester />

    {{-- Modal Konfirmasi Hapus --}}
    @if($showModalKonfirmasi)
        <div
            class="fixed inset-0 z-50 flex items-center justify-center p-4 pointer-events-none"
            role="dialog"
            aria-modal="true"
            aria-labelledby="modal-konfirmasi-title"
        >
            {{-- Backdrop --}}
            <div
                class="absolute inset-0 bg-black/40"
                wire:click="tutupModalKonfirmasi"
                aria-hidden="true"
            ></div>

            {{-- Panel --}}
            <div
                class="relative w-full max-w-md rounded-2xl bg-white shadow-xl"
                style="font-family:'Inter',sans-serif;"
            >
                {{-- Header --}}
                <div class="flex items-start gap-4 px-6 pt-6 pb-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full" style="background-color:#FEE2E2;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" style="color:#9B3A3A;" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 id="modal-konfirmasi-title" class="text-base font-semibold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
                            Konfirmasi Penghapusan
                        </h3>
                        <div class="mt-2 text-sm text-gray-600">
                            <p class="mb-2">
                                Anda akan menghapus semester <strong class="font-semibold text-[#1A2422]">{{ $semesterHapus?->jenis }}</strong>
                                @if($semesterHapus)
                                    tahun ajaran <strong class="font-semibold text-[#1A2422]">{{ $semesterHapus->tahunAjaran->label }}</strong>.
                                @endif
                            </p>

                            @if($semesterHapus)
                            <div class="mt-3 p-3 rounded-lg" style="background-color:#FEF3C7;border-left:3px solid #C9A227;">
                                <p class="font-medium mb-2" style="color:#1A2422;font-size:0.8125rem;">
                                    Konsekuensi penghapusan:
                                </p>
                                <ul class="space-y-1 ml-4 text-xs" style="color:#4B5563;list-style-type:disc;">
                                    <li>Semua riwayat akademik semester ini akan terhapus permanen</li>
                                    <li>Data laporan semester terkait tidak dapat dibuat ulang</li>
                                    <li>Data tidak dapat dikembalikan setelah dihapus</li>
                                </ul>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-end gap-3 border-t px-6 py-4" style="border-color:#E5E7EB;">
                    <button
                        type="button"
                        wire:click="tutupModalKonfirmasi"
                        class="inline-flex items-center rounded-lg px-4 py-2 text-sm font-medium transition-colors focus:outline-none"
                        style="background-color:#F4F5F4;color:#1A2422;border:1px solid #D1D5DB;"
                    >
                        Batalkan
                    </button>
                    <button
                        type="button"
                        wire:click="delete"
                        wire:loading.attr="disabled"
                        wire:target="delete"
                        class="inline-flex items-center rounded-lg px-4 py-2 text-sm font-medium text-white transition-colors focus:outline-none disabled:opacity-70"
                        style="background-color:#9B3A3A;"
                    >
                        <span wire:loading.remove wire:target="delete">Ya, Hapus</span>
                        <span wire:loading wire:target="delete" class="flex items-center gap-2">
                            <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
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
