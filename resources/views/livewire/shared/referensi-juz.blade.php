<div>
    {{-- Arabic font support --}}
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap');

        .font-arabic {
            font-family: 'Amiri', serif;
        }
    </style>

    <div class="max-w-7xl mx-auto">
        {{-- Header --}}
        <div class="rounded-2xl bg-white shadow-sm p-6 mb-6" style="border: 1px solid #E5E7EB;">
            <h1 class="text-2xl font-bold" style="color: #1A2422; font-family: 'Plus Jakarta Sans', sans-serif;">
                Referensi Juz Al-Quran
            </h1>
            <p class="text-sm mt-2" style="color: #6B7280; font-family: 'Inter', sans-serif;">
                Referensi 30 Juz Al-Quran beserta surah-surah yang terkandung di dalamnya
            </p>
        </div>

    {{-- Grid Juz Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
        @foreach($juzs as $juz)
            <button
                wire:click="selectJuz({{ $juz->id }})"
                class="bg-white rounded-xl shadow-sm p-6 transition-all duration-200 hover:shadow-md group"
                style="border: 2px solid {{ $selectedJuz == $juz->id ? '#1B5E4F' : '#E5E7EB' }};"
                onmouseover="if(this.style.borderColor !== 'rgb(27, 94, 79)') this.style.borderColor='#D1D5DB'"
                onmouseout="if(this.style.borderColor !== 'rgb(27, 94, 79)') this.style.borderColor='#E5E7EB'"
            >
                <div class="flex flex-col items-center justify-center">
                    {{-- Juz Number --}}
                    <div class="w-16 h-16 rounded-full flex items-center justify-center mb-3 transition-colors"
                         style="background-color: {{ $selectedJuz == $juz->id ? '#1B5E4F' : '#F4F5F4' }};">
                        <span class="text-2xl font-bold transition-colors"
                              style="color: {{ $selectedJuz == $juz->id ? '#FFFFFF' : '#1B5E4F' }}; font-family: 'Plus Jakarta Sans', sans-serif;">
                            {{ $juz->juz_ke }}
                        </span>
                    </div>

                    {{-- Juz Label --}}
                    <span class="text-sm font-semibold" style="color: #1A2422; font-family: 'Plus Jakarta Sans', sans-serif;">
                        Juz {{ $juz->juz_ke }}
                    </span>

                    {{-- Ayat Count --}}
                    <span class="text-xs mt-1" style="color: #6B7280;">
                        {{ number_format($juz->jumlah_ayat) }} Ayat
                    </span>

                    {{-- Urutan Hafalan Badge --}}
                    @if($juz->urutan_hafalan)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium mt-2"
                              style="background-color: #E8F5F1; color: #1B5E4F;">
                            Urutan {{ $juz->urutan_hafalan }}
                        </span>
                    @endif
                </div>
            </button>
        @endforeach
    </div>

    {{-- Detail Modal (Slide from right) --}}
    @if($juzDetail)
        <div
            x-data="{ show: @entangle('selectedJuz').live }"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-hidden pointer-events-none"
            style="display: none;"
        >
            {{-- Backdrop --}}
            <div
                @click="$wire.closeDetail()"
                class="absolute inset-0 bg-black/40 transition-opacity"
            ></div>

            {{-- Panel --}}
            <div
                x-show="show"
                x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in duration-200 transform"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="absolute right-0 top-0 h-full w-full max-w-2xl bg-white shadow-2xl overflow-y-auto"
            >
                {{-- Header --}}
                <div class="sticky top-0 z-10 bg-white px-6 py-4" style="border-bottom: 2px solid #E5E7EB;">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            {{-- Juz Badge --}}
                            <div class="w-14 h-14 rounded-full flex items-center justify-center"
                                 style="background-color: #1B5E4F;">
                                <span class="text-xl font-bold" style="color: #FFFFFF; font-family: 'Plus Jakarta Sans', sans-serif;">
                                    {{ $juzDetail->juz_ke }}
                                </span>
                            </div>

                            <div>
                                <h2 class="text-xl font-bold" style="color: #1A2422; font-family: 'Plus Jakarta Sans', sans-serif;">
                                    Juz {{ $juzDetail->juz_ke }}
                                </h2>
                                <p class="text-sm" style="color: #6B7280;">
                                    {{ number_format($juzDetail->jumlah_ayat) }} Ayat
                                    @if($juzDetail->urutan_hafalan)
                                        • Urutan Hafalan: {{ $juzDetail->urutan_hafalan }}
                                    @endif
                                </p>
                            </div>
                        </div>

                        {{-- Close Button --}}
                        <button
                            wire:click="closeDetail"
                            class="rounded-lg p-2 transition-colors"
                            style="color: #6B7280;"
                            onmouseover="this.style.backgroundColor='#F4F5F4'"
                            onmouseout="this.style.backgroundColor='transparent'"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Content --}}
                <div class="px-6 py-6">
                    {{-- Juz Range Info --}}
                    <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl p-6 mb-6" style="border: 2px solid #E8F5F1;">
                        <h3 class="text-sm font-semibold mb-4" style="color: #1B5E4F; font-family: 'Plus Jakarta Sans', sans-serif;">
                            Cakupan Juz
                        </h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs font-medium mb-1" style="color: #6B7280;">Ayat Awal</p>
                                <p class="text-sm font-semibold" style="color: #1A2422;">
                                    {{ $juzDetail->awal_surah }} : {{ $juzDetail->awal_ayat }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs font-medium mb-1" style="color: #6B7280;">Ayat Akhir</p>
                                <p class="text-sm font-semibold" style="color: #1A2422;">
                                    {{ $juzDetail->akhir_surah }} : {{ $juzDetail->akhir_ayat }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Surahs List --}}
                    <div>
                        <h3 class="text-sm font-semibold mb-4" style="color: #1A2422; font-family: 'Plus Jakarta Sans', sans-serif;">
                            Daftar Surah ({{ $juzDetail->surahs->count() }} Surah)
                        </h3>

                        <div class="space-y-3">
                            @forelse($juzDetail->surahs as $surah)
                                <div class="bg-white rounded-lg p-4 transition-colors"
                                     style="border: 1.5px solid #E5E7EB;"
                                     onmouseover="this.style.backgroundColor='#F9FAFB'"
                                     onmouseout="this.style.backgroundColor='#FFFFFF'">
                                    <div class="flex items-start gap-3">
                                        {{-- Surah Number Badge --}}
                                        <div class="shrink-0 w-10 h-10 rounded-lg flex items-center justify-center"
                                             style="background-color: #F4F5F4;">
                                            <span class="text-sm font-bold" style="color: #1B5E4F;">
                                                {{ $surah->nomor_surah }}
                                            </span>
                                        </div>

                                        {{-- Surah Info --}}
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-start justify-between gap-2">
                                                <div class="flex-1">
                                                    <h4 class="text-base font-semibold" style="color: #1A2422; font-family: 'Plus Jakarta Sans', sans-serif;">
                                                        {{ $surah->nama_latin }}
                                                    </h4>
                                                    <p class="text-lg font-arabic mt-0.5" style="color: #1B5E4F; direction: rtl;">
                                                        {{ $surah->nama_arab }}
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="flex flex-wrap items-center gap-2 mt-2">
                                                {{-- Tempat Turun --}}
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                                      style="background-color: {{ $surah->tempat_turun === 'Makkiyah' ? '#FEF3C7' : '#E0F2FE' }};
                                                             color: {{ $surah->tempat_turun === 'Makkiyah' ? '#92400E' : '#0C4A6E' }};">
                                                    {{ $surah->tempat_turun }}
                                                </span>

                                                {{-- Jumlah Ayat --}}
                                                <span class="text-xs" style="color: #6B7280;">
                                                    {{ $surah->jumlah_ayat }} Ayat
                                                </span>

                                                {{-- Range Ayat di Juz ini --}}
                                                @if($surah->pivot->ayat_mulai && $surah->pivot->ayat_selesai)
                                                    <span class="text-xs" style="color: #6B7280;">
                                                        • Ayat {{ $surah->pivot->ayat_mulai }} - {{ $surah->pivot->ayat_selesai }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-3" style="color: #D1D5DB;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                    <p class="text-sm" style="color: #6B7280;">Tidak ada data surah</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    </div>
</div>
