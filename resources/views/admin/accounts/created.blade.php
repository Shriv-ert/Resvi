@extends('layouts.admin')

@section('title', 'Kredensial Akun Baru')

@section('content')
<div class="mx-auto max-w-xl space-y-6">
    <!-- Success Badge & Heading -->
    <div class="text-center">
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700 text-2xl shadow-sm">
            ✓
        </div>
        <h1 class="mt-4 text-2xl font-bold tracking-tight text-slate-900">Akun Berhasil Dibuat!</h1>
        <p class="mt-1 text-sm text-slate-600">
            Berikut adalah rincian akun dan kata sandi awal yang telah dibangkitkan secara otomatis.
        </p>
    </div>

    <!-- Security Alert: Shown ONCE -->
    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-amber-900 shadow-sm">
        <div class="flex items-start gap-3">
            <span class="text-amber-700 font-bold text-lg leading-none">⚠</span>
            <div class="text-xs leading-5">
                <span class="font-bold">PERINGATAN KEAMANAN:</span>
                Kata sandi awal di bawah ini <strong>hanya ditampilkan satu kali</strong> pada layar ini. Setelah Anda meninggalkan atau menyegarkan halaman ini, kata sandi awal tidak dapat dilihat kembali demi alasan privasi dan keamanan sistem.
            </div>
        </div>
    </div>

    <!-- Credential Card -->
    <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
        <!-- Account Info Grid -->
        <dl class="grid grid-cols-1 gap-y-4 sm:grid-cols-2 text-sm border-b border-slate-100 pb-6">
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Nama Lengkap</dt>
                <dd class="mt-1 font-semibold text-slate-900">{{ $account['name'] }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Alamat Email</dt>
                <dd class="mt-1 font-mono text-xs font-semibold text-slate-900">{{ $account['email'] }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Peran Akun</dt>
                <dd class="mt-1 font-semibold capitalize text-slate-900">
                    {{ is_object($account['role']) ? $account['role']->label() : ucfirst($account['role']) }}
                </dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Kategori Civitas</dt>
                <dd class="mt-1 font-semibold capitalize text-slate-900">
                    @if (! empty($account['category']))
                        {{ is_object($account['category']) ? $account['category']->label() : ucfirst($account['category']) }}
                    @else
                        <span class="text-slate-400">—</span>
                    @endif
                </dd>
            </div>
        </dl>

        <!-- Initial Password Box -->
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">
                Kata Sandi Awal Pengguna
            </label>
            <div class="relative flex items-center">
                <input
                    type="text"
                    id="plainPasswordInput"
                    value="{{ $account['plain_password'] }}"
                    readonly
                    class="block w-full rounded-xl border-2 border-dashed border-sky-300 bg-sky-50/50 px-4 py-3 font-mono text-base font-bold text-sky-950 tracking-wider outline-none select-all shadow-inner"
                >
                <button
                    type="button"
                    id="copyPasswordBtn"
                    class="absolute right-2 inline-flex items-center gap-1.5 rounded-lg bg-sky-700 px-3.5 py-1.5 text-xs font-bold text-white shadow-sm transition hover:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-500"
                >
                    <span id="copyIcon">📋</span>
                    <span id="copyText">Salin Kata Sandi</span>
                </button>
            </div>
            <p class="mt-2 text-[11px] text-slate-500">
                Sampaikan kredensial email dan kata sandi di atas kepada pemilik akun melalui kanal komunikasi resmi kampus.
            </p>
        </div>

        <!-- Completion Button -->
        <div class="pt-4 border-t border-slate-100 text-center">
            <a
                href="{{ route('admin.accounts.index') }}"
                class="inline-flex w-full justify-center rounded-xl bg-slate-900 px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-slate-800 focus:outline-none focus:ring-4 focus:ring-slate-300"
            >
                Saya Sudah Mencatat & Lanjut ke Daftar Akun
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Vanilla JS untuk Web Clipboard API
    document.addEventListener('DOMContentLoaded', function () {
        const copyBtn = document.getElementById('copyPasswordBtn');
        const passwordInput = document.getElementById('plainPasswordInput');
        const copyText = document.getElementById('copyText');
        const copyIcon = document.getElementById('copyIcon');

        if (copyBtn && passwordInput) {
            copyBtn.addEventListener('click', function () {
                passwordInput.select();
                navigator.clipboard.writeText(passwordInput.value).then(() => {
                    copyText.textContent = 'Tersalin!';
                    copyIcon.textContent = '✓';
                    copyBtn.classList.remove('bg-sky-700', 'hover:bg-sky-800');
                    copyBtn.classList.add('bg-emerald-600', 'hover:bg-emerald-700');

                    setTimeout(() => {
                        copyText.textContent = 'Salin Kata Sandi';
                        copyIcon.textContent = '📋';
                        copyBtn.classList.remove('bg-emerald-600', 'hover:bg-emerald-700');
                        copyBtn.classList.add('bg-sky-700', 'hover:bg-sky-800');
                    }, 2500);
                }).catch(err => {
                    console.error('Gagal menyalin:', err);
                });
            });
        }
    });
</script>
@endpush
@endsection
