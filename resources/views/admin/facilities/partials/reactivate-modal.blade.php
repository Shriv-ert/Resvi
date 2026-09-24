<!-- Modal Konfirmasi Aktifkan Kembali Fasilitas oleh Admin (SRS-ADMIN.md §3 FR-ADM-09) -->
<div id="reactivateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm hidden">
    <div class="bg-[#111111] border border-[#333333] rounded-[6px] max-w-lg w-full p-6 shadow-none relative">
        
        <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-[6px] bg-[#002b11] text-[#50e3c2] flex items-center justify-center border border-[#005522]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-white tracking-tight">Aktifkan kembali data master.</h3>
                    <p class="text-xs text-[#a1a1a1]">Fasilitas akan kembali tampil pada katalog publik.</p>
                </div>
            </div>
            <button type="button" onclick="closeReactivateModal()" class="text-[#888888] hover:text-white p-1 rounded-[6px]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.facilities.reactivate', ['facility' => $facility['id']]) }}" class="space-y-4">
            @csrf
            
            <div class="p-3.5 rounded-[6px] bg-[#0a0a0a] border border-[#333333] text-xs text-[#a1a1a1] space-y-2 font-mono">
                <p>Fasilitas: <strong class="text-white font-sans">{{ $facility['name'] }}</strong></p>
                <p class="text-[#888888] text-[11px] leading-relaxed">
                    Pengaktifan data master tidak otomatis mengubah status operasional fisik. Jika fasilitas sebelumnya berstatus <em>Dalam perbaikan</em>, Petugas tetap perlu memeriksa dan mengembalikannya ke <em>Dapat digunakan</em>.
                </p>
            </div>

            <div class="pt-3 flex items-center justify-end gap-2 border-t border-[#333333]">
                <button 
                    type="button" 
                    onclick="closeReactivateModal()" 
                    class="bg-[#171717] hover:bg-[#222222] text-white border border-[#333333] font-medium text-xs px-3.5 py-1.5 rounded-[6px] transition">
                    Batal
                </button>
                <button 
                    type="submit" 
                    class="bg-emerald-600 hover:bg-emerald-500 text-white font-medium text-xs px-4 py-1.5 rounded-[6px] transition shadow-none">
                    Konfirmasi aktifkan kembali
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openReactivateModal() { document.getElementById('reactivateModal').classList.remove('hidden'); }
    function closeReactivateModal() { document.getElementById('reactivateModal').classList.add('hidden'); }
</script>
