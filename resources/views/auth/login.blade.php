<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk · PPK Room Reservation</title>

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
                        'canvas': '#0a0a0a',
                        'canvas-soft': '#111111',
                        'body': '#a1a1a1',
                        'mute': '#666666',
                        'hairline': '#333333',
                        'link': '#0070f3',
                        'status-danger': '#ff453a',
                    },
                    fontFamily: {
                        sans: ['Geist', 'Inter', 'sans-serif'],
                        mono: ['"Geist Mono"', 'monospace'],
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
        .mesh-line { background: linear-gradient(90deg, #007cf0 0%, #00dfd8 25%, #7928ca 50%, #ff0080 75%, #f9cb28 100%); }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between bg-[#0a0a0a] text-[#ededed] relative overflow-hidden">

    <!-- Top Mesh Line -->
    <div class="h-[2px] w-full mesh-line"></div>

    <main class="w-full max-w-md mx-auto p-4 my-auto relative z-10">
        
        <!-- Back Link -->
        <div class="mb-4">
            <a href="{{ route('facilities.index') }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-[#a1a1a1] hover:text-white transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Katalog Fasilitas
            </a>
        </div>

        <!-- Clean Vercel Auth Card: Crisp border, micro 6px radius, NO soft shadows -->
        <section class="bg-[#111111] border border-[#333333] rounded-[6px] p-8 space-y-6">
            
            <!-- Card Header -->
            <div class="flex items-center">
                <div>
                    <h1 class="text-sm font-semibold text-white tracking-tight leading-tight">PPK Room Reservation</h1>
                    <p class="font-mono text-[9px] text-[#888888] uppercase tracking-wider">Campus Account Verification</p>
                </div>
            </div>

            <div>
                <h2 class="text-xl font-semibold text-white tracking-[-0.03em]">Selamat Datang.</h2>
                <p class="text-xs text-[#a1a1a1] mt-1">Masuk dengan kredensial akun yang didaftarkan Administrator.</p>
            </div>

            <!-- Error Banner -->
            @if ($errors->any())
                <div class="p-3.5 rounded-[6px] bg-[#2b0000] border border-[#ff453a]/40 text-xs text-[#ff453a] space-y-1">
                    <div class="flex items-center gap-2 font-medium">
                        <svg class="w-4 h-4 shrink-0 text-[#ff453a]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Gagal Masuk</span>
                    </div>
                    <p class="text-[11px] text-[#ededed]/90 pl-6">
                        {{ $errors->first('login') ?: $errors->first('email') ?: $errors->first('password') ?: $errors->first() }}
                    </p>
                </div>
            @endif

            @if(session('status'))
                <div class="p-3 rounded-[6px] bg-[#002b11] border border-[#005522] text-xs text-[#50e3c2]">
                    {{ session('status') }}
                </div>
            @endif

            @if(request()->has('draft_facility'))
                <div class="p-3 rounded-[6px] bg-[#001733] border border-[#003b80] text-xs text-[#0070f3] flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Draft reservasi Anda disimpan dan akan otomatis dimuat setelah login.</span>
                </div>
            @endif

            <form class="space-y-4" method="POST" action="{{ route('login.store') }}">
                @csrf
                @if(request()->has('draft_facility'))
                    <input type="hidden" name="draft_facility" value="{{ request('draft_facility') }}">
                @endif

                <!-- Email Input -->
                <div class="space-y-1.5">
                    <label class="block font-mono text-[10px] uppercase tracking-wider text-[#888888] font-medium" for="email">Alamat Email</label>
                    <input
                        class="w-full bg-[#0a0a0a] border {{ $errors->any() ? 'border-[#ff453a]' : 'border-[#333333]' }} text-white text-xs rounded-[6px] px-3 py-2 focus:outline-none focus:border-white transition placeholder-[#666666]"
                        id="email"
                        name="email"
                        type="email"
                        required
                        value="{{ old('email', 'admin@ppk.test') }}"
                        placeholder="nama@kampus.ac.id"
                    />
                </div>

                <!-- Password Input -->
                <div class="space-y-1.5">
                    <label class="block font-mono text-[10px] uppercase tracking-wider text-[#888888] font-medium" for="password">Kata Sandi</label>
                    <input
                        class="w-full bg-[#0a0a0a] border {{ $errors->any() ? 'border-[#ff453a]' : 'border-[#333333]' }} text-white text-xs rounded-[6px] px-3 py-2 focus:outline-none focus:border-white transition placeholder-[#666666]"
                        id="password"
                        name="password"
                        type="password"
                        required
                        value="password123"
                        placeholder="••••••••"
                    />
                </div>

                <!-- Submit Button -->
                <button
                    class="w-full bg-white hover:bg-[#e6e6e6] text-black font-medium text-xs py-2.5 rounded-full transition active:scale-95 shadow-none mt-2"
                    type="submit"
                >
                    Masuk ke Sistem
                </button>
            </form>

            <!-- Quick Demo Login Helpers -->
            <div class="pt-4 border-t border-[#333333] space-y-2">
                <div class="flex items-center justify-between">
                    <span class="block font-mono text-[9px] uppercase tracking-wider text-[#888888]">Masuk Cepat Simulator:</span>
                    <span class="font-mono text-[9px] text-[#555555]">Sandi: password123</span>
                </div>
                <div class="grid grid-cols-3 gap-2 text-[11px] font-mono">
                    <button type="button" onclick="setDemo('admin@ppk.test')" class="px-2 py-1.5 rounded-[4px] bg-[#161616] hover:bg-[#222222] text-white border border-[#333333] transition text-center truncate">
                        Admin
                    </button>
                    <button type="button" onclick="setDemo('petugas.demo@ppk.test')" class="px-2 py-1.5 rounded-[4px] bg-[#161616] hover:bg-[#222222] text-white border border-[#333333] transition text-center truncate">
                        Petugas
                    </button>
                    <button type="button" onclick="setDemo('mahasiswa.demo@ppk.test')" class="px-2 py-1.5 rounded-[4px] bg-[#161616] hover:bg-[#222222] text-white border border-[#333333] transition text-center truncate">
                        Pengguna
                    </button>
                </div>
            </div>
        </section>
    </main>

    <script>
        function setDemo(email) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = 'password123';
        }
    </script>
</body>
</html>
