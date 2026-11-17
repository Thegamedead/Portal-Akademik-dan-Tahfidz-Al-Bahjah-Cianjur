@extends('guru.layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold" style="color: #1A2422; font-family: 'Plus Jakarta Sans', sans-serif;">
            {{ $menuName }}
        </h1>
        <p class="text-sm mt-1" style="color: #6B7280;">
            Portal Guru
        </p>
    </div>

    {{-- Content Placeholder --}}
    <div class="bg-white rounded-xl shadow-sm p-6" style="border: 1px solid #E5E7EB;">
        <div class="text-center py-12">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-4" style="color: #1B5E4F;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            <h3 class="text-lg font-semibold mb-2" style="color: #1A2422; font-family: 'Plus Jakarta Sans', sans-serif;">
                {{ $menuName }}
            </h3>
            <p class="text-sm mb-4" style="color: #6B7280;">
                Halaman ini dalam tahap pengembangan
            </p>
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-lg" style="background-color: #E8F5F1; color: #1B5E4F;">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm font-medium">Coming Soon</span>
            </div>
        </div>
    </div>
</div>
@endsection
