@extends('layouts.admin')

@section('title', 'Akun Berhasil Dibuat · Admin')

@section('content')
@php
    $acc = $account ?? $createdAccount ?? session('created_account') ?? [];
    $accName = $acc['name'] ?? 'Civitas Baru';
    $accEmail = $acc['email'] ?? 'civitas@ppk.test';
    
    $rawRole = $acc['role'] ?? 'Pengguna';
    $accRole = is_object($rawRole) && method_exists($rawRole, 'label') 
        ? $rawRole->label() 
        : (is_object($rawRole) ? ($rawRole->value ?? (string)$rawRole) : ucfirst((string)$rawRole));
        
    $rawCategory = $acc['category'] ?? $acc['user_category'] ?? null;
    $accCategory = is_object($rawCategory) && method_exists($rawCategory, 'label') 
        ? $rawCategory->label() 
        : (is_object($rawCategory) ? ($rawCategory->value ?? (string)$rawCategory) : ($rawCategory ? ucfirst((string)$rawCategory) : null));
        
    $accPassword = $acc['plain_password'] ?? $acc['temporary_password'] ?? 'PPK-Pass-8942!';
@endphp

<div class="max-w-[1200px] mx-auto px-4 sm:px-6 py-8">

    <!-- Success Card -->
    <div class="bg-[#111111] border border-[#333333] rounded-[6px] p-6 sm:p-8 shadow-none text-center space-y-6 max-w-2xl mx-auto">
        
        <!-- Success Icon -->
        <div class="w-12 h-12 rounded-[6px] bg-[#002b11] text-[#50e3c2] flex items-center justify-center mx-auto border border-[#005522]">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>

        <div>
            <h1 class="text-2xl font-semibold text-white tracking-[-0.03em]">Akun Berhasil Dibuat!</h1>
            <p class="text-xs text-[#a1a1a1] mt-1">Akun civitas kampus baru telah aktif dan siap digunakan untuk masuk ke sistem.</p>
        </div>

        <!-- Account Credentials Display Card (SRS FR-ADM-03: Kata sandi awal tampil SATU KALI) -->
        <div class="bg-[#0a0a0a] border border-[#333333] rounded-[6px] p-5 text-left space-y-4">
            <div class="flex items-center justify-between border-b border-[#333333] pb-3">
                <span class="text-xs font-semibold text-white font-mono">Kredensial Login Awal</span>
                <span class="px-2 py-0.5 rounded-[6px] text-[10px] font-mono font-medium bg-[#2b1e00] text-[#f5a623] border border-[#553c00]">
                    Tampil 1x Saja
                </span>
            </div>

            <div class="space-y-3 text-xs">
                <div>
                    <span class="text-[#888888] block mb-0.5">Nama Lengkap:</span>
                    <strong class="text-white font-medium text-sm">{{ $accName }}</strong>
                </div>

                <div>
                    <span class="text-[#888888] block mb-0.5">Alamat Email:</span>
                    <strong class="text-white font-mono text-sm">{{ $accEmail }}</strong>
                </div>

                <div>
                    <span class="text-[#888888] block mb-0.5">Peran & Kategori:</span>
                    <strong class="text-white capitalize text-sm font-medium">
                        {{ $accRole }}
                        @if($accCategory)
                            ({{ $accCategory }})
                        @endif
                    </strong>
                </div>

                <!-- Password Box -->
                <div class="pt-2">
                    <span class="text-[#888888] block mb-1">Kata Sandi Awal:</span>
                    <div class="flex items-center justify-between bg-[#161616] border border-[#333333] rounded-[6px] px-3.5 py-2.5">
                        <code class="font-mono text-base font-semibold text-white tracking-wider" id="generatedPassword">{{ $accPassword }}</code>
                        <input type="hidden" id="plainPasswordInput" value="{{ $accPassword }}">
                        <button 
                            type="button" 
                            onclick="copyPassword()" 
                            class="bg-white hover:bg-[#e6e6e6] text-black font-medium text-xs px-3 py-1.5 rounded-[6px] transition flex items-center gap-1.5 active:scale-95 shadow-none"
                            id="btnCopy">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                            <span id="copyText">Salin Kata Sandi</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Warning Notice (SRS BR-ADM-01) -->
            <div class="p-3 rounded-[6px] bg-[#2b0000] border border-[#550000] text-[11px] text-[#ff453a] leading-relaxed font-mono">
                <strong class="font-semibold text-white font-sans">Penting:</strong> Catat atau salin kata sandi ini sekarang untuk disampaikan kepada pemilik akun di luar sistem. Setelah Anda menutup atau meninggalkan halaman ini, kata sandi awal <strong class="text-white">tidak dapat dibuka kembali</strong> oleh administrator.
            </div>
        </div>

        <div>
            <a href="{{ route('admin.accounts.index') }}" 
               class="bg-[#171717] hover:bg-[#222222] text-white border border-[#333333] font-medium text-xs px-5 py-2.5 rounded-[6px] transition inline-flex items-center gap-2 shadow-none">
                Saya Sudah Mencatat & Lanjut ke Daftar Akun
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function copyPassword() {
        const pass = document.getElementById('generatedPassword')?.innerText?.trim() || document.getElementById('plainPasswordInput')?.value || '';
        navigator.clipboard.writeText(pass).then(() => {
            const copyText = document.getElementById('copyText');
            if (copyText) {
                copyText.innerText = 'Tersalin!';
                setTimeout(() => {
                    copyText.innerText = 'Salin Kata Sandi';
                }, 2000);
            }
        });
    }
</script>
@endpush
@endsection
