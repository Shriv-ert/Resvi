@extends('layouts.admin')

@php
    $userName = is_array($user) ? $user['name'] : $user->name;
    $userEmail = is_array($user) ? $user['email'] : $user->email;
    $rawRole = is_array($user) ? ($user['role'] ?? '') : ($user->role?->value ?? (string)$user->role);
    $userRole = ucfirst($rawRole);
    $rawCat = is_array($user) ? ($user['user_category'] ?? null) : ($user->user_category?->value ?? (string)$user->user_category);
    $userCategory = $rawCat ? ucfirst((string)$rawCat) : null;
    $userCreatedAt = is_array($user) 
        ? ($user['created_at'] ?? '-') 
        : ($user->created_at ? $user->created_at->translatedFormat('d F Y, H:i') . ' WIB' : '-');
    $userUpdatedAt = is_array($user) 
        ? ($user['updated_at'] ?? '-') 
        : ($user->updated_at ? $user->updated_at->translatedFormat('d F Y, H:i') . ' WIB' : '-');
@endphp

@section('title', 'Detail Akun ' . $userName . ' · Admin')

@section('content')
<div class="max-w-[1200px] mx-auto px-4 sm:px-6 py-8">

    <!-- Breadcrumb -->
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('admin.accounts.index') }}" 
           class="inline-flex items-center gap-2 text-xs font-medium text-[#888888] hover:text-white transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke kelola akun
        </a>
    </div>

    <!-- Main Detail Panel -->
    <div class="bg-[#111111] border border-[#333333] rounded-[6px] p-6 sm:p-8 shadow-none space-y-6">
        
        <div class="border-b border-[#333333] pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-[6px] bg-[#1a1a1a] border border-[#333333] text-white font-semibold text-sm flex items-center justify-center font-mono">
                    {{ substr($userName, 0, 2) }}
                </div>
                <div>
                    <h1 class="text-xl font-semibold text-white tracking-[-0.03em]">{{ $userName }}</h1>
                    <p class="text-xs text-[#a1a1a1] font-mono">{{ $userEmail }}</p>
                </div>
            </div>

            <!-- Ganti Kata Sandi Button -> opens modal (SRS FR-ADM-04) -->
            <div>
                <button 
                    type="button" 
                    id="openPasswordResetModalBtn"
                    onclick="openPasswordModal()" 
                    class="bg-[#171717] hover:bg-[#222222] text-white border border-[#333333] hover:border-white text-xs font-medium px-3.5 py-2 rounded-[6px] transition flex items-center gap-2 shadow-none">
                    <svg class="w-3.5 h-3.5 text-[#888888]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                    Tetapkan Kata Sandi Baru
                </button>
            </div>
        </div>

        <!-- 3 Grid Info Cells (Adjacent Vercel Architecture) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 bg-[#333333] gap-[1px] border border-[#333333] rounded-[6px] overflow-hidden">
            <div class="bg-[#111111] p-4">
                <span class="text-[11px] text-[#888888] block mb-0.5 font-mono">Peran Akses</span>
                <strong class="text-sm text-white font-medium capitalize">{{ $userRole }}</strong>
            </div>

            <div class="bg-[#111111] p-4">
                <span class="text-[11px] text-[#888888] block mb-0.5 font-mono">Kategori Civitas</span>
                <strong class="text-sm text-white font-medium capitalize">{{ $userCategory ?? '— (Petugas)' }}</strong>
            </div>

            <div class="bg-[#111111] p-4">
                <span class="text-[11px] text-[#888888] block mb-0.5 font-mono">Terdaftar Sejak</span>
                <strong class="text-sm text-[#a1a1a1] font-mono text-xs">{{ $userCreatedAt }}</strong>
            </div>
        </div>

        <!-- Security Notice (SRS BR-ADM-01 & BR-ADM-04) -->
        <div class="p-3.5 rounded-[6px] bg-[#0a0a0a] border border-[#333333] text-xs text-[#a1a1a1] space-y-1.5">
            <strong class="text-white block font-semibold font-mono text-[11px] uppercase tracking-wide">Keamanan Kredensial:</strong>
            <p>• Kata sandi tersimpan dalam bentuk enkripsi hash satu arah yang aman (*Bcrypt*). Admin maupun pengguna tidak dapat melihat kata sandi lama.</p>
            <p>• Jika pemilik akun lupa kata sandi, gunakan tombol <strong class="text-white">"Tetapkan Kata Sandi Baru"</strong> di atas untuk mengatur ulang kata sandi tanpa perlu mengetahui kata sandi lama.</p>
        </div>
    </div>
</div>

<!-- Password Reset Modal -->
@include('admin.accounts.partials.password-modal', ['user' => $user])
@endsection
