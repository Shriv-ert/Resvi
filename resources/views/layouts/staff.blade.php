<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Panel Petugas') · PPK Room Reservation</title>

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
        body { font-family: 'Geist', 'Inter', sans-serif; background-color: #0a0a0a; color: #ededed; -webkit-font-smoothing: antialiased; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #0a0a0a; }
        ::-webkit-scrollbar-thumb { background: #333333; border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: #555555; }
        .mesh-line { background: linear-gradient(90deg, #007cf0 0%, #00dfd8 25%, #7928ca 50%, #ff0080 75%, #f9cb28 100%); }
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

    <!-- Decorative Top Mesh Accent Line -->
    <div class="h-[2px] w-full mesh-line sticky top-0 z-50"></div>


    <!-- Staff Header: Fixed 64px, 1px bottom border, backdrop blur -->
    <header class="sticky top-[2px] z-30 bg-[#0a0a0a]/90 backdrop-blur-md border-b border-[#333333] h-16">
        <div class="max-w-[1200px] mx-auto px-4 sm:px-6 h-full flex items-center justify-between">
            <!-- Brand & Badge -->
            <div class="flex items-center gap-3">
                <a href="{{ route('staff.reservations.index') }}" class="flex items-center">
                    <div class="flex flex-col">
                        <span class="font-semibold text-sm text-white tracking-tight leading-tight">PPK <span class="text-[#888888]">Staff Portal</span></span>
                        <span class="font-mono text-[9px] text-[#888888] tracking-wider uppercase">Operasional & Sarana</span>
                    </div>
                </a>
                <span class="hidden sm:inline-flex items-center gap-1.5 px-2 py-0.5 rounded-[4px] text-[10px] font-mono font-medium bg-[#002b11] text-[#50e3c2] border border-[#005522]">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#50e3c2]"></span>
                    Shift Aktif
                </span>
            </div>

            <!-- Staff User Info -->
            <div class="flex items-center gap-3">
                <div class="hidden sm:flex flex-col text-right">
                    <span class="text-xs font-medium text-white">{{ auth()->user()->name ?? 'Petugas Sarpras' }}</span>
                    <span class="font-mono text-[10px] text-[#888888]">{{ auth()->user()->role ? ucfirst(auth()->user()->role->value) : 'Petugas' }} Sarpras Kampus</span>
                </div>
                <div class="w-7 h-7 rounded-[4px] bg-[#1a1a1a] border border-[#333333] text-white font-mono font-medium text-xs flex items-center justify-center">
                    {{ strtoupper(substr(auth()->user()->name ?? 'PT', 0, 2)) }}
                </div>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" title="Keluar dari Sistem" class="p-1.5 text-[#888888] hover:text-[#ff453a] hover:bg-[#ff453a]/10 rounded-[4px] transition-colors cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Operational Tab Navigation Bar (Flush Bordered Bar) -->
    <div class="bg-[#111111] border-b border-[#333333] py-2 px-4 sm:px-6">
        <div class="max-w-[1200px] mx-auto flex items-center gap-1 overflow-x-auto text-xs">
            <a href="{{ route('staff.reservations.index') }}" 
               class="px-3 py-1.5 rounded-[6px] font-medium transition flex items-center gap-1.5 {{ request()->routeIs('staff.reservations.index') ? 'bg-white text-black' : 'text-[#a1a1a1] hover:text-white hover:bg-[#1f1f1f]' }}">
                Antrian Reservasi
            </a>

            <a href="{{ route('staff.reservations.history') }}" 
               class="px-3 py-1.5 rounded-[6px] font-medium transition {{ request()->routeIs('staff.reservations.history') ? 'bg-white text-black' : 'text-[#a1a1a1] hover:text-white hover:bg-[#1f1f1f]' }}">
                Riwayat Reservasi
            </a>

            <span class="text-[#333333] px-1">|</span>

            <a href="{{ route('staff.reports.index') }}" 
               class="px-3 py-1.5 rounded-[6px] font-medium transition flex items-center gap-1.5 {{ request()->routeIs('staff.reports.index') ? 'bg-white text-black' : 'text-[#a1a1a1] hover:text-white hover:bg-[#1f1f1f]' }}">
                Laporan Kerusakan
            </a>

            <a href="{{ route('staff.reports.history') }}" 
               class="px-3 py-1.5 rounded-[6px] font-medium transition {{ request()->routeIs('staff.reports.history') ? 'bg-white text-black' : 'text-[#a1a1a1] hover:text-white hover:bg-[#1f1f1f]' }}">
                Riwayat Laporan
            </a>

            <span class="text-[#333333] px-1">|</span>

            <a href="{{ route('staff.facilities.index') }}" 
               class="px-3 py-1.5 rounded-[6px] font-medium transition {{ request()->routeIs('staff.facilities.index') ? 'bg-white text-black' : 'text-[#a1a1a1] hover:text-white hover:bg-[#1f1f1f]' }}">
                Status Fasilitas
            </a>

            <a href="{{ route('staff.facilities.maintenance') }}" 
               class="px-3 py-1.5 rounded-[6px] font-medium transition flex items-center gap-1.5 {{ request()->routeIs('staff.facilities.maintenance') ? 'bg-white text-black' : 'text-[#a1a1a1] hover:text-white hover:bg-[#1f1f1f]' }}">
                Dalam Perbaikan
                <span class="w-1.5 h-1.5 rounded-full bg-[#ff453a]"></span>
            </a>
        </div>
    </div>

    <!-- Main Content Container: max-w-[1200px] centered -->
    <main class="flex-grow w-full">
        @if(session('success'))
            <div class="max-w-[1200px] mx-auto px-4 sm:px-6 pt-6">
                <div class="p-3.5 rounded-[6px] bg-[#002b11] border border-[#005522] text-xs text-[#50e3c2] flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error') || $errors->any())
            <div class="max-w-[1200px] mx-auto px-4 sm:px-6 pt-6">
                <div class="p-3.5 rounded-[6px] bg-[#2b0000] border border-[#550000] text-xs text-[#ff453a] space-y-1">
                    <div class="flex items-center gap-2 font-medium">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Terjadi Kesalahan:</span>
                    </div>
                    <p class="pl-6 text-[#ededed]/90 text-[11px]">
                        {{ session('error') ?: ($errors->first('approve') ?: $errors->first('reject') ?: $errors->first('cancel') ?: $errors->first('operational_status') ?: $errors->first('reason') ?: $errors->first()) }}
                    </p>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
