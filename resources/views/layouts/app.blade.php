<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'PPK Room Reservation') · Kampus Pintar</title>

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
                        'link': '#0070f3',
                        'link-deep': '#3291ff',
                        'status-success': '#50e3c2',
                        'status-warning': '#f5a623',
                        'status-danger': '#ff453a',
                        'violet': '#7928ca',
                        'cyan': '#50e3c2',
                        'highlight-pink': '#ff0080'
                    },
                    fontFamily: {
                        sans: ['Geist', 'Inter', '-apple-system', 'BlinkMacSystemFont', 'system-ui', 'sans-serif'],
                        mono: ['"Geist Mono"', 'ui-monospace', 'SFMono-Regular', 'Menlo', 'Monaco', 'monospace'],
                    },
                    borderRadius: {
                        'micro': '6px',
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Geist', 'Inter', sans-serif;
            background-color: #0a0a0a;
            color: #ededed;
            -webkit-font-smoothing: antialiased;
        }
        .mesh-line {
            background: linear-gradient(90deg, #007cf0 0%, #00dfd8 25%, #7928ca 50%, #ff0080 75%, #f9cb28 100%);
        }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #0a0a0a; }
        ::-webkit-scrollbar-thumb { background: #333333; border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: #555555; }

        /* Hide number input spinners */
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { 
            -webkit-appearance: none; 
            margin: 0; 
        }
        input[type=number] {
            -moz-appearance: textfield;
            appearance: textfield;
        }
    </style>

    @stack('styles')
</head>
<body class="min-h-screen flex flex-col bg-[#0a0a0a] text-[#ededed] antialiased selection:bg-white selection:text-black">

    <!-- Top Mesh Accent Line -->
    <div class="h-[2px] w-full mesh-line sticky top-0 z-50"></div>

    <!-- Role Simulator Helper Bar (Flush Bordered Bar) -->
    <div class="bg-[#111111] border-b border-[#333333] text-xs py-2 px-4 sm:px-6 text-[#888888] z-40">
        <div class="max-w-[1200px] mx-auto flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <span class="inline-block w-2 h-2 rounded-full bg-[#0070f3]"></span>
                <span class="font-mono uppercase tracking-wider text-[11px] text-[#a1a1a1]">Role Simulator:</span>
                <span class="text-white font-medium">
                    @if(request()->is('admin*')) Administrator
                    @elseif(request()->is('staff*')) Petugas Sarpras
                    @elseif(request()->is('my*') || request()->has('as_user')) Pengguna (Mahasiswa/Dosen)
                    @else Pengunjung (Publik)
                    @endif
                </span>
            </div>
            <div class="flex items-center gap-1 overflow-x-auto">
                <span class="text-[#666666] text-[11px] hidden sm:inline mr-1">Switch:</span>
                <a href="{{ route('facilities.index') }}" class="px-2.5 py-1 rounded-[6px] text-xs transition {{ !request()->is('my*') && !request()->is('staff*') && !request()->is('admin*') && !request()->has('as_user') ? 'bg-white text-black font-medium' : 'text-[#a1a1a1] hover:text-white hover:bg-[#1f1f1f]' }}">
                    Pengunjung
                </a>
                <a href="{{ route('facilities.index', ['as_user' => 1]) }}" class="px-2.5 py-1 rounded-[6px] text-xs transition {{ (request()->is('my*') || request()->has('as_user')) ? 'bg-white text-black font-medium' : 'text-[#a1a1a1] hover:text-white hover:bg-[#1f1f1f]' }}">
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

    <!-- Navigation Header (Fixed 64px, 1px bottom border, backdrop-blur-md) -->
    <header class="sticky top-[2px] z-30 bg-[#0a0a0a]/90 backdrop-blur-md border-b border-[#333333] h-16">
        <div class="max-w-[1200px] mx-auto px-4 sm:px-6 h-full flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="{{ route('facilities.index') }}" class="flex items-center gap-2.5 group">
                <div class="w-7 h-7 rounded-[4px] bg-white text-black flex items-center justify-center font-bold text-xs">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                        <polyline points="2 17 12 22 22 17"></polyline>
                        <polyline points="2 12 12 17 22 12"></polyline>
                    </svg>
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold text-sm text-white tracking-tight leading-tight">PPK <span class="text-[#888888]">Reservation</span></span>
                    <span class="font-mono text-[9px] text-[#888888] tracking-wider uppercase">Campus Facility Network</span>
                </div>
            </a>

            <!-- Navigation Menu -->
            <nav class="hidden md:flex items-center gap-6 text-xs font-normal text-[#a1a1a1]">
                <a href="{{ route('facilities.index') }}" class="{{ request()->routeIs('facilities.index') && !request()->routeIs('user.*') ? 'text-white font-medium' : 'hover:text-white transition-colors' }}">
                    Katalog Fasilitas
                </a>
                
                @if(request()->has('as_user') || request()->is('my*'))
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
                @if(request()->has('as_user') || request()->is('my*'))
                    <div class="flex items-center gap-2.5 pl-3 border-l border-[#333333]">
                        <div class="hidden sm:flex flex-col text-right">
                            <span class="text-xs font-medium text-white">Ahmad Fauzi</span>
                            <span class="font-mono text-[10px] text-[#888888]">Mahasiswa · TI-22</span>
                        </div>
                        <div class="w-7 h-7 rounded-[4px] bg-[#1a1a1a] border border-[#333333] text-white font-mono font-medium text-xs flex items-center justify-center">
                            AF
                        </div>
                        <a href="{{ route('facilities.index') }}" title="Logout" class="p-1.5 text-[#888888] hover:text-[#ff453a] hover:bg-[#ff453a]/10 rounded-[4px] transition-colors">
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

    <!-- Main Content Container: max-w-[1200px] centered with 24px padding -->
    <main class="flex-grow w-full">
        @yield('content')
    </main>

    <!-- Full-bleed Content Block Footer with 1px top border -->
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
                <div class="font-mono text-[11px] text-[#888888]">Designed strictly to Vercel Structural Layout Rules.</div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
