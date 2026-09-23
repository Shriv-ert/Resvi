<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Login · {{ config('app.name') }}</title>

        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="min-h-screen bg-gradient-to-br from-sky-50 via-slate-50 to-emerald-50 text-slate-900">
        <main class="mx-auto flex min-h-screen max-w-md items-center px-6 py-12">
            <section class="w-full rounded-2xl border border-slate-200 bg-white p-8 shadow-xl shadow-slate-200/60">
                <a class="text-sm font-semibold text-sky-700 hover:text-sky-900 hover:underline" href="{{ route('home') }}">
                    ← Kembali ke Homepage
                </a>

                <h1 class="mt-7 text-3xl font-bold tracking-tight">Masuk</h1>
                <p class="mt-2 text-sm leading-6 text-slate-600">Gunakan akun yang telah dibuat oleh Admin.</p>

                <form class="mt-8 space-y-5" method="POST" action="{{ route('login.store') }}">
                    @csrf

                    <div>
                        <label class="block text-sm font-semibold" for="email">Email</label>
                        <input
                            class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm shadow-sm outline-none transition placeholder:text-slate-400 focus:border-sky-600 focus:ring-4 focus:ring-sky-100"
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            autocomplete="email"
                            required
                            autofocus
                        >
                        @error('email')
                            <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold" for="password">Kata sandi</label>
                        <input
                            class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm shadow-sm outline-none transition placeholder:text-slate-400 focus:border-sky-600 focus:ring-4 focus:ring-sky-100"
                            id="password"
                            name="password"
                            type="password"
                            autocomplete="current-password"
                            required
                        >
                        @error('password')
                            <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <button class="w-full rounded-lg bg-sky-700 px-4 py-3 text-sm font-bold text-white transition hover:bg-sky-800 focus:outline-none focus:ring-4 focus:ring-sky-200" type="submit">
                        Masuk
                    </button>

                    @error('login')
                        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert" aria-live="polite">
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                </form>
            </section>
        </main>
    </body>
</html>
