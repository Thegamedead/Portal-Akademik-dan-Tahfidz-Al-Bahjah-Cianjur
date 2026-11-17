<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Informasi Tahfidz & Akademik SMP</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, .font-heading { font-family: 'Plus Jakarta Sans', sans-serif; }
        .hero-pattern {
            background-color: #F8FAFC;
            background-image: radial-gradient(#CBD5E1 1px, transparent 1px);
            background-size: 24px 24px;
        }
    </style>
</head>
<body class="antialiased bg-slate-50 text-slate-800 flex flex-col min-h-screen">
    
    <!-- Navbar -->
    <header class="bg-white/80 backdrop-blur-md border-b border-slate-200 fixed top-0 w-full z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-[#1B5E4F] flex items-center justify-center text-white font-bold text-xl">
                    T
                </div>
                <span class="font-heading font-bold text-lg text-[#1A2422] tracking-tight">Sistem Tahfidz</span>
            </div>
            <nav class="flex items-center gap-4">
                @auth('admin')
                    <a href="{{ route('admin.dashboard') }}" class="text-sm font-medium hover:text-[#1B5E4F] transition-colors">Dashboard Admin</a>
                @elseauth('guru')
                    <a href="{{ route('guru.dashboard') }}" class="text-sm font-medium hover:text-[#1B5E4F] transition-colors">Dashboard Guru</a>
                @elseauth('siswa')
                    <a href="{{ route('siswa.dashboard') }}" class="text-sm font-medium hover:text-[#1B5E4F] transition-colors">Dashboard Siswa</a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium px-5 py-2 rounded-lg bg-[#1B5E4F] text-white hover:bg-[#134338] transition-all shadow-sm hover:shadow">
                        Masuk / Login
                    </a>
                @endauth
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <main class="flex-grow hero-pattern pt-24 pb-12 flex items-center">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center gap-12">
            <div class="flex-1 text-center md:text-left space-y-6">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-100 text-[#1B5E4F] text-xs font-semibold uppercase tracking-wider">
                    <span class="w-2 h-2 rounded-full bg-[#1B5E4F] animate-pulse"></span>
                    Portal Akademik & Tahfidz Terpadu
                </div>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-[#1A2422] leading-tight">
                    Pantau Perkembangan <br class="hidden md:block">
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#1B5E4F] to-[#0284C7]">Generasi Qur'ani</span>
                </h1>
                <p class="text-lg text-slate-600 max-w-2xl mx-auto md:mx-0 leading-relaxed">
                    Sistem informasi terintegrasi untuk manajemen hafalan Al-Qur'an, pemantauan akademik, dan pelaporan progres siswa secara real-time.
                </p>
                <div class="flex flex-col sm:flex-row items-center gap-4 justify-center md:justify-start pt-4">
                    @auth
                        <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-[#1B5E4F] text-white font-medium hover:bg-[#134338] transition-all shadow-lg shadow-[#1B5E4F]/30 text-center">
                            Masuk ke Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-[#1B5E4F] text-white font-medium hover:bg-[#134338] transition-all shadow-lg shadow-[#1B5E4F]/30 text-center flex items-center justify-center gap-2">
                            Mulai Akses Portal
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                    @endauth
                </div>
            </div>
            
            <div class="flex-1 hidden md:flex justify-center relative">
                <!-- Abstract decorative element mimicking a dashboard/book -->
                <div class="relative w-full max-w-lg aspect-square">
                    <div class="absolute inset-0 bg-gradient-to-br from-[#1B5E4F]/20 to-[#0284C7]/20 rounded-full blur-3xl animate-pulse" style="animation-duration: 4s;"></div>
                    <div class="relative bg-white border border-slate-200 shadow-2xl rounded-2xl p-6 -rotate-2 transform hover:rotate-0 transition-transform duration-500">
                        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                            <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-[#1B5E4F]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <div>
                                <div class="h-4 w-32 bg-slate-200 rounded-full mb-2"></div>
                                <div class="h-3 w-24 bg-slate-100 rounded-full"></div>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="flex items-end gap-3">
                                <div class="w-full h-12 bg-emerald-100 rounded-lg rounded-b-none"></div>
                                <div class="w-full h-24 bg-emerald-200 rounded-lg rounded-b-none"></div>
                                <div class="w-full h-16 bg-emerald-100 rounded-lg rounded-b-none"></div>
                                <div class="w-full h-32 bg-[#1B5E4F] rounded-lg rounded-b-none"></div>
                                <div class="w-full h-20 bg-emerald-200 rounded-lg rounded-b-none"></div>
                            </div>
                            <div class="h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-[#1B5E4F] w-3/4 rounded-full"></div>
                            </div>
                            <div class="grid grid-cols-2 gap-4 pt-4">
                                <div class="p-4 rounded-xl border border-slate-100 bg-slate-50">
                                    <div class="w-6 h-6 rounded bg-blue-100 mb-2"></div>
                                    <div class="h-3 w-16 bg-slate-200 rounded-full"></div>
                                </div>
                                <div class="p-4 rounded-xl border border-slate-100 bg-slate-50">
                                    <div class="w-6 h-6 rounded bg-amber-100 mb-2"></div>
                                    <div class="h-3 w-16 bg-slate-200 rounded-full"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="text-sm text-slate-500">
                &copy; {{ date('Y') }} Sistem Informasi Tahfidz SMP. Hak Cipta Dilindungi.
            </div>
            <div class="flex gap-4">
                <span class="text-xs font-medium px-2.5 py-1 rounded-md bg-slate-100 text-slate-600 border border-slate-200">
                    Laravel v{{ Illuminate\Foundation\Application::VERSION }}
                </span>
                <span class="text-xs font-medium px-2.5 py-1 rounded-md bg-slate-100 text-slate-600 border border-slate-200">
                    PHP v{{ PHP_VERSION }}
                </span>
            </div>
        </div>
    </footer>
</body>
</html>
