@extends('layouts.admin')

@section('title', 'Kelola Akun')

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Kelola Akun</h1>
            <p class="mt-1 text-sm text-slate-600">
                Pusat manajemen akun Petugas dan Pengguna (Mahasiswa, Dosen, Staf) terpusat tanpa registrasi mandiri.
            </p>
        </div>
        <div>
            <a
                href="{{ route('admin.accounts.create') }}"
                class="inline-flex items-center gap-2 rounded-xl bg-sky-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-800 focus:outline-none focus:ring-4 focus:ring-sky-200"
            >
                <span class="text-base leading-none">+</span>
                Buat Akun Baru
            </a>
        </div>
    </div>

    <!-- Filter & Search Panel -->
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('admin.accounts.index') }}" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Search Keyword -->
            <div class="lg:col-span-2">
                <label for="search" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Cari Akun</label>
                <input
                    type="text"
                    id="search"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Ketik nama atau alamat email..."
                    class="mt-1.5 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm shadow-sm outline-none transition placeholder:text-slate-400 focus:border-sky-600 focus:ring-4 focus:ring-sky-100"
                >
            </div>

            <!-- Role Filter -->
            <div>
                <label for="role" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Peran</label>
                <select
                    id="role"
                    name="role"
                    class="mt-1.5 block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-sm shadow-sm outline-none transition focus:border-sky-600 focus:ring-4 focus:ring-sky-100"
                >
                    <option value="">Semua Peran</option>
                    <option value="petugas" {{ request('role') === 'petugas' ? 'selected' : '' }}>Petugas</option>
                    <option value="pengguna" {{ request('role') === 'pengguna' ? 'selected' : '' }}>Pengguna</option>
                </select>
            </div>

            <!-- Category Filter -->
            <div>
                <label for="category" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Kategori Pengguna</label>
                <select
                    id="category"
                    name="category"
                    class="mt-1.5 block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-sm shadow-sm outline-none transition focus:border-sky-600 focus:ring-4 focus:ring-sky-100"
                >
                    <option value="">Semua Kategori</option>
                    <option value="mahasiswa" {{ request('category') === 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                    <option value="dosen" {{ request('category') === 'dosen' ? 'selected' : '' }}>Dosen</option>
                    <option value="staf" {{ request('category') === 'staf' ? 'selected' : '' }}>Staf</option>
                </select>
            </div>

            <!-- Submit & Reset Buttons -->
            <div class="flex items-center gap-2 sm:col-span-2 lg:col-span-4 pt-1">
                <button
                    type="submit"
                    class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800"
                >
                    Terapkan Filter
                </button>

                @if(request()->hasAny(['search', 'role', 'category']))
                    <a
                        href="{{ route('admin.accounts.index') }}"
                        class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                    >
                        Reset Filter
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table of Accounts -->
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="border-b border-slate-200 bg-slate-50/75 text-xs font-semibold uppercase tracking-wider text-slate-700">
                    <tr>
                        <th class="px-6 py-4">No.</th>
                        <th class="px-6 py-4">Nama Lengkap</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Peran</th>
                        <th class="px-6 py-4">Kategori Civitas</th>
                        <th class="px-6 py-4">Terdaftar</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($accounts as $index => $account)
                        <tr class="transition hover:bg-slate-50/60">
                            <td class="whitespace-nowrap px-6 py-4 font-medium text-slate-500">
                                {{ $accounts->firstItem() + $index }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 font-semibold text-slate-900">
                                {{ $account->name }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 font-mono text-xs text-slate-600">
                                {{ $account->email }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                @if ($account->role->value === 'petugas')
                                    <span class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700 ring-1 ring-inset ring-indigo-700/10">
                                        Petugas
                                    </span>
                                @elseif ($account->role->value === 'pengguna')
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-700/10">
                                        Pengguna
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                        {{ $account->role->label() }}
                                    </span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                @if ($account->user_category)
                                    @php
                                        $categoryVal = $account->user_category->value ?? (string) $account->user_category;
                                    @endphp
                                    @if ($categoryVal === 'mahasiswa')
                                        <span class="inline-flex items-center rounded-full bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-700 ring-1 ring-inset ring-sky-700/10">
                                            Mahasiswa
                                        </span>
                                    @elseif ($categoryVal === 'dosen')
                                        <span class="inline-flex items-center rounded-full bg-purple-50 px-2.5 py-1 text-xs font-semibold text-purple-700 ring-1 ring-inset ring-purple-700/10">
                                            Dosen
                                        </span>
                                    @elseif ($categoryVal === 'staf')
                                        <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700 ring-1 ring-inset ring-amber-700/10">
                                            Staf
                                        </span>
                                    @endif
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-xs text-slate-500">
                                {{ $account->created_at ? $account->created_at->translatedFormat('d M Y, H:i') : '-' }} WIB
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                <a
                                    href="{{ route('admin.accounts.show', $account) }}"
                                    class="inline-flex items-center gap-1 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 hover:text-slate-900"
                                >
                                    Rincian & Reset
                                    <span>→</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="mx-auto flex max-w-sm flex-col items-center">
                                    <span class="text-3xl text-slate-400">👤</span>
                                    <h3 class="mt-3 text-sm font-semibold text-slate-900">Tidak ada akun ditemukan</h3>
                                    <p class="mt-1 text-xs text-slate-500">
                                        Tidak ada akun Petugas atau Pengguna yang cocok dengan kriteria pencarian dan filter saat ini.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($accounts->hasPages())
            <div class="border-t border-slate-200 px-6 py-4">
                {{ $accounts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
