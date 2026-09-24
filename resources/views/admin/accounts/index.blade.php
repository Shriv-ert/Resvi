@extends('layouts.admin')

@section('title', 'Kelola Akun · Admin')

@section('content')
<div class="max-w-[1200px] mx-auto px-4 sm:px-6 py-8">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-semibold text-white tracking-[-0.03em]">
                Kelola Akun
            </h1>
            <p class="text-xs sm:text-sm text-[#a1a1a1] mt-1">
                Pusat manajemen akun Petugas dan Pengguna (Mahasiswa, Dosen, Staf) terpusat tanpa registrasi mandiri.
            </p>
        </div>

        <div>
            <a href="{{ route('admin.accounts.create') }}" 
               class="bg-white hover:bg-[#e6e6e6] text-black font-medium text-xs px-4 py-2 rounded-[6px] transition flex items-center gap-2 active:scale-95 shadow-none">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                Buat Akun Baru
            </a>
        </div>
    </div>

    <!-- Filter & Search Panel (Server-side & Client-side compatible) -->
    <form method="GET" action="{{ route('admin.accounts.index') }}" class="bg-[#111111] border border-[#333333] rounded-[6px] p-4 mb-6 grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
        <div class="relative">
            <label for="searchAccount" class="block text-[10px] font-mono uppercase text-[#888888] tracking-wider mb-1.5">Cari Akun</label>
            <div class="relative">
                <input 
                    type="text" 
                    id="searchAccount" 
                    name="search"
                    value="{{ request('search', $search ?? '') }}"
                    placeholder="Ketik nama atau email..." 
                    class="w-full bg-[#0a0a0a] border border-[#333333] text-white text-xs rounded-[6px] pl-8 pr-3 py-2 focus:outline-none focus:border-white transition placeholder-[#666666]"
                />
                <svg class="w-3.5 h-3.5 text-[#666666] absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </div>

        <div>
            <label for="roleSelect" class="block text-[10px] font-mono uppercase text-[#888888] tracking-wider mb-1.5">Peran</label>
            <select 
                id="roleSelect" 
                name="role"
                class="w-full bg-[#0a0a0a] border border-[#333333] text-white text-xs rounded-[6px] px-3 py-2 focus:outline-none focus:border-white transition font-mono"
            >
                <option value="">Semua Peran</option>
                <option value="petugas" {{ request('role', $selectedRole ?? '') === 'petugas' ? 'selected' : '' }}>Petugas</option>
                <option value="pengguna" {{ request('role', $selectedRole ?? '') === 'pengguna' ? 'selected' : '' }}>Pengguna</option>
            </select>
        </div>

        <div>
            <label for="categorySelect" class="block text-[10px] font-mono uppercase text-[#888888] tracking-wider mb-1.5">Kategori Civitas</label>
            <select 
                id="categorySelect" 
                name="category"
                class="w-full bg-[#0a0a0a] border border-[#333333] text-white text-xs rounded-[6px] px-3 py-2 focus:outline-none focus:border-white transition font-mono"
            >
                <option value="">Semua Kategori</option>
                <option value="mahasiswa" {{ request('category', $selectedCategory ?? '') === 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                <option value="dosen" {{ request('category', $selectedCategory ?? '') === 'dosen' ? 'selected' : '' }}>Dosen</option>
                <option value="staf" {{ request('category', $selectedCategory ?? '') === 'staf' ? 'selected' : '' }}>Staf</option>
            </select>
        </div>

        <div class="flex items-center gap-2">
            <button 
                type="submit" 
                class="flex-1 bg-white hover:bg-[#e6e6e6] text-black font-medium text-xs py-2 px-3 rounded-[6px] transition text-center">
                Filter
            </button>
            @if(request()->hasAny(['search', 'role', 'category']))
                <a 
                    href="{{ route('admin.accounts.index') }}" 
                    class="bg-[#1f1f1f] hover:bg-[#2a2a2a] text-[#888888] hover:text-white border border-[#333333] font-medium text-xs py-2 px-3 rounded-[6px] transition text-center">
                    Reset
                </a>
            @endif
        </div>
    </form>

    <!-- Accounts Table -->
    <div class="bg-[#111111] border border-[#333333] rounded-[6px] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-[#ededed]" id="accountsTable">
                <thead class="bg-[#161616] border-b border-[#333333] text-[10px] font-mono uppercase text-[#888888] tracking-wider">
                    <tr>
                        <th class="py-3 px-5 font-medium">Nama Lengkap</th>
                        <th class="py-3 px-5 font-medium">Alamat Email</th>
                        <th class="py-3 px-5 font-medium">Peran Akun</th>
                        <th class="py-3 px-5 font-medium">Kategori Pengguna</th>
                        <th class="py-3 px-5 font-medium">Waktu Pendaftaran</th>
                        <th class="py-3 px-5 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#222222] text-xs">
                    @php
                        $accountList = $accounts ?? $users ?? [];
                    @endphp
                    @forelse($accountList as $u)
                        @php
                            $uId = is_array($u) ? $u['id'] : $u->id;
                            $uName = is_array($u) ? $u['name'] : $u->name;
                            $uEmail = is_array($u) ? $u['email'] : $u->email;
                            $uRole = is_array($u) 
                                ? ($u['role'] ?? '') 
                                : ($u->role?->value ?? (string)$u->role);
                            $uCategory = is_array($u) 
                                ? ($u['user_category'] ?? null) 
                                : ($u->user_category?->value ?? (string)$u->user_category);
                            $uCreated = is_array($u) 
                                ? ($u['created_at'] ?? '-') 
                                : ($u->created_at ? $u->created_at->translatedFormat('d M Y, H:i') . ' WIB' : '-');
                        @endphp
                        <tr class="hover:bg-[#161616] transition-colors"
                            data-search="{{ strtolower($uName . ' ' . $uEmail) }}"
                            data-role="{{ $uRole }}">
                            
                            <td class="py-3.5 px-5">
                                <span class="font-medium text-white block">{{ $uName }}</span>
                            </td>

                            <td class="py-3.5 px-5 text-[#a1a1a1] font-mono text-[11px]">
                                {{ $uEmail }}
                            </td>

                            <td class="py-3.5 px-5">
                                @if($uRole === 'petugas')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#50e3c2] bg-[#002b11] border border-[#005522]">
                                        Petugas
                                    </span>
                                @elseif($uRole === 'pengguna')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#a1a1a1] bg-[#1a1a1a] border border-[#333333]">
                                        Pengguna
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#0070f3] bg-[#001733] border border-[#003b80]">
                                        {{ ucfirst($uRole) }}
                                    </span>
                                @endif
                            </td>

                            <td class="py-3.5 px-5 capitalize text-[#a1a1a1] font-mono text-[11px]">
                                @if ($uCategory === 'mahasiswa')
                                    <span class="text-sky-400">Mahasiswa</span>
                                @elseif ($uCategory === 'dosen')
                                    <span class="text-purple-400">Dosen</span>
                                @elseif ($uCategory === 'staf')
                                    <span class="text-amber-400">Staf</span>
                                @elseif ($uCategory)
                                    <span>{{ ucfirst($uCategory) }}</span>
                                @else
                                    <span class="text-[#555555]">—</span>
                                @endif
                            </td>

                            <td class="py-3.5 px-5 text-[#888888] font-mono text-[11px]">
                                {{ $uCreated }}
                            </td>

                            <td class="py-3.5 px-5 text-right">
                                <a href="{{ route('admin.accounts.show', is_array($u) ? ['user' => $uId] : $u) }}" 
                                   class="text-white hover:text-[#0070f3] font-medium transition inline-flex items-center gap-1 text-xs">
                                    Detail & Reset →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-[#888888]">
                                <div class="mx-auto flex max-w-sm flex-col items-center">
                                    <span class="text-2xl text-[#555555] mb-2">👤</span>
                                    <h3 class="text-xs font-semibold text-white">Tidak ada akun ditemukan</h3>
                                    <p class="mt-1 text-[11px] text-[#888888]">
                                        Belum ada akun civitas terdaftar atau tidak ada data yang cocok dengan kriteria pencarian.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if (isset($accounts) && method_exists($accounts, 'hasPages') && $accounts->hasPages())
            <div class="border-t border-[#333333] px-5 py-3">
                {{ $accounts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
