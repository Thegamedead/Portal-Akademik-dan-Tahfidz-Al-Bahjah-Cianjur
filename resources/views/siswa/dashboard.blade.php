@extends('siswa.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="max-w-4xl">

    {{-- Greeting card --}}
    <div
        class="rounded-2xl bg-white p-6 shadow-sm mb-6"
        style="border: 1px solid #E5E7EB;"
    >
        <div class="flex items-start gap-4">
            <div
                class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl text-white text-lg font-bold"
                style="background-color: #1B5E4F; font-family: 'Plus Jakarta Sans', sans-serif;"
                aria-hidden="true"
            >{{ strtoupper(substr(auth('siswa')->user()?->nama ?? 'S', 0, 1)) }}</div>
            <div>
                <h2
                    class="text-xl font-semibold"
                    style="font-family: 'Plus Jakarta Sans', sans-serif; color: #1A2422;"
                >Selamat datang, {{ auth('siswa')->user()?->nama ?? 'Siswa' }}!</h2>
                <p class="mt-1 text-sm" style="color: #6B7280;">
                    Anda login sebagai
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                          style="background-color: #E8F5F1; color: #1B5E4F;">
                        Siswa
                    </span>
                    &mdash; NIS: {{ auth('siswa')->user()?->nis ?? '-' }}
                </p>
            </div>
        </div>
    </div>

    {{-- Placeholder info --}}
    <div class="rounded-2xl bg-white p-6 shadow-sm" style="border: 1px solid #E5E7EB; border-left: 4px solid #1B5E4F;">
        <p class="text-sm" style="color: #6B7280; font-family: 'Inter', sans-serif;">
            Halaman dashboard Siswa sedang dalam pengembangan. Progres hafalan juz, riwayat absensi, dan informasi kelompok akan ditampilkan di sini.
        </p>
    </div>

</div>
@endsection
