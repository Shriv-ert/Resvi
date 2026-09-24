@php
    $facilityId = is_array($facility) ? $facility['id'] : $facility->id;
    $facilityName = is_array($facility) ? $facility['name'] : $facility->name;
    $facilityType = is_array($facility) ? ($facility['type'] ?? '-') : (is_object($facility->type ?? null) && method_exists($facility->type, 'label') ? $facility->type->label() : ($facility->type ?? '-'));
    $facilityLocation = is_array($facility) ? ($facility['location'] ?? '-') : ($facility->location ?? '-');
    $opStatus = is_array($facility) 
        ? ($facility['operational_status'] ?? '') 
        : ($facility->operational_status?->value ?? ($facility->operational_status ?? ''));
@endphp

<!-- Modal Konfirmasi Aktifkan Kembali Fasilitas oleh Admin -->
<div id="reactivateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75 backdrop-blur-sm hidden" role="dialog" aria-modal="true" aria-labelledby="modal-title-reactivate">
    <div class="bg-[#111111] border border-[#333333] rounded-[8px] max-w-lg w-full p-6 shadow-2xl relative text-left">
        
        <div class="flex items-start justify-between pb-4 border-b border-[#222222]">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-[6px] bg-[#002b11] text-[#50e3c2] flex items-center justify-center border border-[#005522]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-white tracking-tight" id="modal-title-reactivate">Aktifkan Kembali Data Master</h3>
                    <p class="text-xs text-[#a1a1a1]">Fasilitas akan kembali tampil pada katalog publik</p>
                </div>
            </div>
            <button type="button" onclick="closeReactivateModal()" class="close-reactivate-modal text-[#888888] hover:text-white p-1 rounded-[6px] transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.facilities.reactivate', is_array($facility) ? $facility['id'] : $facility) }}" class="space-y-4 pt-4">
            @csrf
            
            <div class="p-3.5 rounded-[6px] bg-[#0a0a0a] border border-[#333333] text-xs text-[#a1a1a1] space-y-2">
                <p><strong class="text-white">Fasilitas:</strong> {{ $facilityName }}</p>
                <p class="text-[#666666] text-[11px]">{{ $facilityType }} — {{ $facilityLocation }}</p>
                <div class="pt-2 border-t border-[#222222] text-[11px] leading-relaxed">
                    @if ($opStatus === 'dalam_perbaikan')
                        <p class="text-amber-400 font-medium">
                            ⚠ Fasilitas ini saat ini berstatus operasional <strong>Dalam Perbaikan</strong>. Setelah diaktifkan kembali, fasilitas tetap belum dapat direservasi hingga Petugas mengubah status operasionalnya menjadi "Dapat Digunakan".
                        </p>
                    @else
                        <p class="text-[#888888]">
                            Pengaktifan kembali data master ini tidak mengubah status operasional fisik fasilitas. Fasilitas berstatus operasional <strong class="text-white">Dapat digunakan</strong> dan akan langsung siap menerima reservasi baru.
                        </p>
                    @endif
                </div>
            </div>

            <div class="pt-3 flex items-center justify-end gap-2 border-t border-[#222222]">
                <button 
                    type="button" 
                    onclick="closeReactivateModal()" 
                    class="close-reactivate-modal bg-[#171717] hover:bg-[#222222] text-white border border-[#333333] font-medium text-xs px-3.5 py-1.5 rounded-[6px] transition">
                    Batal
                </button>
                <button 
                    type="submit" 
                    class="bg-emerald-600 hover:bg-emerald-500 text-white font-medium text-xs px-4 py-1.5 rounded-[6px] transition shadow-none">
                    Konfirmasi Aktifkan Kembali
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openReactivateModal() {
        const modal = document.getElementById('reactivateModal') || document.getElementById('reactivateFacilityModal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
    }

    function closeReactivateModal() {
        const modal = document.getElementById('reactivateModal') || document.getElementById('reactivateFacilityModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('reactivateModal') || document.getElementById('reactivateFacilityModal');
        const openBtn = document.getElementById('openReactivateModalBtn');
        const closeBtns = document.querySelectorAll('.close-reactivate-modal');

        if (openBtn) {
            openBtn.addEventListener('click', openReactivateModal);
        }

        closeBtns.forEach(btn => {
            btn.addEventListener('click', closeReactivateModal);
        });

        if (modal) {
            modal.addEventListener('click', function (e) {
                if (e.target === modal) {
                    closeReactivateModal();
                }
            });
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
                closeReactivateModal();
            }
        });
    });
</script>
