<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'Admin Dashboard') · {{ config('app.name', 'PPK Room Reservation') }}</title>

        <!-- Tailwind CSS Play CDN (Bebas node_modules) -->
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="flex min-h-full flex-col text-slate-800 antialiased">
        <!-- Top Navigation -->
        <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 backdrop-blur">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8 py-3.5">
                <div class="flex items-center gap-8">
                    <a href="{{ route('home') }}" class="flex items-center gap-2.5 text-slate-900 font-bold text-lg tracking-tight">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-sky-700 text-white font-extrabold text-sm shadow-sm">
                            PPK
                        </span>
                        <span>Room Reservation</span>
                    </a>

                    <nav class="hidden md:flex items-center gap-1.5 text-sm font-semibold">
                        <a
                            href="{{ Route::has('admin.facilities.index') ? route('admin.facilities.index') : '#' }}"
                            class="rounded-lg px-3 py-2 transition {{ request()->routeIs('admin.facilities.*') ? 'bg-sky-50 text-sky-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}"
                        >
                            Kelola Fasilitas
                        </a>
                        <a
                            href="{{ route('admin.accounts.index') }}"
                            class="rounded-lg px-3 py-2 transition {{ request()->routeIs('admin.accounts.*') ? 'bg-sky-50 text-sky-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}"
                        >
                            Kelola Akun
                        </a>
                        <a
                            href="{{ Route::has('admin.recaps.index') ? route('admin.recaps.index') : '#' }}"
                            class="rounded-lg px-3 py-2 transition {{ request()->routeIs('admin.recaps.*') ? 'bg-sky-50 text-sky-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}"
                        >
                            Rekapitulasi
                        </a>
                    </nav>
                </div>

                <div class="flex items-center gap-3">
                    <div class="hidden sm:flex flex-col text-right">
                        <span class="text-xs font-semibold text-slate-900">{{ auth()->user()->name ?? 'Administrator' }}</span>
                        <span class="text-[11px] font-medium text-sky-700 uppercase tracking-wider">Admin</span>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button
                            type="submit"
                            class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500/20"
                        >
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Main Content Container -->
        <main class="mx-auto w-full max-w-7xl flex-1 px-4 sm:px-6 lg:px-8 py-8">
            <!-- Flash Status Alerts -->
            @if (session('status'))
                <div class="mb-6 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-900 shadow-sm">
                    <span class="text-emerald-700 font-bold">✓</span>
                    <div class="text-sm font-medium leading-5">
                        {{ session('status') }}
                    </div>
                </div>
            @endif

            @if (session('warning'))
                <div class="mb-6 flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4 text-amber-900 shadow-sm">
                    <span class="text-amber-700 font-bold">⚠</span>
                    <div class="text-sm font-medium leading-5">
                        {{ session('warning') }}
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 p-4 text-red-900 shadow-sm">
                    <span class="text-red-700 font-bold">✕</span>
                    <div class="text-sm font-medium leading-5">
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="border-t border-slate-200 bg-white py-4 text-center text-xs text-slate-500">
            &copy; {{ date('Y') }} PPK Room Reservation — Administrator Governance
        </footer>

        @stack('scripts')
    </body>
</html>
