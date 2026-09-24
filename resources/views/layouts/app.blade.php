<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Katalog Fasilitas') · {{ config('app.name', 'PPK Room Reservation') }}</title>

    <!-- Google Fonts: Geist & Geist Mono & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@300;400;500;600&family=Geist+Mono:wght@400;500;600&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Tailwind CSS with Vercel Dark Mode Tokens -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'primary': '#ffffff',
                        'ink': '#ededed',
                        'on-primary': '#000000',
                        'canvas': '#0a0a0a',
                        'canvas-soft': '#111111',
                        'canvas-soft-2': '#161616',
                        'body': '#a1a1a1',
                        'mute': '#666666',
                        'hairline': '#333333',
                        'hairline-strong': '#444444',
                        'accent-blue': '#0070f3',
                        'accent-green': '#50e3c2',
                        'accent-red': '#ff453a',
                        'accent-amber': '#f5a623',
                    },
                    fontFamily: {
                        sans: ['Geist', 'Inter', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'sans-serif'],
                        mono: ['Geist Mono', 'ui-monospace', 'SFMono-Regular', 'Menlo', 'Monaco', 'monospace'],
                    },
                    borderRadius: {
                        'micro': '6px',
                        'soft': '8px',
                        'pill': '9999px',
                    },
                }
            }
        }
    </script>

    <style>
        body {
            background-color: #0a0a0a;
            color: #ededed;
            font-family: 'Geist', -apple-system, BlinkMacSystemFont, sans-serif;
            -webkit-font-smoothing: antialiased;
        }
        .mesh-line {
            background: linear-gradient(90deg, #007cf0 0%, #00dfd8 50%, #7928ca 100%);
        }
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { 
            -webkit-appearance: none; 
            margin: 0; 
        }
    </style>
</head>
<body class="min-h-screen bg-[#0a0a0a] text-[#ededed] flex flex-col font-sans selection:bg-[#333333] selection:text-white">

    <!-- Decorative Top Mesh Accent Line -->
    <div class="h-[2px] w-full mesh-line sticky top-0 z-50"></div>

    <!-- Simulator Role Switcher Bar (Flush Bordered Bar) -->
    <div class="bg-[#111111] border-b border-[#333333] text-xs py-2 px-4 sm:px-6 text-[#888888] z-40">
        <div class="max-w-[1200px] mx-auto flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <span class="inline-block w-2 h-2 rounded-full {{ auth()->check() || request()->has('as_user') ? 'bg-[#50e3c2]' : 'bg-[#a1a1a1]' }}"></span>
                <span class="font-mono uppercase tracking-wider text-[11px] text-[#a1a1a1]">Peran:</span>
                <span class="text-white font-medium">
                    @auth
                        {{ auth()->user()->role?->label() ?? 'Civitas' }} ({{ auth()->user()->name }})
                    @else
                        {{ request()->has('as_user') ? 'Pengguna (Civitas Terdaftar)' : 'Pengunjung (Publik)' }}
                    @endauth
                </span>
            </div>
            <div class="flex items-center gap-1 overflow-x-auto">
                <span class="text-[#666666] text-[11px] hidden sm:inline mr-1">Switch:</span>
                <a href="{{ route('facilities.index') }}" class="px-2.5 py-1 rounded-[6px] text-xs transition {{ (!request()->is('my*') && !request()->has('as_user') && !request()->is('staff*') && !request()->is('admin*') && !auth()->check()) ? 'bg-white text-black font-medium' : 'text-[#a1a1a1] hover:text-white hover:bg-[#1f1f1f]' }}">
                    Pengunjung
                </a>
                <a href="{{ route('facilities.index', ['as_user' => 1]) }}" class="px-2.5 py-1 rounded-[6px] text-xs transition {{ (request()->is('my*') || request()->has('as_user') || (auth()->check() && auth()->user()->role?->value === 'pengguna')) ? 'bg-white text-black font-medium' : 'text-[#a1a1a1] hover:text-white hover:bg-[#1f1f1f]' }}">
                    Pengguna
                </a>
                <a href="{{ route('staff.reservations.index') }}" class="px-2.5 py-1 rounded-[6px] text-xs transition {{ request()->is('staff*') ? 'bg-white text-black font-medium' : 'text-[#a1a1a1] hover:text-white hover:bg-[#1f1f1f]' }}">
                    Petugas
                </a>
                <a href="{{ route('admin.facilities.index') }}" class="px-2.5 py-1 rounded-[6px] text-xs transition {{ request()->is('admin*') ? 'bg-white text-black font-medium' : 'text-[#a1a1a1] hover:text-white hover:bg-[#1f1f1f]' }}">
                    Admin
                </a>
            </div>
        </div>
    </div>

    <!-- Navigation Header -->
    <header class="sticky top-[2px] z-30 bg-[#0a0a0a]/90 backdrop-blur-md border-b border-[#333333] h-16">
        <div class="max-w-[1200px] mx-auto px-4 sm:px-6 h-full flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="{{ route('facilities.index') }}" class="flex items-center gap-2.5 group">
                <div class="w-7 h-7 rounded-[4px] bg-white text-black flex items-center justify-center font-bold text-xs">
                    ▲
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold text-sm text-white tracking-tight leading-tight">PPK <span class="text-[#888888]">Reservation</span></span>
                    <span class="font-mono text-[9px] text-[#888888] tracking-wider uppercase">Sistem Fasilitas Kampus</span>
                </div>
            </a>

            <!-- Navigation Menu -->
            <nav class="hidden md:flex items-center gap-6 text-xs font-normal text-[#a1a1a1]">
                <a href="{{ route('facilities.index') }}" class="{{ request()->routeIs('facilities.index') && !request()->routeIs('user.*') ? 'text-white font-medium' : 'hover:text-white transition-colors' }}">
                    Katalog Fasilitas
                </a>
                
                @if(auth()->check() || request()->has('as_user') || request()->is('my*'))
                    <a href="{{ route('user.reservations.index') }}" class="{{ request()->routeIs('user.reservations.*') ? 'text-white font-medium' : 'hover:text-white transition-colors' }}">
                        Riwayat Reservasi
                    </a>
                    <a href="{{ route('user.reports.index') }}" class="{{ request()->routeIs('user.reports.index') || request()->routeIs('user.reports.show') ? 'text-white font-medium' : 'hover:text-white transition-colors' }}">
                        Riwayat Laporan
                    </a>
                    <a href="{{ route('user.reports.create') }}" class="{{ request()->routeIs('user.reports.create') ? 'text-white font-medium' : 'hover:text-white transition-colors' }}">
                        Buat Laporan
                    </a>
                @endif
            </nav>

            <!-- User Action -->
            <div class="flex items-center gap-3">
                @auth
                    <div class="flex items-center gap-2.5 pl-3 border-l border-[#333333]">
                        <div class="hidden sm:flex flex-col text-right">
                            <span class="text-xs font-medium text-white">{{ auth()->user()->name }}</span>
                            <span class="font-mono text-[10px] text-[#888888]">{{ auth()->user()->role?->label() }}</span>
                        </div>
                        <div class="w-7 h-7 rounded-[4px] bg-[#1a1a1a] border border-[#333333] text-white font-mono font-medium text-xs flex items-center justify-center">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" title="Keluar / Logout" class="p-1.5 text-[#888888] hover:text-[#ff453a] hover:bg-[#ff453a]/10 rounded-[4px] transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                @elseif(request()->has('as_user') || request()->is('my*'))
                    <div class="flex items-center gap-2.5 pl-3 border-l border-[#333333]">
                        <div class="hidden sm:flex flex-col text-right">
                            <span class="text-xs font-medium text-white">Ahmad Fauzi</span>
                            <span class="font-mono text-[10px] text-[#888888]">Mahasiswa · TI-22</span>
                        </div>
                        <div class="w-7 h-7 rounded-[4px] bg-[#1a1a1a] border border-[#333333] text-white font-mono font-medium text-xs flex items-center justify-center">
                            AF
                        </div>
                        <a href="{{ route('facilities.index') }}" title="Keluar Simulator" class="p-1.5 text-[#888888] hover:text-[#ff453a] hover:bg-[#ff453a]/10 rounded-[4px] transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                        </a>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="bg-white hover:bg-[#e6e6e6] text-black font-medium text-xs px-4 py-2 rounded-full transition-all active:scale-95 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                        </svg>
                        Masuk
                    </a>
                @endif
            </div>
        </div>
    </header>

    <!-- Flash Status Alerts -->
    @if (session('status') || session('success'))
        <div class="max-w-[1200px] mx-auto px-4 sm:px-6 pt-6 w-full">
            <div class="flex items-start gap-3 rounded-[6px] border border-[#005522] bg-[#002b11] p-4 text-[#50e3c2] text-xs">
                <span class="font-bold text-sm">✓</span>
                <div class="leading-5">{{ session('status') ?? session('success') }}</div>
            </div>
        </div>
    @endif

    @if (session('warning'))
        <div class="max-w-[1200px] mx-auto px-4 sm:px-6 pt-6 w-full">
            <div class="flex items-start gap-3 rounded-[6px] border border-[#553c00] bg-[#2b1e00] p-4 text-[#f5a623] text-xs">
                <span class="font-bold text-sm">⚠</span>
                <div class="leading-5">{{ session('warning') }}</div>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="max-w-[1200px] mx-auto px-4 sm:px-6 pt-6 w-full">
            <div class="flex items-start gap-3 rounded-[6px] border border-[#550000] bg-[#2b0000] p-4 text-[#ff453a] text-xs">
                <span class="font-bold text-sm">✕</span>
                <div class="leading-5">{{ session('error') }}</div>
            </div>
        </div>
    @endif

    <!-- Main Content Container -->
    <main class="flex-grow w-full">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="border-t border-[#333333] bg-[#0a0a0a] mt-24">
        <div class="max-w-[1200px] mx-auto px-4 sm:px-6 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="space-y-2 md:col-span-2">
                    <div class="flex items-center gap-2">
                        <div class="w-5 h-5 rounded-[4px] bg-white text-black flex items-center justify-center font-bold text-[10px]">▲</div>
                        <span class="font-semibold text-xs text-white">PPK Room Reservation System</span>
                    </div>
                    <p class="text-xs text-[#888888] leading-relaxed max-w-md">
                        Sistem manajemen dan peminjaman fasilitas kampus terintegrasi. Menghubungkan seluruh civitas akademika dengan transparansi jadwal slot 30 menit.
                    </p>
                </div>
                <div>
                    <h4 class="font-mono text-[10px] uppercase tracking-wider text-[#888888] mb-3">Aturan Operasional</h4>
                    <ul class="text-xs text-[#a1a1a1] space-y-1.5 font-mono">
                        <li>Jam: 07.00 – 20.00 WIB</li>
                        <li>Slot: 30 Menit / interval</li>
                        <li>Verifikasi: Petugas Sarpras</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-mono text-[10px] uppercase tracking-wider text-[#888888] mb-3">Pusat Bantuan</h4>
                    <ul class="text-xs text-[#a1a1a1] space-y-1.5">
                        <li>Gedung Rektorat Lt. 1</li>
                        <li>sarpras@kampus.ac.id</li>
                        <li>Internal ext. 104</li>
                    </ul>
                </div>
            </div>

            <div class="mt-12 pt-6 border-t border-[#333333] flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-[#888888]">
                <div>© 2026 PPK Sarpras Kampus. Hak cipta dilindungi.</div>
                <div class="font-mono text-[11px] text-[#888888]">PPK Room Reservation System</div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
