@php
    $facilityId = is_array($facility) ? $facility['id'] : $facility->id;
    $facilityName = is_array($facility) ? $facility['name'] : $facility->name;
    $facilityType = is_array($facility) ? ($facility['type'] ?? '-') : (is_object($facility->type ?? null) && method_exists($facility->type, 'label') ? $facility->type->label() : ($facility->type ?? '-'));
    $facilityLocation = is_array($facility) ? ($facility['location'] ?? '-') : ($facility->location ?? '-');
@endphp

<!-- Modal Konfirmasi Nonaktifkan Fasilitas oleh Admin -->
<div id="deactivateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75 backdrop-blur-sm hidden" role="dialog" aria-modal="true" aria-labelledby="modal-title-deactivate">
    <div class="bg-[#111111] border border-[#333333] rounded-[8px] max-w-lg w-full p-6 shadow-2xl relative text-left">
        
        <div class="flex items-start justify-between pb-4 border-b border-[#222222]">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-[6px] bg-[#2b0000] text-[#ff453a] flex items-center justify-center border border-[#550000]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-white tracking-tight" id="modal-title-deactivate">Nonaktifkan Data Master Fasilitas</h3>
                    <p class="text-xs text-[#888888]">Fasilitas tidak akan tampil pada katalog publik</p>
                </div>
            </div>
            <button type="button" onclick="closeDeactivateModal()" class="close-deactivate-modal text-[#888888] hover:text-white p-1 rounded-[6px] transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.facilities.deactivate', is_array($facility) ? $facility['id'] : $facility) }}" class="space-y-4 pt-4">
            @csrf
            
            <!-- Warning Notice (BR-ADM-02) -->
            <div class="p-3.5 rounded-[6px] bg-[#2b1e00]/50 border border-[#553c00] text-xs text-[#f5a623] space-y-1.5 leading-relaxed">
                <strong class="font-semibold text-[#f5a623] block font-mono text-[11px] uppercase tracking-wide">Pemberitahuan Sistem (BR-ADM-02):</strong>
                <p>• Tindakan ini hanya diizinkan jika <strong class="text-white">tidak ada reservasi Disetujui atau Berlangsung</strong> pada fasilitas ini.</p>
                <p>• Seluruh reservasi yang masih berstatus <em>Menunggu</em> otomatis <strong class="text-white">Ditolak</strong> oleh sistem.</p>
                <p>• Fasilitas akan disembunyikan dari katalog publik Pengunjung dan Pengguna.</p>
            </div>

            <div class="rounded-[6px] bg-[#0a0a0a] border border-[#222222] p-3 text-xs text-[#a1a1a1]">
                <p><strong class="text-white">Fasilitas:</strong> {{ $facilityName }}</p>
                <p class="text-[#666666] text-[11px] mt-0.5">{{ $facilityType }} — {{ $facilityLocation }}</p>
            </div>

            <div>
                <label for="deactivate_reason" class="block text-xs font-medium text-[#a1a1a1] uppercase tracking-wider mb-1.5">
                    Alasan Penonaktifan <span class="text-rose-500">*</span>
                </label>
                <textarea
                    id="deactivate_reason"
                    name="reason"
                    rows="3"
                    required
                    minlength="5"
                    placeholder="Contoh: Renovasi gedung jangka panjang / inventaris dipindahkan permanen..."
                    class="w-full bg-[#0a0a0a] border border-[#333333] rounded-[6px] px-3.5 py-2 text-xs text-white placeholder-[#555555] outline-none transition focus:border-white focus:ring-1 focus:ring-white"
                >{{ old('reason') }}</textarea>
                @error('reason')
                    <p class="mt-1 text-xs text-rose-400 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-3 flex items-center justify-end gap-2 border-t border-[#222222]">
                <button 
                    type="button" 
                    onclick="closeDeactivateModal()" 
                    class="close-deactivate-modal bg-[#171717] hover:bg-[#222222] text-white border border-[#333333] font-medium text-xs px-3.5 py-1.5 rounded-[6px] transition">
                    Batal
                </button>
                <button 
                    type="submit" 
                    class="bg-[#ee0000] hover:bg-[#c50000] text-white font-medium text-xs px-4 py-1.5 rounded-[6px] transition shadow-none">
                    Konfirmasi Nonaktifkan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openDeactivateModal() {
        const modal = document.getElementById('deactivateModal') || document.getElementById('deactivateFacilityModal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
    }

    function closeDeactivateModal() {
        const modal = document.getElementById('deactivateModal') || document.getElementById('deactivateFacilityModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('deactivateModal') || document.getElementById('deactivateFacilityModal');
        const openBtn = document.getElementById('openDeactivateModalBtn');
        const closeBtns = document.querySelectorAll('.close-deactivate-modal');

        if (openBtn) {
            openBtn.addEventListener('click', openDeactivateModal);
        }

        closeBtns.forEach(btn => {
            btn.addEventListener('click', closeDeactivateModal);
        });

        if (modal) {
            modal.addEventListener('click', function (e) {
                if (e.target === modal) {
                    closeDeactivateModal();
                }
            });
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
                closeDeactivateModal();
            }
        });

        @if ($errors->has('reason'))
            openDeactivateModal();
        @endif
    });
</script>
