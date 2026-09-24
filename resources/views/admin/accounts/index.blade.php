@extends('layouts.admin')

@section('title', 'Kelola Akun Civitas · Admin')

@section('content')
<div class="max-w-[1200px] mx-auto px-4 sm:px-6 py-8">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-semibold text-white tracking-[-0.03em]">
                Kelola akun civitas.
            </h1>
            <p class="text-xs sm:text-sm text-[#a1a1a1] mt-1">
                Pendaftaran akun pengguna dan petugas secara langsung oleh admin (tanpa registrasi mandiri).
            </p>
        </div>

        <div>
            <a href="{{ route('admin.accounts.create') }}" 
               class="bg-white hover:bg-[#e6e6e6] text-black font-medium text-xs px-4 py-2 rounded-full transition flex items-center gap-2 active:scale-95 shadow-none">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                Buat Akun Baru
            </a>
        </div>
    </div>

    <!-- Search & Role Filter: Crisp border, micro 6px radius -->
    <div class="bg-[#111111] border border-[#333333] rounded-[6px] p-4 mb-6 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="w-full md:w-80 relative">
            <input 
                type="text" 
                id="searchAccount" 
                placeholder="Cari nama atau email..." 
                class="w-full bg-[#0a0a0a] border border-[#333333] text-white text-xs rounded-[6px] pl-8 pr-3 py-2 focus:outline-none focus:border-white transition placeholder-[#666666]"
            />
            <svg class="w-3.5 h-3.5 text-[#666666] absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto">
            <div class="relative custom-dropdown min-w-[210px]" id="dropdown-filterRole">
                <input type="hidden" id="filterRole" value="">
                <button type="button" onclick="toggleCustomDropdown('filterRole')" id="filterRoleTrigger"
                        class="w-full bg-[#0a0a0a] border border-[#333333] hover:border-[#555555] text-white text-xs rounded-[6px] px-3 py-2 flex items-center justify-between focus:outline-none focus:border-white transition font-mono">
                    <span id="filterRoleLabel">Semua Peran</span>
                    <svg id="filterRoleChevron" class="custom-dropdown-chevron w-3.5 h-3.5 text-[#888888] transition-transform duration-200 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
                <div id="filterRoleMenu" class="custom-dropdown-menu hidden absolute top-full right-0 mt-1.5 z-50 bg-[#111111] border border-[#333333] rounded-[6px] shadow-2xl overflow-hidden py-1 min-w-[210px]">
                    <div onclick="selectCustomDropdown('filterRole', '', 'Semua Peran')" data-value="" class="dropdown-item px-3 py-2 text-xs font-mono text-white hover:bg-[#1f1f1f] cursor-pointer flex items-center justify-between bg-[#1f1f1f]">
                        <span>Semua Peran</span>
                        <span class="check-icon text-[#50e3c2] text-xs">✓</span>
                    </div>
                    <div onclick="selectCustomDropdown('filterRole', 'pengguna', 'Pengguna (Mahasiswa/Dosen/Staf)')" data-value="pengguna" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between">
                        <span>Pengguna (Mahasiswa/Dosen/Staf)</span>
                        <span class="check-icon text-[#50e3c2] text-xs hidden">✓</span>
                    </div>
                    <div onclick="selectCustomDropdown('filterRole', 'petugas', 'Petugas Sarpras')" data-value="petugas" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between">
                        <span>Petugas Sarpras</span>
                        <span class="check-icon text-[#50e3c2] text-xs hidden">✓</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Accounts Table: Crisp border, micro 6px radius, NO soft shadows -->
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
                <tbody class="divide-y divide-[#333333] text-xs">
                    @forelse($users as $u)
                        <tr class="hover:bg-[#161616] transition-colors"
                            data-search="{{ strtolower($u['name'] . ' ' . $u['email']) }}"
                            data-role="{{ $u['role'] }}">
                            
                            <td class="py-3.5 px-5">
                                <span class="font-medium text-white block">{{ $u['name'] }}</span>
                            </td>

                            <td class="py-3.5 px-5 text-[#a1a1a1] font-mono text-[11px]">
                                {{ $u['email'] }}
                            </td>

                            <td class="py-3.5 px-5">
                                @if($u['role'] === 'petugas')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#50e3c2] bg-[#002b11] border border-[#005522]">
                                        Petugas
                                    </span>
                                @elseif($u['role'] === 'pengguna')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#a1a1a1] bg-[#1a1a1a] border border-[#333333]">
                                        Pengguna
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#0070f3] bg-[#001733] border border-[#003b80]">
                                        Admin
                                    </span>
                                @endif
                            </td>

                            <td class="py-3.5 px-5 capitalize text-[#a1a1a1] font-mono text-[11px]">
                                {{ $u['user_category'] ?? '—' }}
                            </td>

                            <td class="py-3.5 px-5 text-[#888888] font-mono text-[11px]">
                                {{ $u['created_at'] }}
                            </td>

                            <td class="py-3.5 px-5 text-right">
                                <a href="{{ route('admin.accounts.show', ['user' => $u['id']]) }}" 
                                   class="text-white hover:text-[#0070f3] font-medium transition inline-flex items-center gap-1 text-xs">
                                    Detail →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-[#888888]">
                                Belum ada akun civitas terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const searchAccount = document.getElementById('searchAccount');
    const filterRole = document.getElementById('filterRole');
    const rowsAccounts = document.querySelectorAll('#accountsTable tbody tr[data-search]');

    function filterAccountsTable() {
        const query = searchAccount.value.toLowerCase().trim();
        const role = filterRole.value;

        rowsAccounts.forEach(row => {
            const matchesSearch = !query || row.dataset.search.includes(query);
            const matchesRole = !role || row.dataset.role === role;

            row.style.display = (matchesSearch && matchesRole) ? '' : 'none';
        });
    }

    searchAccount.addEventListener('input', filterAccountsTable);

    function toggleCustomDropdown(name) {
        const menu = document.getElementById(name + 'Menu');
        const chevron = document.getElementById(name + 'Chevron');
        const isOpen = !menu.classList.contains('hidden');

        document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.add('hidden'));
        document.querySelectorAll('.custom-dropdown-chevron').forEach(c => c.classList.remove('rotate-180'));

        if (!isOpen) {
            menu.classList.remove('hidden');
            chevron.classList.add('rotate-180');
        }
    }

    function selectCustomDropdown(name, value, label) {
        document.getElementById(name).value = value;
        document.getElementById(name + 'Label').textContent = label;
        
        const menu = document.getElementById(name + 'Menu');
        menu.querySelectorAll('.dropdown-item').forEach(item => {
            const check = item.querySelector('.check-icon');
            if (item.getAttribute('data-value') === value) {
                item.classList.add('bg-[#1f1f1f]', 'text-white');
                item.classList.remove('text-[#cccccc]');
                if (check) check.classList.remove('hidden');
            } else {
                item.classList.remove('bg-[#1f1f1f]', 'text-white');
                item.classList.add('text-[#cccccc]');
                if (check) check.classList.add('hidden');
            }
        });

        menu.classList.add('hidden');
        const chevron = document.getElementById(name + 'Chevron');
        if (chevron) chevron.classList.remove('rotate-180');

        filterAccountsTable();
    }

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.custom-dropdown')) {
            document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.add('hidden'));
            document.querySelectorAll('.custom-dropdown-chevron').forEach(c => c.classList.remove('rotate-180'));
        }
    });
</script>
@endpush
