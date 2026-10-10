<!-- Modal Perubahan Status Operasional Fasilitas oleh Petugas -->
<div id="facilityStatusModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm hidden">
    <div class="bg-[#111111] border border-[#333333] rounded-[6px] max-w-lg w-full p-6 shadow-none relative animate-in fade-in zoom-in-95 duration-200">
        
        <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-[6px] bg-[#161616] text-white flex items-center justify-center border border-[#333333]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-white tracking-tight">Ubah Status Operasional Fasilitas</h3>
                    <p class="text-xs text-[#888888]">Pembaruan ketersediaan operasional sarana & prasarana.</p>
                </div>
            </div>
            <button type="button" onclick="closeFacilityStatusModal()" class="text-[#888888] hover:text-white p-1 rounded-[6px]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        @php
            $modalFacility = $facility ?? ($report->facility ?? null);
            $modalFacilityId = $modalFacility?->id ?? ($report['facility_id'] ?? '1');
            $modalFacilityName = $modalFacility?->name ?? ($report['facility_name'] ?? 'Fasilitas Terpilih');
            $currentOpStatusVal = ($modalFacility?->operational_status instanceof \BackedEnum) 
                ? $modalFacility->operational_status->value 
                : (string)($modalFacility?->operational_status ?? 'dapat_digunakan');
            $initOpStatus = ($currentOpStatusVal === 'dalam_perbaikan') ? 'dapat_digunakan' : 'dalam_perbaikan';
        @endphp

        <form method="POST" action="{{ route('staff.facilities.update-status', ['facility' => $modalFacilityId]) }}" class="space-y-4">
            @csrf
            @if(isset($report))
                <input type="hidden" name="report_id" value="{{ $report->id ?? $report['id'] }}">
            @endif
            
            <div class="p-3.5 rounded-[6px] bg-[#161616] border border-[#333333] text-xs text-[#a1a1a1] space-y-1 font-mono">
                <p>Fasilitas: <strong class="text-white font-sans">{{ $modalFacilityName }}</strong></p>
                <p>Status Saat Ini: 
                    <span class="font-medium {{ $currentOpStatusVal === 'dapat_digunakan' ? 'text-[#50e3c2]' : 'text-[#ff453a]' }}">
                        {{ $currentOpStatusVal === 'dapat_digunakan' ? 'Dapat Digunakan' : 'Dalam Perbaikan' }}
                    </span>
                </p>
            </div>

            <!-- Target Status Selection -->
            <div class="space-y-1.5 relative custom-dropdown" id="dropdown-operational_status">
                <label class="block font-mono text-[11px] uppercase tracking-wider text-[#888888] font-medium">Status Operasional Baru</label>
                <input type="hidden" id="operational_status" name="operational_status" value="{{ $initOpStatus }}">
                @php
                    $initOpStatusLabel = $initOpStatus === 'dalam_perbaikan' ? 'Dalam Perbaikan (Nonaktifkan Reservasi)' : 'Dapat Digunakan (Tersedia Kembali)';
                @endphp
                <button type="button" onclick="toggleCustomDropdown('operational_status')" id="operational_statusTrigger" title="{{ $initOpStatusLabel }}"
                        class="w-full bg-[#0a0a0a] border border-[#333333] hover:border-[#555555] text-white text-xs rounded-[6px] px-3 py-2 flex items-center justify-between min-w-0 gap-2 focus:outline-none focus:border-white transition font-mono">
                    <span id="operational_statusLabel" class="truncate text-left flex-1" title="{{ $initOpStatusLabel }}">{{ $initOpStatusLabel }}</span>
                    <svg id="operational_statusChevron" class="custom-dropdown-chevron w-3.5 h-3.5 text-[#888888] transition-transform duration-200 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
                <div id="operational_statusMenu" class="custom-dropdown-menu hidden absolute top-full left-0 right-0 mt-1.5 z-50 bg-[#111111] border border-[#333333] rounded-[6px] shadow-2xl overflow-hidden py-1">
                    <div onclick="selectCustomDropdown('operational_status', 'dalam_perbaikan', 'Dalam Perbaikan (Nonaktifkan Reservasi)')" data-value="dalam_perbaikan" class="dropdown-item px-3 py-2 text-xs font-mono {{ $initOpStatus === 'dalam_perbaikan' ? 'bg-[#1f1f1f] text-white' : 'text-[#cccccc]' }} hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between">
                        <span>Dalam Perbaikan (Nonaktifkan Reservasi)</span>
                        <span class="check-icon text-[#50e3c2] text-xs {{ $initOpStatus === 'dalam_perbaikan' ? '' : 'hidden' }}">✓</span>
                    </div>
                    <div onclick="selectCustomDropdown('operational_status', 'dapat_digunakan', 'Dapat Digunakan (Tersedia Kembali)')" data-value="dapat_digunakan" class="dropdown-item px-3 py-2 text-xs font-mono {{ $initOpStatus === 'dapat_digunakan' ? 'bg-[#1f1f1f] text-white' : 'text-[#cccccc]' }} hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between">
                        <span>Dapat Digunakan (Tersedia Kembali)</span>
                        <span class="check-icon text-[#50e3c2] text-xs {{ $initOpStatus === 'dapat_digunakan' ? '' : 'hidden' }}">✓</span>
                    </div>
                </div>
            </div>

            <!-- Mandatory Reason or Maintenance Note -->
            <div class="space-y-1.5">
                <label for="status_reason" class="block font-mono text-[11px] uppercase tracking-wider text-[#888888] font-medium">
                    Alasan Perubahan / Catatan Pemeriksaan <span class="text-[#0070f3]">*</span>
                </label>
                <textarea 
                    id="status_reason" 
                    name="reason" 
                    rows="3" 
                    required 
                    placeholder="Contoh: Dilakukan isolasi ruangan perbaikan AC sentral / atau Perbaikan instalasi kelistrikan telah selesai diuji dan siap pakai..."
                    class="w-full bg-[#0a0a0a] border border-[#333333] text-white text-xs rounded-[6px] p-3 focus:outline-none focus:border-white transition-all placeholder-[#666666]"
                ></textarea>
                <span class="text-[11px] text-[#888888] font-mono">
                    Jika ditandai <em>Dalam perbaikan</em>, reservasi disetujui mendatang akan otomatis dibatalkan sistem.
                </span>
            </div>

            <div class="pt-3 flex items-center justify-end gap-2 border-t border-[#333333]">
                <button 
                    type="button" 
                    onclick="closeFacilityStatusModal()" 
                    class="bg-[#171717] hover:bg-[#222222] text-white border border-[#333333] font-medium text-xs px-3.5 py-1.5 rounded-[6px] transition">
                    Batal
                </button>
                <button 
                    type="submit" 
                    class="bg-white hover:bg-[#e6e6e6] text-black font-medium text-xs px-4 py-1.5 rounded-[6px] transition shadow-none">
                    Simpan Perubahan Status
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openFacilityStatusModal() {
        document.getElementById('facilityStatusModal').classList.remove('hidden');
    }
    function closeFacilityStatusModal() {
        document.getElementById('facilityStatusModal').classList.add('hidden');
    }
    if (typeof window.toggleCustomDropdown !== 'function') {
        window.toggleCustomDropdown = function(name) {
            const menu = document.getElementById(name + 'Menu');
            const chevron = document.getElementById(name + 'Chevron');
            const isOpen = !menu.classList.contains('hidden');

            document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.add('hidden'));
            document.querySelectorAll('.custom-dropdown-chevron').forEach(c => c.classList.remove('rotate-180'));

            if (!isOpen) {
                menu.classList.remove('hidden');
                if (chevron) chevron.classList.add('rotate-180');
            }
        };
    }
    if (typeof window.selectCustomDropdown !== 'function') {
        window.selectCustomDropdown = function(name, value, label) {
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
        };
    }
</script>
