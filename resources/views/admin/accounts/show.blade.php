@extends('layouts.admin')

@section('title', 'Detail Akun: ' . $user->name)

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs font-medium text-slate-500">
        <a href="{{ route('admin.accounts.index') }}" class="hover:text-slate-900 transition">Kelola Akun</a>
        <span>/</span>
        <span class="text-slate-900">Rincian Akun</span>
    </nav>

    <!-- Header Section -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $user->name }}</h1>
            <p class="mt-1 font-mono text-xs text-slate-500">{{ $user->email }}</p>
        </div>
        <div class="flex items-center gap-2.5">
            <button
                type="button"
                id="openPasswordResetModalBtn"
                class="inline-flex items-center gap-2 rounded-xl bg-amber-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-amber-700 focus:outline-none focus:ring-4 focus:ring-amber-200"
            >
                <span>🔑</span>
                Ganti Kata Sandi
            </button>
            <a
                href="{{ route('admin.accounts.index') }}"
                class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50"
            >
                ← Kembali
            </a>
        </div>
    </div>

    <!-- Account Details Card -->
    <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
        <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">
            Informasi Profil & Kredensial
        </h2>

        <dl class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2 text-sm">
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Nama Lengkap</dt>
                <dd class="mt-1 font-semibold text-slate-900">{{ $user->name }}</dd>
            </div>

            <div>
                <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Alamat Email</dt>
                <dd class="mt-1 font-mono text-xs font-semibold text-slate-900">{{ $user->email }}</dd>
            </div>

            <div>
                <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Peran Sistem</dt>
                <dd class="mt-1">
                    @if ($user->role->value === 'petugas')
                        <span class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700 ring-1 ring-inset ring-indigo-700/10">
                            Petugas
                        </span>
                    @elseif ($user->role->value === 'pengguna')
                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-700/10">
                            Pengguna
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                            {{ $user->role->label() }}
                        </span>
                    @endif
                </dd>
            </div>

            <div>
                <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Kategori Civitas</dt>
                <dd class="mt-1">
                    @if ($user->user_category)
                        @php
                            $catVal = $user->user_category->value ?? (string) $user->user_category;
                        @endphp
                        @if ($catVal === 'mahasiswa')
                            <span class="inline-flex items-center rounded-full bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-700 ring-1 ring-inset ring-sky-700/10">
                                Mahasiswa
                            </span>
                        @elseif ($catVal === 'dosen')
                            <span class="inline-flex items-center rounded-full bg-purple-50 px-2.5 py-1 text-xs font-semibold text-purple-700 ring-1 ring-inset ring-purple-700/10">
                                Dosen
                            </span>
                        @elseif ($catVal === 'staf')
                            <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700 ring-1 ring-inset ring-amber-700/10">
                                Staf
                            </span>
                        @endif
                    @else
                        <span class="text-slate-400">—</span>
                    @endif
                </dd>
            </div>

            <div>
                <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Terdaftar Pada</dt>
                <dd class="mt-1 text-xs text-slate-700">
                    {{ $user->created_at ? $user->created_at->translatedFormat('d F Y, H:i') : '-' }} WIB
                </dd>
            </div>

            <div>
                <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Pembaruan Terakhir</dt>
                <dd class="mt-1 text-xs text-slate-700">
                    {{ $user->updated_at ? $user->updated_at->translatedFormat('d F Y, H:i') : '-' }} WIB
                </dd>
            </div>
        </dl>

        <!-- Cryptographic Security Badge -->
        <div class="rounded-xl border border-slate-200 bg-slate-50/80 p-4 text-xs text-slate-600">
            <div class="flex items-start gap-2.5">
                <span class="text-slate-400 font-bold">🔒</span>
                <div class="leading-5">
                    <strong>Privasi & Keamanan Kredensial:</strong>
                    Kata sandi akun ini dienkripsi menggunakan algoritma *Bcrypt* satu arah. Admin maupun pengguna tidak dapat melihat kembali teks asli kata sandi lama. Jika pengguna lupa kata sandi, gunakan tombol <strong>"Ganti Kata Sandi"</strong> di atas.
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.accounts.partials.password-modal', ['user' => $user])
@endsection
