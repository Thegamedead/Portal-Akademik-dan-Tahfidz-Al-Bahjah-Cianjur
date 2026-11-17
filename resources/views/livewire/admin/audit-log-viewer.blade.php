@section('title', 'Audit Log Aktivitas')
@section('page-title', 'Audit Log Aktivitas Sistem')

<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-[#1A2422] font-jakarta">Audit Log Aktivitas</h1>
                <p class="mt-1 text-sm text-gray-500">Rekam jejak aktivitas seluruh pengguna pada sistem.</p>
            </div>
        </div>

        @if(!$activityLogInstalled)
            <div class="rounded-lg bg-yellow-50 border border-yellow-200 p-6 text-center">
                <svg class="mx-auto h-12 w-12 text-yellow-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <h3 class="font-semibold text-yellow-800 mb-2">Paket Audit Log Belum Terinstall</h3>
                <p class="text-sm text-yellow-700">Jalankan: <code class="bg-yellow-100 px-2 py-0.5 rounded font-mono">composer require spatie/laravel-activitylog</code></p>
            </div>
        @else
            {{-- Filter Panel --}}
            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 mb-6 flex flex-wrap gap-4 items-end">
                <div class="w-full md:w-48">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Role Pengguna</label>
                    <select wire:model.live="filters.causer_type" class="mt-1 block w-full pl-3 pr-10 py-2 text-sm border-gray-300 focus:outline-none focus:ring-[#1B5E4F] focus:border-[#1B5E4F] rounded-md cursor-pointer">
                        <option value="">Semua Role</option>
                        <option value="Admin">Admin</option>
                        <option value="Guru">Guru</option>
                        <option value="Siswa">Siswa</option>
                    </select>
                </div>

                <div class="w-full md:w-64">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Deskripsi Aktivitas</label>
                    <x-ui.search-field wireModel="filters.description" placeholder="Cari aktivitas..." />
                </div>

                <div class="w-full md:w-36">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Dari Tanggal</label>
                    <input type="date" wire:model.live="filters.tanggal_mulai" class="block w-full sm:text-sm border-gray-300 focus:ring-[#1B5E4F] focus:border-[#1B5E4F] rounded-md py-2 px-3 cursor-pointer">
                </div>
                <div class="w-full md:w-36">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                    <input type="date" wire:model.live="filters.tanggal_akhir" class="block w-full sm:text-sm border-gray-300 focus:ring-[#1B5E4F] focus:border-[#1B5E4F] rounded-md py-2 px-3 cursor-pointer">
                </div>

                <div class="ml-auto">
                    <button wire:click="resetFilters" type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#1B5E4F] cursor-pointer transition-colors">
                        Reset
                    </button>
                </div>
            </div>

            {{-- Log Table --}}
            <div class="bg-white shadow overflow-hidden sm:rounded-lg border border-gray-200 relative min-h-[200px]">
                <div wire:loading wire:target="filters" class="absolute inset-0 z-10 bg-white/50 backdrop-blur-sm flex items-center justify-center">
                    <div class="flex items-center px-4 py-2 bg-white rounded-lg shadow text-sm text-[#1B5E4F]">
                        <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-[#1B5E4F]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Memuat log...
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-[#1B5E4F] text-white">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Waktu</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Pelaku</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Aktivitas</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Detail</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($logs as $log)
                            <tr class="hover:bg-gray-100 transition-colors">
                                <td class="px-6 py-3 whitespace-nowrap text-xs text-gray-500">
                                    {{ $log->created_at->tglJam() }}
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $log->causer?->nama ?? $log->causer?->name ?? 'Sistem' }}</div>
                                    <div class="text-xs text-gray-400">{{ class_basename($log->causer_type ?? 'System') }}</div>
                                </td>
                                <td class="px-6 py-3 text-sm text-gray-900">{{ $log->description }}</td>
                                <td class="px-6 py-3 text-xs text-gray-500">
                                    @if($log->properties && count($log->properties))
                                        <pre class="text-[10px] bg-gray-50 p-1 rounded overflow-x-auto max-w-xs">{{ json_encode($log->properties, JSON_PRETTY_PRINT) }}</pre>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <h3 class="text-sm font-medium text-gray-900">Tidak ada log aktivitas</h3>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
