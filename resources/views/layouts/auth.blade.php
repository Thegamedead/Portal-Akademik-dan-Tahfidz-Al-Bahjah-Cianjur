<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Sistem Tahfidz')) — {{ config('app.name', 'Sistem Informasi Tahfidz') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    {{-- hCaptcha script — di-load di sini agar tersedia saat widget dirender --}}
    <script src="https://js.hcaptcha.com/1/api.js" async defer></script>
</head>
<body class="h-full antialiased" style="background-color: #F4F5F4; color: #1A2422;">

    {{-- Flash messages (timeout / session errors) --}}
    @if (session('timeout_message'))
        <div
            x-data="{ show: true }"
            x-show="show"
            x-init="setTimeout(() => show = false, 5000)"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed top-4 right-4 z-50 flex items-start gap-3 rounded-xl px-4 py-3 shadow-lg max-w-sm w-full"
            style="background-color: #FFF8E1; border: 1px solid #C9A227; color: #7A5F00;"
            role="alert"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mt-0.5 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
            </svg>
            <p class="text-sm" style="font-family: 'Inter', sans-serif;">{{ session('timeout_message') }}</p>
            <button @click="show = false" class="ml-auto shrink-0 -mr-1 -mt-0.5 p-1 rounded hover:bg-black/10 transition-colors" aria-label="Tutup">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
    @endif

    {{-- Main: vertically & horizontally centered --}}
    <main class="flex min-h-full flex-col items-center justify-center px-4 py-12 sm:px-6 lg:px-8">

        {{-- Auth card — max 400px, white, rounded, soft shadow --}}
        <div class="w-full" style="max-width: 400px;">
            <div
                class="rounded-xl bg-white px-8 py-10 shadow-md"
                style="border-radius: 12px; box-shadow: 0 2px 12px 0 rgba(26,36,34,0.10);"
            >
                @yield('content')
            </div>
        </div>

    </main>

    @livewireScripts
</body>
</html>
