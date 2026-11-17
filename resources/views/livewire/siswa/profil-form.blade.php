{{-- livewire/siswa/profil-form.blade.php --}}
<div class="space-y-3 text-sm">
    {{-- Nama --}}
    <div class="flex items-center gap-3 py-2 border-b" style="border-color:#F3F4F6;">
        <span class="w-20 text-xs font-medium shrink-0" style="color:#6B7280;">Nama</span>
        <span class="font-semibold" style="color:#1A2422;">{{ $siswa->nama }}</span>
    </div>
    {{-- NIS --}}
    <div class="flex items-center gap-3 py-2 border-b" style="border-color:#F3F4F6;">
        <span class="w-20 text-xs font-medium shrink-0" style="color:#6B7280;">NIS</span>
        <span style="color:#374151;">{{ $siswa->nis ?? '-' }}</span>
    </div>
    {{-- Kelas --}}
    <div class="flex items-center gap-3 py-2 border-b" style="border-color:#F3F4F6;">
        <span class="w-20 text-xs font-medium shrink-0" style="color:#6B7280;">Kelas</span>
        <span style="color:#374151;">{{ $siswa->kelas ?? '-' }}</span>
    </div>
    {{-- Kelompok --}}
    <div class="flex items-center gap-3 py-2 border-b" style="border-color:#F3F4F6;">
        <span class="w-20 text-xs font-medium shrink-0" style="color:#6B7280;">Kelompok</span>
        @if($siswa->kelompok)
            <div>
                <span class="font-medium" style="color:#1B5E4F;">{{ $siswa->kelompok->nama }}</span>
                <span class="text-xs ml-1" style="color:#6B7280;">({{ $siswa->kelompok->tingkat ?? '-' }})</span>
            </div>
        @else
            <span class="text-xs italic" style="color:#9CA3AF;">Belum ditempatkan</span>
        @endif
    </div>
    {{-- Guru Pembimbing --}}
    <div class="flex items-center gap-3 py-2">
        <span class="w-20 text-xs font-medium shrink-0" style="color:#6B7280;">Pembimbing</span>
        <span style="color:#374151;">{{ $siswa->kelompok?->guruPembimbing?->nama ?? '-' }}</span>
    </div>
</div>
