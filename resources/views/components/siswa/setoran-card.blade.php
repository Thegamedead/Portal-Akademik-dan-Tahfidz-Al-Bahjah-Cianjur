@props([
    'setoran',
])

@php
    $isObject = is_object($setoran);
    
    $keputusan = $isObject ? $setoran->keputusan : ($setoran['keputusan'] ?? null);
    $isLanjut = $keputusan instanceof \App\Enums\Keputusan
        ? $keputusan->value === 'Lanjut'
        : $keputusan === 'Lanjut';
        
    $badgeConfig = $isLanjut
        ? ['bg' => 'bg-green-100', 'text' => 'text-green-700']
        : ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700'];
        
    $surahNama = $isObject 
        ? ($setoran->surah->nama_latin ?? 'Surah ' . $setoran->surah_id)
        : (($setoran['surah']['nama_latin'] ?? null) ?? 'Surah ' . ($setoran['surah_id'] ?? '-'));
        
    $ayatMulai = $isObject ? $setoran->ayat_mulai : ($setoran['ayat_mulai'] ?? '-');
    $ayatSelesai = $isObject ? $setoran->ayat_selesai : ($setoran['ayat_selesai'] ?? '-');
    
    $jenisSetoran = $isObject ? $setoran->jenis_setoran : ($setoran['jenis_setoran'] ?? null);
    $jenisSetoranLabel = $jenisSetoran instanceof \App\Enums\JenisSetoran 
        ? $jenisSetoran->label() 
        : $jenisSetoran;
        
    $predikat = $isObject ? $setoran->predikat : ($setoran['predikat'] ?? null);
    $predikatValue = $predikat instanceof \App\Enums\SkorKomponen 
        ? $predikat->value 
        : $predikat;
        
    $tanggal = $isObject 
        ? $setoran->absensi->tanggal->isoFormat('D MMM')
        : (\Carbon\Carbon::parse($setoran['absensi']['tanggal'] ?? ($setoran['created_at'] ?? now()))->isoFormat('D MMM'));
@endphp

<div {{ $attributes->merge(['class' => 'flex items-start gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition']) }}>
    {{-- Icon Badge --}}
    <div class="shrink-0 w-10 h-10 rounded-full flex items-center justify-center {{ $badgeConfig['bg'] }} {{ $badgeConfig['text'] }}">
        @if($isLanjut)
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
            </svg>
        @else
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>
            </svg>
        @endif
    </div>

    {{-- Content --}}
    <div class="flex-1 min-w-0">
        <p class="text-sm font-medium text-gray-900">
            {{ $surahNama }}
        </p>
        <p class="text-xs text-gray-600 mt-0.5">
            Ayat {{ $ayatMulai }}-{{ $ayatSelesai }} |
            {{ $jenisSetoranLabel }}
        </p>
        <div class="flex items-center gap-2 mt-1 flex-wrap">
            <x-ui.status-badge
                size="sm"
                :status="$predikatValue ?? '-'"
                :custom-colors="($predikatValue === 'Lancar')
                    ? ['bg' => '#D1FAE5', 'text' => '#065F46']
                    : ['bg' => '#FEF3C7', 'text' => '#92400E']"
            />
            <span class="text-xs text-gray-500">
                {{ $tanggal }}
            </span>
        </div>
    </div>
</div>
