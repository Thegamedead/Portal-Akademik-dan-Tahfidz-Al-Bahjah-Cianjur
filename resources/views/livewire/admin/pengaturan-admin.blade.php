<div class="space-y-6">

    {{-- PAGE HEADER --}}
    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;">
        <h1 class="text-2xl font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">Pengaturan Sistem</h1>
        <p class="mt-2 text-sm" style="color:#6B7280;font-family:'Inter',sans-serif;">Kelola konfigurasi dan fitur sistem Tahfidz</p>
    </div>

    {{-- CONTENT --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Kolom Kiri: Logo Sekolah -->
        <div class="rounded-2xl bg-white shadow-sm p-6 flex flex-col" style="border:1px solid #E5E7EB;">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2" style="border-bottom:1px solid #E5E7EB;font-family:'Plus Jakarta Sans',sans-serif;color:white;">Logo Sekolah</h3>
            <div class="bg-gray-50 rounded-xl p-5 border border-gray-100 flex-grow flex flex-col">
                <div class="grid grid-cols-1 gap-6 flex-grow">
                    <!-- Kolom Logo -->
                    <div class="flex flex-col h-full">
                        <label class="block text-xs font-medium text-gray-700 mb-2">Logo Sekolah (PNG/JPG)</label>
                        <label class="relative h-40 w-full flex items-center justify-center bg-white border-2 border-dashed border-gray-300 rounded-lg p-4 cursor-pointer hover:bg-gray-50 hover:border-zamrud transition-all group overflow-hidden">
                            <input type="file" wire:model="logo" accept="image/*" class="sr-only">

                            @if ($logo)
                                <img src="{{ $logo->temporaryUrl() }}" alt="Preview" class="max-h-full max-w-full object-contain">
                            @elseif ($existingLogo)
                                <img src="{{ Storage::url($existingLogo) }}" alt="Logo" class="max-h-full max-w-full object-contain group-hover:opacity-40 transition-opacity">
                                <div class="absolute inset-0 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <div class="bg-zamrud text-white rounded-full p-2 mb-1 shadow-sm">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                        </svg>
                                    </div>
                                    <span class="text-xs font-medium text-zamrud bg-white px-2 py-0.5 rounded shadow-sm border border-zamrud border-opacity-20">Ganti Logo</span>
                                </div>
                            @else
                                <div class="text-center">
                                    <svg class="mx-auto h-8 w-8 text-gray-400 group-hover:text-zamrud transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                    <span class="mt-2 block text-xs font-medium text-gray-500 group-hover:text-zamrud transition-colors">Klik untuk unggah</span>
                                </div>
                            @endif

                            {{-- Loading Overlay saat upload logo --}}
                            <div wire:loading.flex wire:target="logo" class="absolute inset-0 flex-col items-center justify-center rounded-lg z-10" style="background:rgba(255,255,255,0.92);backdrop-filter:blur(2px);">
                                <svg class="animate-spin h-7 w-7 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-20" cx="12" cy="12" r="10" stroke="#1B5E4F" stroke-width="3"/>
                                    <path class="opacity-90" fill="#1B5E4F" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                <span class="text-[10px] font-semibold uppercase tracking-wider" style="color:#1B5E4F;">Mengunggah…</span>
                            </div>
                        </label>
                        @error('logo') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Stack Fitur & Log -->
        <div class="flex flex-col space-y-6">
            <!-- Feature Toggles Section -->
            <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">Fitur Aplikasi</h3>
                <div class="space-y-4">
                    @forelse($features as $feature)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-100">
                            <div>
                                <h4 class="font-medium text-gray-900">{{ $feature->name }}</h4>
                                <p class="text-sm text-gray-500 mt-1">{{ $feature->description }}</p>
                            </div>
                            <button wire:click="toggleFeature('{{ $feature->key }}')" type="button" class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none" style="background-color: {{ $feature->is_enabled ? '#1B5E4F' : '#E5E7EB' }};" role="switch" aria-checked="{{ $feature->is_enabled ? 'true' : 'false' }}">
                                <span aria-hidden="true" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out" style="transform: {{ $feature->is_enabled ? 'translateX(1.25rem)' : 'translateX(0)' }};"></span>
                            </button>
                        </div>
                    @empty
                        <div class="text-center py-6 text-gray-500 text-sm">Belum ada fitur yang dapat dikonfigurasi saat ini.</div>
                    @endforelse
                </div>
            </div>

            <!-- Alat Administratif Section -->
            <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">Alat Administratif</h3>
                <div class="grid grid-cols-1 gap-6">
                    <a href="{{ route('admin.audit-log') }}" class="group block p-5 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 hover:border-zamrud transition duration-150 ease-in-out shadow-sm">
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-zamrud bg-opacity-10 text-zamrud group-hover:bg-zamrud group-hover:text-white transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-base font-semibold text-gray-900 group-hover:text-zamrud transition-colors">Log Aktivitas (Audit Log)</h4>
                                <p class="mt-1 text-sm text-gray-500">Pantau riwayat perubahan dan aktivitas pengguna.</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

    </div>  {{-- /CONTENT --}}

</div>
