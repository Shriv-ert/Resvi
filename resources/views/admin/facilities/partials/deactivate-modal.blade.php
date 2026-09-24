<!-- Modal Konfirmasi Nonaktifkan Fasilitas oleh Admin -->
<div id="deactivateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm hidden">
    <div class="bg-[#111111] border border-[#333333] rounded-[6px] max-w-lg w-full p-6 shadow-none relative">
        
        <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-[6px] bg-[#2b0000] text-[#ff453a] flex items-center justify-center border border-[#550000]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-white tracking-tight">Nonaktifkan data master fasilitas.</h3>
                    <p class="text-xs text-[#888888]">Fasilitas tidak akan tampil pada daftar katalog publik.</p>
                </div>
            </div>
            <button type="button" onclick="closeDeactivateModal()" class="text-[#888888] hover:text-white p-1 rounded-[6px]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.facilities.deactivate', ['facility' => $facility['id']]) }}" class="space-y-4">
            @csrf
            
            <div class="p-3.5 rounded-[6px] bg-[#2b1e00]/50 border border-[#553c00] text-xs text-[#f5a623] space-y-2">
                <strong class="font-semibold text-[#f5a623] block font-mono">Pemberitahuan sistem (BR-ADM-02):</strong>
                <p>• Tindakan ini hanya diizinkan jika <strong class="text-white">tidak ada reservasi Disetujui atau Berlangsung</strong> pada fasilitas ini.</p>
                <p>• Jika penonaktifan berhasil, seluruh permohonan yang masih <em>Menunggu</em> otomatis berstatus <em>Ditolak</em> dengan alasan penonaktifan data master.</p>
            </div>

            <div class="pt-3 flex items-center justify-end gap-2 border-t border-[#333333]">
                <button 
                    type="button" 
                    onclick="closeDeactivateModal()" 
                    class="bg-[#171717] hover:bg-[#222222] text-white border border-[#333333] font-medium text-xs px-3.5 py-1.5 rounded-[6px] transition">
                    Batal
                </button>
                <button 
                    type="submit" 
                    class="bg-[#ee0000] hover:bg-[#c50000] text-white font-medium text-xs px-4 py-1.5 rounded-[6px] transition shadow-none">
                    Konfirmasi nonaktifkan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openDeactivateModal() { document.getElementById('deactivateModal').classList.remove('hidden'); }
    function closeDeactivateModal() { document.getElementById('deactivateModal').classList.add('hidden'); }
</script>
