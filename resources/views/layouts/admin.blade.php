<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Master Control') · {{ config('app.name', 'Resvi') }}</title>

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
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #333333; border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: #555555; }
        * {
            scrollbar-width: thin;
            scrollbar-color: #333333 transparent;
        }
        .mesh-line { background: linear-gradient(90deg, #007cf0 0%, #00dfd8 25%, #7928ca 50%, #ff0080 75%, #f9cb28 100%); }
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


    <!-- Admin Header: Fixed 64px, 1px bottom border, backdrop blur -->
    <header class="sticky top-[2px] z-30 bg-[#0a0a0a]/90 backdrop-blur-md border-b border-[#333333] h-16">
        <div class="max-w-[1200px] mx-auto px-4 sm:px-6 h-full flex items-center justify-between">
            <!-- Brand & Badge -->
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.facilities.index') }}" class="flex items-center">
                    <div class="flex flex-col">
                        <span class="font-semibold text-sm text-white tracking-tight leading-tight">Resvi <span class="text-[#888888]">Master Admin</span></span>
                        <span class="font-mono text-[9px] text-[#888888] tracking-wider uppercase">Pusat Kendali Data Master</span>
                    </div>
                </a>
                <span class="hidden sm:inline-flex items-center gap-1.5 px-2 py-0.5 rounded-[4px] text-[10px] font-mono font-medium bg-[#001733] text-[#0070f3] border border-[#003b80]">
                    Super Admin
                </span>
            </div>

            <!-- Admin Profile Info -->
            <div class="flex items-center gap-3">
                <div class="hidden sm:flex flex-col text-right">
                    <span class="text-xs font-medium text-white">{{ auth()->check() ? auth()->user()->name : 'Admin Pusat' }}</span>
                    <span class="font-mono text-[10px] text-[#888888]">{{ auth()->check() ? auth()->user()->email : 'administrator@kampus.ac.id' }}</span>
                </div>
                <div class="w-7 h-7 rounded-[4px] bg-[#1a1a1a] border border-[#333333] text-white font-mono font-medium text-xs flex items-center justify-center">
                    {{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 2)) : 'AD' }}
                </div>
                @auth
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" title="Keluar / Logout" class="p-1.5 text-[#888888] hover:text-[#ff453a] hover:bg-[#ff453a]/10 rounded-[4px] transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                        </button>
                    </form>
                @else
                    <a href="{{ route('facilities.index') }}" title="Keluar ke Portal Publik" class="p-1.5 text-[#888888] hover:text-[#ff453a] hover:bg-[#ff453a]/10 rounded-[4px] transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Admin Master Control Tab Bar (Flush Bordered Bar) -->
    <div class="bg-[#111111] border-b border-[#333333] py-2 px-4 sm:px-6">
        <div class="max-w-[1200px] mx-auto flex items-center gap-1 overflow-x-auto text-xs">
            <a href="{{ route('admin.facilities.index') }}" 
               class="px-3 py-1.5 rounded-[6px] font-medium transition {{ request()->routeIs('admin.facilities.*') ? 'bg-white text-black' : 'text-[#a1a1a1] hover:text-white hover:bg-[#1f1f1f]' }}">
                Kelola Fasilitas Master
            </a>

            <a href="{{ route('admin.accounts.index') }}" 
               class="px-3 py-1.5 rounded-[6px] font-medium transition {{ request()->routeIs('admin.accounts.*') ? 'bg-white text-black' : 'text-[#a1a1a1] hover:text-white hover:bg-[#1f1f1f]' }}">
                Kelola Akun Civitas
            </a>

            <a href="{{ route('admin.recaps.index') }}" 
               class="px-3 py-1.5 rounded-[6px] font-medium transition {{ request()->routeIs('admin.recaps.*') ? 'bg-white text-black' : 'text-[#a1a1a1] hover:text-white hover:bg-[#1f1f1f]' }}">
                Rekapitulasi Okupansi & Kerusakan
            </a>
        </div>
    </div>

    <!-- Flash Status Alerts -->
    @if (session('status') || session('success'))
        <div class="max-w-[1200px] mx-auto px-4 sm:px-6 pt-6 w-full">
            <div class="flex items-start gap-3 rounded-[6px] border border-[#005522] bg-[#002b11] p-4 text-[#50e3c2] text-xs font-mono">
                <span class="font-bold text-sm">✓</span>
                <div class="leading-5">{{ session('status') ?? session('success') }}</div>
            </div>
        </div>
    @endif

    @if (session('warning'))
        <div class="max-w-[1200px] mx-auto px-4 sm:px-6 pt-6 w-full">
            <div class="flex items-start gap-3 rounded-[6px] border border-[#553c00] bg-[#2b1e00] p-4 text-[#f5a623] text-xs font-mono">
                <span class="font-bold text-sm">⚠</span>
                <div class="leading-5">{{ session('warning') }}</div>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="max-w-[1200px] mx-auto px-4 sm:px-6 pt-6 w-full">
            <div class="flex items-start gap-3 rounded-[6px] border border-[#550000] bg-[#2b0000] p-4 text-[#ff453a] text-xs font-mono">
                <span class="font-bold text-sm">✕</span>
                <div class="leading-5">{{ session('error') }}</div>
            </div>
        </div>
    @endif

    <!-- Main Content Container: max-w-[1200px] centered -->
    <main class="flex-grow w-full">
        @yield('content')
    </main>

    <script>
        function toggleCustomDropdown(id) {
            const menu = document.getElementById(id + 'Menu');
            const chevron = document.getElementById(id + 'Chevron');
            const isOpen = menu && !menu.classList.contains('hidden');

            document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.add('hidden'));
            document.querySelectorAll('.custom-dropdown-chevron').forEach(c => c.classList.remove('rotate-180'));

            if (!isOpen && menu) {
                menu.classList.remove('hidden');
                if (chevron) chevron.classList.add('rotate-180');

                const searchInput = menu.querySelector('input[type="text"]');
                if (searchInput) {
                    searchInput.value = '';
                    filterDropdownOptions(searchInput, id + 'Menu');
                    setTimeout(() => searchInput.focus(), 50);
                }
            }
        }

        function filterDropdownOptions(inputEl, menuId) {
            const q = inputEl.value.toLowerCase().trim();
            const menu = document.getElementById(menuId);
            if (!menu) return;
            menu.querySelectorAll('.dropdown-item').forEach(item => {
                const text = item.innerText.toLowerCase();
                item.style.display = text.includes(q) ? 'flex' : 'none';
            });
        }

        function selectCustomDropdown(id, val, label, onSelectCallback) {
            const input = document.getElementById(id);
            const labelEl = document.getElementById(id + 'Label');
            const menu = document.getElementById(id + 'Menu');
            const chevron = document.getElementById(id + 'Chevron');

            if (input) input.value = val;
            if (labelEl) labelEl.innerText = label;

            if (menu) {
                menu.querySelectorAll('.dropdown-item').forEach(item => {
                    if (item.dataset.value === val) {
                        item.classList.add('bg-[#1f1f1f]', 'text-white');
                        const check = item.querySelector('.check-icon');
                        if (check) check.classList.remove('hidden');
                    } else {
                        item.classList.remove('bg-[#1f1f1f]', 'text-white');
                        const check = item.querySelector('.check-icon');
                        if (check) check.classList.add('hidden');
                    }
                });
                menu.classList.add('hidden');
            }
            if (chevron) chevron.classList.remove('rotate-180');

            if (typeof onSelectCallback === 'function') {
                onSelectCallback(val, label);
            } else if (typeof window[onSelectCallback] === 'function') {
                window[onSelectCallback](val, label);
            }
        }

        document.addEventListener('click', (e) => {
            if (!e.target.closest('.custom-dropdown')) {
                document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.add('hidden'));
                document.querySelectorAll('.custom-dropdown-chevron').forEach(c => c.classList.remove('rotate-180'));
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
