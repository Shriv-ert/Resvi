<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'Katalog Fasilitas') · {{ config('app.name', 'PPK Room Reservation') }}</title>

        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            brand: {
                                50: '#f0f9ff',
                                100: '#e0f2fe',
                                500: '#0ea5e9',
                                600: '#0284c7',
                                700: '#0369a1',
                                800: '#075985',
                            }
                        }
                    }
                }
            }
        </script>
    </head>
    <body class="min-h-screen bg-slate-50 text-slate-900 flex flex-col font-sans antialiased">
        <!-- Navigation Bar -->
        <header class="sticky top-0 z-40 w-full border-b border-slate-200 bg-white/95 backdrop-blur">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-700 text-white font-bold text-lg shadow-sm group-hover:bg-sky-800 transition">
                            P
                        </div>
                        <div>
                            <span class="text-base font-bold tracking-tight text-slate-900 block leading-tight">PPK Room Reservation</span>
                            <span class="text-xs text-slate-500 font-medium">Sistem Fasilitas Kampus</span>
                        </div>
                    </a>
                </div>

                <nav class="flex items-center gap-4">
                    <a href="{{ route('home') }}" class="text-sm font-semibold text-slate-700 hover:text-sky-700 transition">
                        Katalog Fasilitas
                    </a>

                    @auth
                        <div class="flex items-center gap-3 border-l border-slate-200 pl-4">
                            <div class="text-right hidden sm:block">
                                <span class="block text-sm font-semibold text-slate-800 leading-tight">{{ auth()->user()->name }}</span>
                                <span class="inline-block text-xs font-medium text-sky-700 bg-sky-50 px-2 py-0.5 rounded-md">
                                    {{ auth()->user()->role->label() }}
                                </span>
                            </div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-red-700 hover:border-red-200 transition">
                                    Keluar
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="flex items-center gap-2 border-l border-slate-200 pl-4">
                            <a href="{{ route('login') }}" class="rounded-lg bg-sky-700 px-4 py-2 text-xs sm:text-sm font-semibold text-white shadow-sm hover:bg-sky-800 transition">
                                Masuk / Login
                            </a>
                        </div>
                    @endauth
                </nav>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="border-t border-slate-200 bg-white py-8 text-center text-xs text-slate-500">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <p>© 2026 PPK Room Reservation. Sistem Manajemen Sarana dan Prasarana Kampus.</p>
                <p class="mt-1 text-slate-400">Jam Operasional Pelayanan: 07.00 – 20.00 WIB</p>
            </div>
        </footer>
    </body>
</html>
