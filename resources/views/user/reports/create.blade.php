@extends('layouts.app')

@section('title', 'Buat Laporan Kerusakan / Masalah Fasilitas')

@section('content')
<div class="max-w-[1200px] mx-auto px-4 sm:px-6 py-8">

    <!-- Breadcrumb -->
    <div class="mb-6">
        <a href="{{ route('user.reports.index', ['as_user' => 1]) }}" 
           class="inline-flex items-center gap-1.5 text-xs font-medium text-[#a1a1a1] hover:text-white transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Riwayat Laporan
        </a>
    </div>

    <!-- Header Section -->
    <div class="mb-8">
        <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-[6px] text-xs font-mono text-[#ff453a] bg-[#2b0000] border border-[#550000] mb-3">
            <span class="w-1.5 h-1.5 rounded-full bg-[#ff453a]"></span>
            <span>Pelaporan Sarana & Prasarana</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-semibold text-white tracking-[-0.03em]">
            Formulir laporan kendala fasilitas.
        </h1>
        <p class="mt-1 text-xs sm:text-sm text-[#a1a1a1]">
            Sampaikan kendala teknis, kerusakan fisik, atau kebersihan pada fasilitas kampus agar segera ditindaklanjuti oleh petugas sarpras.
        </p>
    </div>

    <!-- Report Form Card: Crisp border, micro 6px radius, NO soft shadows -->
    <div class="bg-[#111111] border border-[#333333] rounded-[6px] p-6 sm:p-8 space-y-6">
        @if ($errors->any())
            <div class="p-4 rounded-[6px] border border-[#550000] bg-[#2b0000] text-xs text-[#ff453a] space-y-1">
                <strong class="font-semibold text-white block">Gagal Mengirim Laporan:</strong>
                <ul class="list-disc list-inside space-y-1 text-[#ededed]">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('user.reports.store', ['as_user' => 1]) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Facility Target -->
            @php
                $preselectedFacilityId = old('facility_id', request('facility_id', ''));
                $preselectedFacility = collect($facilities)->firstWhere('id', $preselectedFacilityId);
                $preType = $preselectedFacility ? (is_object($preselectedFacility['type']) && property_exists($preselectedFacility['type'], 'value') ? $preselectedFacility['type']->value : (string)$preselectedFacility['type']) : '';
                $facilityLabel = $preselectedFacility ? ($preselectedFacility['name'] . ' (' . ucfirst($preType) . ' — ' . $preselectedFacility['location'] . ')') : 'Pilih Fasilitas Kampus...';
            @endphp
            <div class="space-y-1.5 relative custom-dropdown" id="dropdown-facility_id">
                <label class="block font-mono text-[10px] uppercase tracking-wider text-[#888888] font-medium">
                    Fasilitas yang Dilaporkan <span class="text-[#ff453a]">*</span>
                </label>
                <input type="hidden" id="facility_id" name="facility_id" value="{{ $preselectedFacilityId }}" required>
                <button type="button" onclick="toggleCustomDropdown('facility_id')" id="facility_idTrigger" title="{{ $facilityLabel }}"
                        class="w-full bg-[#0a0a0a] border border-[#333333] hover:border-[#555555] text-white text-xs rounded-[6px] px-3 py-2.5 flex items-center justify-between min-w-0 gap-2 focus:outline-none focus:border-white transition font-mono">
                    <span id="facility_idLabel" class="truncate text-left flex-1" title="{{ $facilityLabel }}">{{ $facilityLabel }}</span>
                    <svg id="facility_idChevron" class="custom-dropdown-chevron w-3.5 h-3.5 text-[#888888] transition-transform duration-200 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
                <div id="facility_idMenu" class="custom-dropdown-menu hidden absolute top-full left-0 right-0 mt-1.5 z-50 bg-[#111111] border border-[#333333] rounded-[6px] shadow-2xl overflow-y-auto max-h-60 py-1">
                    <div onclick="selectCustomDropdown('facility_id', '', 'Pilih Fasilitas Kampus...')" data-value="" class="dropdown-item px-3 py-2 text-xs font-mono {{ empty($preselectedFacilityId) ? 'bg-[#1f1f1f] text-white' : 'text-[#888888]' }} hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between">
                        <span>Pilih Fasilitas Kampus...</span>
                        <span class="check-icon text-[#50e3c2] text-xs {{ empty($preselectedFacilityId) ? '' : 'hidden' }}">✓</span>
                    </div>
                    @foreach($facilities as $f)
                        @php
                            $fType = is_object($f['type']) && property_exists($f['type'], 'value') ? $f['type']->value : (string)$f['type'];
                            $optLabel = $f['name'] . ' (' . ucfirst($fType) . ' — ' . $f['location'] . ')';
                            $isSelected = ($preselectedFacilityId == $f['id']);
                        @endphp
                        <div onclick="selectCustomDropdown('facility_id', '{{ $f['id'] }}', '{{ addslashes($optLabel) }}')" data-value="{{ $f['id'] }}" class="dropdown-item px-3 py-2 text-xs font-mono {{ $isSelected ? 'bg-[#1f1f1f] text-white' : 'text-[#cccccc]' }} hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between">
                            <span>{{ $optLabel }}</span>
                            <span class="check-icon text-[#50e3c2] text-xs {{ $isSelected ? '' : 'hidden' }}">✓</span>
                        </div>
                    @endforeach
                </div>
                <span class="text-[11px] text-[#888888] font-mono">Dapat dilaporkan tanpa harus sedang meminjam fasilitas tersebut.</span>
            </div>

            <!-- Issue Category -->
            @php
                $preselectedCat = old('category', 'kerusakan_fisik');
                $categories = [
                    'kerusakan_fisik' => 'Kerusakan Fisik (Pintu, Jendela, Kursi, Meja, Tembok)',
                    'kerusakan_elektronik' => 'Kerusakan Elektronik (AC, Proyektor, Lampu, PC, Stopkontak)',
                    'kebersihan' => 'Kebersihan Ruangan & Sanitasi',
                    'keamanan' => 'Keamanan / Kehilangan Fasilitas',
                    'lainnya' => 'Lainnya (Tuliskan detail pada deskripsi)'
                ];
                $initCategoryLabel = $categories[$preselectedCat] ?? 'Pilih Kategori';
            @endphp
            <div class="space-y-1.5 relative custom-dropdown" id="dropdown-category">
                <label class="block font-mono text-[10px] uppercase tracking-wider text-[#888888] font-medium">
                    Kategori Masalah <span class="text-[#ff453a]">*</span>
                </label>
                <input type="hidden" id="category" name="category" value="{{ $preselectedCat }}" required>
                <button type="button" onclick="toggleCustomDropdown('category')" id="categoryTrigger" title="{{ $initCategoryLabel }}"
                        class="w-full bg-[#0a0a0a] border border-[#333333] hover:border-[#555555] text-white text-xs rounded-[6px] px-3 py-2.5 flex items-center justify-between min-w-0 gap-2 focus:outline-none focus:border-white transition font-mono">
                    <span id="categoryLabel" class="truncate text-left flex-1" title="{{ $initCategoryLabel }}">{{ $initCategoryLabel }}</span>
                    <svg id="categoryChevron" class="custom-dropdown-chevron w-3.5 h-3.5 text-[#888888] transition-transform duration-200 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
                <div id="categoryMenu" class="custom-dropdown-menu hidden absolute top-full left-0 right-0 mt-1.5 z-50 bg-[#111111] border border-[#333333] rounded-[6px] shadow-2xl overflow-hidden py-1">
                    @foreach($categories as $cKey => $cLabel)
                        @php $isCatSelected = ($preselectedCat === $cKey); @endphp
                        <div onclick="selectCustomDropdown('category', '{{ $cKey }}', '{{ addslashes($cLabel) }}')" data-value="{{ $cKey }}" class="dropdown-item px-3 py-2 text-xs font-mono {{ $isCatSelected ? 'bg-[#1f1f1f] text-white' : 'text-[#cccccc]' }} hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between">
                            <span>{{ $cLabel }}</span>
                            <span class="check-icon text-[#50e3c2] text-xs {{ $isCatSelected ? '' : 'hidden' }}">✓</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Detailed Description -->
            <div class="space-y-1.5">
                <label for="description" class="block font-mono text-[10px] uppercase tracking-wider text-[#888888] font-medium">
                    Deskripsi Masalah <span class="text-[#ff453a]">*</span>
                </label>
                <textarea 
                    id="description" 
                    name="description" 
                    rows="4" 
                    required 
                    placeholder="Jelaskan titik kerusakan, gejala masalah, atau kondisi fasilitas saat ditemukan secara terperinci..."
                    class="w-full bg-[#0a0a0a] border border-[#333333] text-white text-xs rounded-[6px] p-3 focus:outline-none focus:border-white transition placeholder-[#666666]"
                ></textarea>
            </div>

            <!-- Photo Upload (Optional) -->
            <div class="space-y-1.5" id="photo-upload-wrapper">
                <label for="photo" class="block font-mono text-[10px] uppercase tracking-wider text-[#888888] font-medium">
                    Foto Bukti Kerusakan (Opsional)
                </label>
                <input 
                    type="file" 
                    id="photo" 
                    name="photo" 
                    accept="image/*"
                    class="w-full bg-[#161616] border border-[#333333] text-[#a1a1a1] text-xs rounded-[6px] file:mr-4 file:py-2 file:px-3 file:rounded-[4px] file:border-0 file:text-xs file:font-medium file:bg-white file:text-black hover:file:bg-[#e6e6e6] cursor-pointer"
                />
                <!-- Preview -->
                <div id="photo-preview-wrap" class="hidden mt-2">
                    <p class="text-[10px] text-[#888888] font-mono mb-1.5">Preview (setelah diproses · 4:3):</p>
                    <img id="photo-preview" src="" alt="Preview"
                         class="w-full max-w-xs rounded-[4px] border border-[#333333] object-cover bg-[#0a0a0a]" style="aspect-ratio:4/3" />
                    <p id="photo-info" class="text-[10px] text-[#666666] font-mono mt-1"></p>
                </div>
            </div>

            <div class="pt-4 border-t border-[#333333] flex items-center justify-end gap-3">
                <a href="{{ route('user.reports.index', ['as_user' => 1]) }}" class="bg-[#171717] hover:bg-[#222222] text-[#a1a1a1] hover:text-white border border-[#333333] font-medium text-xs px-4 py-2 rounded-full transition">
                    Batal
                </a>
                <button 
                    type="submit" 
                    class="bg-white hover:bg-[#e6e6e6] text-black font-medium text-xs px-5 py-2.5 rounded-full transition flex items-center gap-2 active:scale-95 shadow-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                    Kirim Laporan Kerusakan
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
        const input = document.getElementById(name);
        const labelEl = document.getElementById(name + 'Label');
        const trigger = document.getElementById(name + 'Trigger');

        if (input) input.value = value;
        if (labelEl) {
            labelEl.textContent = label;
            labelEl.title = label;
        }
        if (trigger) trigger.title = label;
        
        const menu = document.getElementById(name + 'Menu');
        if (menu) {
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
        }

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
<script type="module">
    import { preprocessImage, attachImagePreprocessor } from '/js/image-upload.js';

    const form        = document.querySelector('form[enctype="multipart/form-data"]');
    const fileInput   = document.getElementById('photo');
    const previewWrap = document.getElementById('photo-preview-wrap');
    const previewImg  = document.getElementById('photo-preview');
    const previewInfo = document.getElementById('photo-info');

    const OPTS = {
        aspectRatio: 4 / 3,
        maxWidth: 1280,
        maxHeight: 960,
        quality: 0.82,
    };

    fileInput?.addEventListener('change', async () => {
        if (!fileInput.files?.length) {
            previewWrap.classList.add('hidden');
            return;
        }
        try {
            const processed = await preprocessImage(fileInput.files[0], OPTS);
            previewImg.src = URL.createObjectURL(processed);
            const kb = (processed.size / 1024).toFixed(1);
            previewInfo.textContent = `${processed.name} · ${kb} KB`;
            previewWrap.classList.remove('hidden');
        } catch (err) {
            previewWrap.classList.add('hidden');
            console.warn('[image-upload] Preview failed:', err?.message);
        }
    });

    attachImagePreprocessor(form, fileInput, OPTS);
</script>
@endpush
