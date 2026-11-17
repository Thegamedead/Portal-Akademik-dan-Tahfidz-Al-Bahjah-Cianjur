<div>

    {{-- ── Info Akun ──────────────────────────────────────────────────────── --}}
    <div class="mb-6 rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;">
        <div class="flex items-center gap-4">
            <div
                class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full text-white text-xl font-bold"
                style="background-color:#1B5E4F;font-family:'Plus Jakarta Sans',sans-serif;"
                aria-hidden="true"
            >
                {{ strtoupper(substr($guru->nama ?? 'G', 0, 1)) }}
            </div>
            <div>
                <p class="text-base font-semibold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
                    {{ $guru->nama ?? '—' }}
                </p>
                <p class="text-sm" style="color:#6B7280;font-family:'Inter',sans-serif;">
                    {{ $guru->jabatan ?? 'Guru' }} &mdash; NIY: {{ $guru->niy ?? '—' }}
                </p>
            </div>
        </div>
    </div>

    {{-- ── Data Pribadi Guru ───────────────────────────────────────────────── --}}
    <div class="mb-6 rounded-2xl bg-white shadow-sm" style="border:1px solid #E5E7EB;">

        {{-- Header --}}
        <div class="flex items-center gap-4 px-6 py-5 rounded-t-2xl flex-shrink-0" style="background-color:#1B5E4F;">
            <div class="flex h-9 w-9 items-center justify-center rounded-lg shrink-0" style="background-color:#E8F5F1;">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="#1B5E4F" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#FFFFFF;">Data Pribadi</p>
                <p class="text-xs" style="color:#D1D5DB;font-family:'Inter',sans-serif;">Informasi pribadi guru (read-only, dikelola oleh Admin)</p>
            </div>
        </div>

        {{-- Content --}}
        <div class="px-6 py-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-5">

                {{-- NIY --}}
                <div>
                    <p class="text-xs font-medium mb-1" style="color:#6B7280;font-family:'Inter',sans-serif;">NIY</p>
                    <p class="text-sm font-medium" style="color:#1A2422;font-family:'Inter',sans-serif;">{{ $guru->niy ?? '—' }}</p>
                </div>

                {{-- Nama Lengkap --}}
                <div>
                    <p class="text-xs font-medium mb-1" style="color:#6B7280;font-family:'Inter',sans-serif;">Nama Lengkap</p>
                    <p class="text-sm font-medium" style="color:#1A2422;font-family:'Inter',sans-serif;">{{ $guru->nama ?? '—' }}</p>
                </div>

                {{-- Jenis Kelamin --}}
                <div>
                    <p class="text-xs font-medium mb-1" style="color:#6B7280;font-family:'Inter',sans-serif;">Jenis Kelamin</p>
                    <p class="text-sm font-medium" style="color:#1A2422;font-family:'Inter',sans-serif;">
                        @if($guru->jenis_kelamin === 'Laki-laki') Laki-laki
                        @elseif($guru->jenis_kelamin === 'Perempuan') Perempuan
                        @else —
                        @endif
                    </p>
                </div>

                {{-- Jabatan --}}
                <div>
                    <p class="text-xs font-medium mb-1" style="color:#6B7280;font-family:'Inter',sans-serif;">Jabatan</p>
                    <p class="text-sm font-medium" style="color:#1A2422;font-family:'Inter',sans-serif;">{{ $guru->jabatan ?? '—' }}</p>
                </div>

                {{-- Tempat Lahir --}}
                <div>
                    <p class="text-xs font-medium mb-1" style="color:#6B7280;font-family:'Inter',sans-serif;">Tempat Lahir</p>
                    <p class="text-sm font-medium" style="color:#1A2422;font-family:'Inter',sans-serif;">{{ $guru->tempat_lahir ?? '—' }}</p>
                </div>

                {{-- Tanggal Lahir --}}
                <div>
                    <p class="text-xs font-medium mb-1" style="color:#6B7280;font-family:'Inter',sans-serif;">Tanggal Lahir</p>
                    <p class="text-sm font-medium" style="color:#1A2422;font-family:'Inter',sans-serif;">
                        {{ $guru->tanggal_lahir ? \Carbon\Carbon::parse($guru->tanggal_lahir)->translatedFormat('d F Y') : '—' }}
                    </p>
                </div>

                {{-- No. Telepon --}}
                <div>
                    <p class="text-xs font-medium mb-1" style="color:#6B7280;font-family:'Inter',sans-serif;">No. Telepon</p>
                    <p class="text-sm font-medium" style="color:#1A2422;font-family:'Inter',sans-serif;">{{ $guru->no_telepon ?? '—' }}</p>
                </div>

                {{-- Email --}}
                <div>
                    <p class="text-xs font-medium mb-1" style="color:#6B7280;font-family:'Inter',sans-serif;">Email</p>
                    <p class="text-sm font-medium" style="color:#1A2422;font-family:'Inter',sans-serif;">{{ $guru->email ?? '—' }}</p>
                </div>

                {{-- Tanggal Mulai Kerja --}}
                <div>
                    <p class="text-xs font-medium mb-1" style="color:#6B7280;font-family:'Inter',sans-serif;">Tanggal Mulai Kerja</p>
                    <p class="text-sm font-medium" style="color:#1A2422;font-family:'Inter',sans-serif;">
                        {{ $guru->tanggal_mulai_kerja ? \Carbon\Carbon::parse($guru->tanggal_mulai_kerja)->translatedFormat('d F Y') : '—' }}
                    </p>
                </div>

            </div>
        </div>
    </div>

    {{-- ── Penempatan Kelompok ──────────────────────────────────────────────── --}}
    <div class="rounded-2xl bg-white shadow-sm" style="border:1px solid #E5E7EB;">

        {{-- Header --}}
        <div class="flex items-center gap-4 px-6 py-5 rounded-t-2xl flex-shrink-0" style="background-color:#1B5E4F;">
            <div class="flex h-9 w-9 items-center justify-center rounded-lg shrink-0" style="background-color:#E8F5F1;">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="#1B5E4F" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#FFFFFF;">Penempatan Kelompok</p>
                <p class="text-xs" style="color:#D1D5DB;font-family:'Inter',sans-serif;">Kelompok tahfidz yang Anda bimbing saat ini</p>
            </div>
        </div>

        {{-- Content --}}
        <div class="px-6 py-5">
            @if($guru->kelompoks && $guru->kelompoks->count() > 0)
                <div class="space-y-3">
                    @foreach($guru->kelompoks as $kelompok)
                        <div class="rounded-xl p-4" style="background-color:#F9FAFB;border:1px solid #E5E7EB;">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1 min-w-0">
                                    {{-- Nama Kelompok --}}
                                    <h4 class="text-sm font-bold mb-2" style="color:#1B5E4F;font-family:'Plus Jakarta Sans',sans-serif;">
                                        {{ $kelompok->nama }}
                                    </h4>

                                    {{-- Detail baris 1: Tingkat & Jenis --}}
                                    <div class="flex flex-wrap items-center gap-2 mb-2">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-semibold" style="background-color:#E8F5F1;color:#1B5E4F;">
                                            Tingkat {{ $kelompok->tingkat }}
                                        </span>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-semibold" style="background-color:#F3F4F6;color:#374151;">
                                            {{ $kelompok->jenis }}
                                        </span>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-semibold" style="background-color:#EFF6FF;color:#1D4ED8;">
                                            TA {{ $kelompok->tahunAjaran->label ?? '—' }}
                                        </span>
                                    </div>


                                    {{-- Tanggal penempatan --}}
                                    <p class="text-xs mt-1" style="color:#9CA3AF;font-family:'Inter',sans-serif;">
                                        Ditetapkan sejak {{ $kelompok->tanggal_penempatan_guru ? \Carbon\Carbon::parse($kelompok->tanggal_penempatan_guru)->translatedFormat('d F Y') : '—' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <div class="flex flex-col items-center justify-center gap-3">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full" style="background-color:#FEF3C7;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="#C9A227" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="text-sm font-medium" style="color:#6B7280;font-family:'Inter',sans-serif;">Belum ada kelompok yang dibimbing</p>
                        <p class="text-xs" style="color:#9CA3AF;font-family:'Inter',sans-serif;">Penugasan kelompok akan dilakukan oleh Administrator</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

</div>
