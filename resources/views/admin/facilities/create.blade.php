@extends('layouts.admin')

@section('title', 'Tambah Fasilitas Baru · Admin')

@section('content')
<div class="max-w-[1200px] mx-auto px-4 sm:px-6 py-8">

    <!-- Breadcrumb -->
    <div class="mb-6">
        <a href="{{ route('admin.facilities.index') }}" 
           class="inline-flex items-center gap-2 text-xs font-medium text-[#888888] hover:text-white transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke kelola fasilitas
        </a>
    </div>

    <!-- Header Section -->
    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-semibold text-white tracking-[-0.03em]">
            Tambah fasilitas baru.
        </h1>
        <p class="mt-1 text-xs sm:text-sm text-[#a1a1a1]">
            Daftarkan ruangan, laboratorium, aula, lapangan, atau alat khusus ke dalam katalog master sistem.
        </p>
    </div>

    <!-- Form Container -->
    <div class="bg-[#111111] border border-[#333333] rounded-[6px] p-6 sm:p-8 shadow-none">
        <form method="POST" action="{{ route('admin.facilities.index') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Facility Name -->
            <div class="space-y-1.5">
                <label for="name" class="block text-xs font-medium text-[#a1a1a1]">
                    Nama fasilitas <span class="text-[#ff453a]">*</span>
                </label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    required 
                    placeholder="Contoh: Lab Komputer AI 302, Aula Gedung Utama..."
                    class="w-full bg-[#0a0a0a] border border-[#333333] text-white text-sm rounded-[6px] px-3.5 py-2.5 focus:outline-none focus:border-white transition placeholder-[#666666]"
                />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Type Selection -->
                <div class="space-y-1.5 relative custom-dropdown" id="dropdown-type">
                    <label class="block text-xs font-medium text-[#a1a1a1]">
                        Tipe fasilitas <span class="text-[#ff453a]">*</span>
                    </label>
                    <input type="hidden" id="type" name="type" value="{{ old('type', 'kelas') }}" required>
                    <button type="button" onclick="toggleCustomDropdown('type')" id="typeTrigger"
                            class="w-full bg-[#0a0a0a] border border-[#333333] hover:border-[#555555] text-white text-sm rounded-[6px] px-3.5 py-2.5 flex items-center justify-between focus:outline-none focus:border-white transition font-mono">
                        <span id="typeLabel">
                            @if(old('type') == 'laboratorium') Laboratorium
                            @elseif(old('type') == 'aula') Aula
                            @elseif(old('type') == 'alat') Alat multimedia / khusus
                            @elseif(old('type') == 'lapangan') Lapangan olahraga
                            @else Ruang kelas
                            @endif
                        </span>
                        <svg id="typeChevron" class="custom-dropdown-chevron w-4 h-4 text-[#888888] transition-transform duration-200 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                    <div id="typeMenu" class="custom-dropdown-menu hidden absolute top-full left-0 right-0 mt-1.5 z-50 bg-[#111111] border border-[#333333] rounded-[6px] shadow-2xl overflow-hidden py-1">
                        <div onclick="selectCustomDropdown('type', 'kelas', 'Ruang kelas')" data-value="kelas" class="dropdown-item px-3.5 py-2.5 text-xs font-mono {{ old('type', 'kelas') == 'kelas' ? 'bg-[#1f1f1f] text-white' : 'text-[#cccccc]' }} hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between">
                            <span>Ruang kelas</span>
                            <span class="check-icon text-[#50e3c2] text-xs {{ old('type', 'kelas') == 'kelas' ? '' : 'hidden' }}">✓</span>
                        </div>
                        <div onclick="selectCustomDropdown('type', 'laboratorium', 'Laboratorium')" data-value="laboratorium" class="dropdown-item px-3.5 py-2.5 text-xs font-mono {{ old('type') == 'laboratorium' ? 'bg-[#1f1f1f] text-white' : 'text-[#cccccc]' }} hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between">
                            <span>Laboratorium</span>
                            <span class="check-icon text-[#50e3c2] text-xs {{ old('type') == 'laboratorium' ? '' : 'hidden' }}">✓</span>
                        </div>
                        <div onclick="selectCustomDropdown('type', 'aula', 'Aula')" data-value="aula" class="dropdown-item px-3.5 py-2.5 text-xs font-mono {{ old('type') == 'aula' ? 'bg-[#1f1f1f] text-white' : 'text-[#cccccc]' }} hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between">
                            <span>Aula</span>
                            <span class="check-icon text-[#50e3c2] text-xs {{ old('type') == 'aula' ? '' : 'hidden' }}">✓</span>
                        </div>
                        <div onclick="selectCustomDropdown('type', 'alat', 'Alat multimedia / khusus')" data-value="alat" class="dropdown-item px-3.5 py-2.5 text-xs font-mono {{ old('type') == 'alat' ? 'bg-[#1f1f1f] text-white' : 'text-[#cccccc]' }} hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between">
                            <span>Alat multimedia / khusus</span>
                            <span class="check-icon text-[#50e3c2] text-xs {{ old('type') == 'alat' ? '' : 'hidden' }}">✓</span>
                        </div>
                        <div onclick="selectCustomDropdown('type', 'lapangan', 'Lapangan olahraga')" data-value="lapangan" class="dropdown-item px-3.5 py-2.5 text-xs font-mono {{ old('type') == 'lapangan' ? 'bg-[#1f1f1f] text-white' : 'text-[#cccccc]' }} hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between">
                            <span>Lapangan olahraga</span>
                            <span class="check-icon text-[#50e3c2] text-xs {{ old('type') == 'lapangan' ? '' : 'hidden' }}">✓</span>
                        </div>
                    </div>
                </div>

                <!-- Capacity (Optional) -->
                <div class="space-y-1.5">
                    <label for="capacity" class="block text-xs font-medium text-[#a1a1a1]">
                        Kapasitas orang <span class="text-[#666666] font-normal">(opsional)</span>
                    </label>
                    <input 
                        type="number" 
                        id="capacity" 
                        name="capacity" 
                        min="1"
                        placeholder="Contoh: 40 (kosongkan bila alat)"
                        class="w-full bg-[#0a0a0a] border border-[#333333] text-white text-sm rounded-[6px] px-3.5 py-2.5 focus:outline-none focus:border-white transition placeholder-[#666666] font-mono"
                    />
                </div>
            </div>

            <!-- Location -->
            <div class="space-y-1.5">
                <label for="location" class="block text-xs font-medium text-[#a1a1a1]">
                    Lokasi gedung / ruang <span class="text-[#ff453a]">*</span>
                </label>
                <input 
                    type="text" 
                    id="location" 
                    name="location" 
                    required 
                    placeholder="Contoh: Gedung B Lantai 3, Sayap Barat..."
                    class="w-full bg-[#0a0a0a] border border-[#333333] text-white text-sm rounded-[6px] px-3.5 py-2.5 focus:outline-none focus:border-white transition placeholder-[#666666]"
                />
            </div>

            <!-- Description -->
            <div class="space-y-1.5">
                <label for="description" class="block text-xs font-medium text-[#a1a1a1]">
                    Deskripsi lengkap fasilitas <span class="text-[#ff453a]">*</span>
                </label>
                <textarea 
                    id="description" 
                    name="description" 
                    rows="4" 
                    required 
                    placeholder="Jelaskan fasilitas penunjang, spesifikasi peralatan, peruntukan ruangan, dan panduan penggunaan..."
                    class="w-full bg-[#0a0a0a] border border-[#333333] text-white text-sm rounded-[6px] p-3.5 focus:outline-none focus:border-white transition placeholder-[#666666]"
                ></textarea>
            </div>

            <!-- Photo Upload (Optional) -->
            <div class="space-y-1.5">
                <label for="photo" class="block text-xs font-medium text-[#a1a1a1]">
                    Foto utama fasilitas <span class="text-[#666666] font-normal">(opsional)</span>
                </label>
                <input 
                    type="file" 
                    id="photo" 
                    name="photo" 
                    accept="image/*"
                    class="w-full bg-[#161616] border border-[#333333] text-[#a1a1a1] text-xs rounded-[6px] file:mr-4 file:py-2 file:px-3 file:rounded-[6px] file:border-0 file:text-xs file:font-medium file:bg-white file:text-black hover:file:bg-[#e6e6e6] cursor-pointer"
                />
            </div>

            <!-- Notice on Initial State -->
            <div class="p-3.5 rounded-[6px] bg-[#002b11]/40 border border-[#005522] text-xs text-[#50e3c2]">
                <strong class="font-semibold text-[#50e3c2] block mb-0.5">Ketentuan status otomatis:</strong>
                Fasilitas baru otomatis berstatus data master <span class="font-medium text-white">Aktif</span> dan status operasional <span class="font-medium text-white">Dapat Digunakan</span> sehingga langsung tersedia di katalog publik.
            </div>

            <div class="pt-4 border-t border-[#333333] flex items-center justify-end gap-3">
                <a href="{{ route('admin.facilities.index') }}" class="bg-[#171717] hover:bg-[#222222] text-white border border-[#333333] font-medium text-xs px-4 py-2 rounded-[6px] transition">
                    Batal
                </a>
                <button 
                    type="submit" 
                    class="bg-white hover:bg-[#e6e6e6] text-black font-medium text-xs px-4 py-2 rounded-[6px] shadow-none transition flex items-center gap-2 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Simpan fasilitas baru
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
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
    }

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.custom-dropdown')) {
            document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.add('hidden'));
            document.querySelectorAll('.custom-dropdown-chevron').forEach(c => c.classList.remove('rotate-180'));
        }
    });
</script>
@endpush
