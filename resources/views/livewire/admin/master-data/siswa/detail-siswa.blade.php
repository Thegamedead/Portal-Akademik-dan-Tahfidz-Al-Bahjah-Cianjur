{{--
    View: livewire/admin/master-data/siswa/detail-siswa.blade.php
    Komponen Livewire: App\Livewire\Admin\MasterData\Siswa\DetailSiswa

    Read-only detail view untuk Siswa (Requirements 3.5, 8.6, 8.7)
--}}
<div class="space-y-6">

    {{-- Header --}}
    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
                    Detail Data Siswa
                </h2>
                <p class="mt-1 text-sm" style="color:#6B7280;font-family:'Inter',sans-serif;">
                    Informasi lengkap siswa dan riwayat perpindahan kelompok
                </p>
            </div>
            <div class="flex gap-3">
                <button
                    wire:click="kembali"
                    type="button"
                    class="px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2"
                    style="background-color:#FFFFFF;color:#1A2422;border:1.5px solid #D1D5DB;font-family:'Inter',sans-serif;"
                >
                    Kembali
                </button>
            </div>
        </div>
    </div>

    {{-- Tab Navigation --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="flex border-b-2 border-gray-300 bg-gray-100 px-2 pt-2 gap-1">
            <button wire:click="setTab('data')"
                    class="px-6 py-3 text-sm font-bold transition-all rounded-t-lg {{ $activeTab === 'data' ? 'bg-[#1B5E4F] text-white shadow-[0_-3px_0_0_#155441] translate-y-[2px] -mb-[2px]' : 'bg-white border-transparent text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Data Siswa
                </span>
            </button>
            <button wire:click="setTab('riwayat_kelompok')"
                    class="px-6 py-3 text-sm font-bold transition-all rounded-t-lg {{ $activeTab === 'riwayat_kelompok' ? 'bg-[#1B5E4F] text-white shadow-[0_-3px_0_0_#155441] translate-y-[2px] -mb-[2px]' : 'bg-white border-transparent text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Riwayat Kelompok
                </span>
            </button>
            <button wire:click="setTab('riwayat_absensi')"
                    class="px-6 py-3 text-sm font-bold transition-all rounded-t-lg {{ $activeTab === 'riwayat_absensi' ? 'bg-[#1B5E4F] text-white shadow-[0_-3px_0_0_#155441] translate-y-[2px] -mb-[2px]' : 'bg-white border-transparent text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Riwayat Absensi
                </span>
            </button>
        </div>

        <div class="p-6">
            @if($activeTab === 'data')
                {{-- TAB: Data Siswa --}}
                @include('livewire.admin.master-data.siswa.partials.tab-data-siswa')
            @elseif($activeTab === 'riwayat_kelompok')
                {{-- TAB: Riwayat Kelompok --}}
                @include('livewire.admin.master-data.siswa.partials.tab-riwayat-kelompok')
            @elseif($activeTab === 'riwayat_absensi')
                {{-- TAB: Riwayat Absensi --}}
                @include('livewire.admin.master-data.siswa.partials.tab-riwayat-absensi')
            @endif
        </div>
    </div>

</div>
