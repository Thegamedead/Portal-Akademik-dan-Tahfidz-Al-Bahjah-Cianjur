<div class="space-y-6">
    {{-- PAGE HEADER --}}
    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;">
        <h1 class="text-2xl font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
            Log Aktivitas Sistem
        </h1>
        <p class="mt-2 text-sm" style="color:#6B7280;font-family:'Inter',sans-serif;">
            Pantau riwayat seluruh aksi yang tercatat dalam sistem.
        </p>
    </div>

    {{-- SINGLE ROW HEADER: Info LEFT + Search/Filter RIGHT --}}
    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;font-family:'Inter',sans-serif;">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">

            {{-- LEFT: Info Label & Clear Button --}}
            <div x-data="{ showClearModal: false }" class="flex flex-wrap items-center gap-3">
                <div class="flex items-center">
                    <span class="text-sm text-gray-500">
                        Total <strong style="color:#1A2422;">{{ $activities->total() }}</strong> log aktivitas
                    </span>
                </div>

                @if($activities->total() > 0)
                <div class="h-4 w-px bg-gray-300"></div>
                <button 
                    @click="showClearModal = true"
                    type="button"
                    class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md text-red-600 bg-red-50 border border-red-100 hover:bg-red-100 transition-colors"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Bersihkan Log
                </button>

                {{-- Custom Confirmation Modal (No Backdrop) --}}
                <template x-if="showClearModal">
                    <div class="fixed inset-0 z-50 flex items-center justify-center pointer-events-none" style="margin-bottom: 10vh;">
                        <div class="relative bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6 border border-gray-200 pointer-events-auto text-center">
                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Konfirmasi Hapus</h3>
                            <p class="text-sm text-gray-500 mb-6">Apakah Anda yakin ingin menghapus SEMUA log aktivitas? Tindakan ini tidak dapat dibatalkan.</p>
                            <div class="flex items-center justify-center gap-3">
                                <button @click="showClearModal = false" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Batal</button>
                                <button wire:click="clearAllLogs" @click="showClearModal = false" type="button" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">Ya, Hapus Semua</button>
                            </div>
                        </div>
                    </div>
                </template>
                @endif
            </div>

            {{-- RIGHT: Search Field + Filter Dropdown Button --}}
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 lg:min-w-[400px]">
                {{-- Search Input --}}
                <div class="flex-1">
                    <x-ui.search-field
                        wireModel="filterSearch"
                        placeholder="Cari deskripsi aksi..."
                    />
                </div>

                {{-- Filter Dropdown Button --}}
                <div x-data="{ open: false }" class="relative">
                    <button
                        @click="open = !open"
                        type="button"
                        class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none whitespace-nowrap cursor-pointer w-full sm:w-auto"
                        style="background-color:#FFFFFF;color:#1A2422;border:1.5px solid #D1D5DB;"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Filter
                        @if($filterLogName || $filterCauserType || $filterDateFrom || $filterDateTo)
                            <span class="ml-2 inline-flex items-center justify-center w-5 h-5 text-xs font-bold rounded-full" style="background-color:#1B5E4F;color:#FFFFFF;">
                                {{ collect([$filterLogName, $filterCauserType, $filterDateFrom, $filterDateTo])->filter()->count() }}
                            </span>
                        @endif
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div x-show="open"
                         @click.outside="open = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute z-50 right-0 mt-2 w-80 rounded-xl shadow-xl bg-white ring-1 ring-black ring-opacity-5 focus:outline-none overflow-hidden"
                         style="display: none; border: 1px solid #E5E7EB;">

                        <div class="p-4 space-y-3">
                            {{-- Filter Header --}}
                            <div class="flex items-center justify-between pb-2" style="border-bottom: 1px solid #E5E7EB;">
                                <h3 class="text-sm font-semibold" style="color:#1A2422;font-family:'Plus Jakarta Sans',sans-serif;">
                                    Filter Aktivitas
                                </h3>
                                @if($filterLogName || $filterCauserType || $filterDateFrom || $filterDateTo)
                                    <button
                                        wire:click="resetFilters"
                                        type="button"
                                        class="text-xs font-medium text-gray-500 hover:text-[#1B5E4F] transition-colors"
                                    >
                                        Reset
                                    </button>
                                @endif
                            </div>

                            {{-- Filter Kategori --}}
                            <div>
                                <label class="block text-xs font-medium mb-1.5" style="color:#6B7280;">Kategori Log</label>
                                <select wire:model.live="filterLogName" class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all" style="border:1.5px solid #D1D5DB;color:#1A2422;">
                                    <option value="">Semua</option>
                                    @foreach($logNames as $name)
                                        <option value="{{ $name }}">{{ ucfirst($name) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Filter Tipe Aktor --}}
                            <div>
                                <label class="block text-xs font-medium mb-1.5" style="color:#6B7280;">Tipe Aktor</label>
                                <select wire:model.live="filterCauserType" class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all" style="border:1.5px solid #D1D5DB;color:#1A2422;">
                                    <option value="">Semua</option>
                                    <option value="Admin">Admin</option>
                                    <option value="Guru">Guru</option>
                                    <option value="Siswa">Siswa</option>
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                {{-- Filter Dari Tanggal --}}
                                <div>
                                    <label class="block text-xs font-medium mb-1.5" style="color:#6B7280;">Dari Tanggal</label>
                                    <input type="date" wire:model.live="filterDateFrom" class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all" style="border:1.5px solid #D1D5DB;color:#1A2422;">
                                </div>

                                {{-- Filter Sampai Tanggal --}}
                                <div>
                                    <label class="block text-xs font-medium mb-1.5" style="color:#6B7280;">Sampai</label>
                                    <input type="date" wire:model.live="filterDateTo" class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all" style="border:1.5px solid #D1D5DB;color:#1A2422;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- Table --}}
    <div class="rounded-2xl bg-white shadow-sm overflow-hidden" style="border:1px solid #E5E7EB;font-family:'Inter',sans-serif;">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr style="background-color:#1B5E4F;">
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider" style="color:white;">Waktu</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider" style="color:white;">Aktor</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider" style="color:white;">Kategori</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider" style="color:white;">Deskripsi Aksi</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider" style="color:white;">Model Terkait</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider" style="color:white;">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color:#E5E7EB;">
                    @forelse($activities as $activity)
                    @php
                        // Format Kategori
                        $kategori = match($activity->log_name) {
                            'auth' => 'Otentikasi',
                            'default' => 'Aktivitas Umum',
                            'tahun_ajaran' => 'Tahun Ajaran',
                            'kelompok' => 'Kelompok Tahfidz',
                            default => ucfirst(str_replace('_', ' ', $activity->log_name))
                        };

                        // Format Model
                        $modelName = $activity->subject_type ? preg_replace('/(?<!^)[A-Z]/', ' $0', class_basename($activity->subject_type)) : '';
                        
                        // Format Deskripsi
                        $deskripsi = $activity->description;
                        if ($activity->log_name === 'auth') {
                            if (str_contains(strtolower($deskripsi), 'login')) $deskripsi = 'Melakukan Login ke sistem';
                            if (str_contains(strtolower($deskripsi), 'logout')) $deskripsi = 'Melakukan Logout dari sistem';
                        } else {
                            $deskripsi = str_replace(
                                ['created', 'updated', 'deleted'],
                                ['Membuat data baru:', 'Memperbarui data:', 'Menghapus data:'],
                                $deskripsi
                            );
                        }
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        {{-- Waktu --}}
                        <td class="px-6 py-4 text-sm whitespace-nowrap" style="color:#6B7280;font-family:'Inter',sans-serif;">
                            {{ $activity->created_at->tglJam() }}
                        </td>
                        {{-- Aktor --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($activity->causer)
                                <div class="text-sm font-medium" style="color:#1A2422;font-family:'Inter',sans-serif;">{{ $activity->causer->name ?? $activity->causer->email ?? '-' }}</div>
                                <div class="text-xs uppercase tracking-wide mt-0.5" style="color:#6B7280;">{{ class_basename($activity->causer_type) }}</div>
                            @else
                                <span class="text-sm italic" style="color:#9CA3AF;font-family:'Inter',sans-serif;">Sistem (Otomatis)</span>
                            @endif
                        </td>
                        {{-- Kategori --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold" style="background-color:#EFF6FF;color:#3B82F6;border:1px solid #BFDBFE;">
                                {{ $kategori }}
                            </span>
                        </td>
                        {{-- Deskripsi --}}
                        <td class="px-6 py-4 text-sm max-w-xs truncate" style="color:#1A2422;font-family:'Inter',sans-serif;" title="{{ $activity->description }}">
                            {{ $deskripsi }}
                        </td>
                        {{-- Model Terkait --}}
                        <td class="px-6 py-4 text-sm whitespace-nowrap" style="font-family:'Inter',sans-serif;">
                            @if($activity->subject)
                                <span class="font-medium" style="color:#1A2422;">{{ $modelName }}</span>
                                <span style="color:#9CA3AF;"> #{{ $activity->subject_id }}</span>
                            @else
                                <span class="italic" style="color:#9CA3AF;">—</span>
                            @endif
                        </td>
                        {{-- Aksi Detail --}}
                        <td class="px-6 py-4 text-center">
                            <button wire:click="showDetail({{ $activity->id }})"
                                class="inline-flex items-center justify-center p-1.5 rounded-md transition-all"
                                style="color:#1B5E4F;background-color:#F0FDF9;border:1px solid #B2DFDB;"
                                onmouseover="this.style.backgroundColor='#1B5E4F';this.style.color='white';"
                                onmouseout="this.style.backgroundColor='#F0FDF9';this.style.color='#1B5E4F';"
                                title="Lihat Detail Log">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-16 h-16 mb-4" style="color:#D1D5DB;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <p class="text-sm font-medium" style="color:#6B7280;">Belum ada log aktivitas</p>
                                <p class="text-xs mt-1" style="color:#9CA3AF;">Tidak ada data yang sesuai dengan filter pencarian Anda.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($activities->hasPages())
        <div class="px-6 py-4" style="border-top:1px solid #E5E7EB;">
            {{ $activities->links() }}
        </div>
        @endif
    </div>

    {{-- Detail Modal --}}
    @if($showDetailModal && $detailActivity)
    <div class="fixed inset-0 z-50 flex items-center justify-center pointer-events-none" role="dialog" aria-modal="true" style="pointer-events:none;">
        <div class="fixed inset-0 pointer-events-none" wire:click="closeDetail" style="pointer-events:auto;"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full p-6 border border-gray-200" style="pointer-events:auto; margin-bottom: 10vh;">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Detail Log Aktivitas #{{ $detailActivity->id }}</h2>
                    <button wire:click="closeDetail" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <dl class="grid grid-cols-2 gap-4 mb-4 text-sm">
                    @php
                        $detKategori = match($detailActivity->log_name) {
                            'auth' => 'Otentikasi',
                            'default' => 'Aktivitas Umum',
                            'tahun_ajaran' => 'Tahun Ajaran',
                            'kelompok' => 'Kelompok Tahfidz',
                            default => ucfirst(str_replace('_', ' ', $detailActivity->log_name))
                        };
                        $detModelName = $detailActivity->subject_type ? preg_replace('/(?<!^)[A-Z]/', ' $0', class_basename($detailActivity->subject_type)) : '';
                        $detDeskripsi = $detailActivity->description;
                        if ($detailActivity->log_name === 'auth') {
                            if (str_contains(strtolower($detDeskripsi), 'login')) $detDeskripsi = 'Melakukan Login ke sistem';
                            if (str_contains(strtolower($detDeskripsi), 'logout')) $detDeskripsi = 'Melakukan Logout dari sistem';
                        } else {
                            $detDeskripsi = str_replace(
                                ['created', 'updated', 'deleted'],
                                ['Membuat data baru:', 'Memperbarui data:', 'Menghapus data:'],
                                $detDeskripsi
                            );
                        }
                    @endphp
                    <div>
                        <dt class="font-medium text-gray-500">Waktu</dt>
                        <dd class="text-gray-900">{{ $detailActivity->created_at->tglJam() }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Kategori</dt>
                        <dd><span class="px-2 py-0.5 rounded text-xs bg-blue-100 text-blue-800">{{ $detKategori }}</span></dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Aktor</dt>
                        <dd class="text-gray-900">{{ $detailActivity->causer?->name ?? $detailActivity->causer?->email ?? 'Sistem (Otomatis)' }} @if($detailActivity->causer) <span class="text-xs text-gray-500">({{ class_basename($detailActivity->causer_type) }})</span> @endif</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Model Terkait</dt>
                        <dd class="text-gray-900">
                            @if($detailActivity->subject)
                                {{ $detModelName }} <span class="text-gray-400">#{{ $detailActivity->subject_id }}</span>
                            @else
                                <span class="text-gray-400 italic">—</span>
                            @endif
                        </dd>
                    </div>
                    <div class="col-span-2">
                        <dt class="font-medium text-gray-500">Deskripsi</dt>
                        <dd class="text-gray-900">{{ $detDeskripsi }}</dd>
                    </div>
                </dl>

                @if($detailActivity->properties && $detailActivity->properties->isNotEmpty())
                <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Properties:</h3>
                    <div class="bg-gray-50 rounded-lg p-3 overflow-auto max-h-60">
                        @if($detailActivity->properties->has('old') && $detailActivity->properties->has('attributes'))
                        <div class="grid grid-cols-2 gap-3 text-xs">
                            <div>
                                <p class="font-semibold text-red-600 mb-1">Sebelum:</p>
                                <pre class="text-gray-700 whitespace-pre-wrap">{{ json_encode($detailActivity->properties['old'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                            <div>
                                <p class="font-semibold text-green-600 mb-1">Sesudah:</p>
                                <pre class="text-gray-700 whitespace-pre-wrap">{{ json_encode($detailActivity->properties['attributes'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        </div>
                        @else
                        <pre class="text-xs text-gray-700 whitespace-pre-wrap">{{ json_encode($detailActivity->properties->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        @endif
                    </div>
                </div>
                @endif
            </div>
    </div>
    @endif
</div>
