<!-- Modal Penolakan Reservasi oleh Petugas -->
<div id="rejectModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm hidden">
    <div class="bg-[#111111] border border-[#333333] rounded-[6px] max-w-lg w-full p-6 shadow-none relative animate-in fade-in zoom-in-95 duration-200">
        
        <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-[6px] bg-[#2b0000] text-[#ff453a] flex items-center justify-center border border-[#550000]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-white tracking-tight">Tolak Pengajuan Reservasi</h3>
                    <p class="text-xs text-[#888888]">Penolakan bersifat final dan tercatat dalam sistem audit.</p>
                </div>
            </div>
            <button type="button" onclick="closeRejectModal()" class="text-[#888888] hover:text-white p-1 rounded-[6px]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('staff.reservations.reject', ['reservation' => $reservation['id']]) }}" class="space-y-4">
            @csrf
            
            <div class="p-3.5 rounded-[6px] bg-[#161616] border border-[#333333] text-xs text-[#a1a1a1] space-y-1 font-mono">
                <p>Nomor: <strong class="text-white">{{ $reservation['reservation_number'] }}</strong></p>
                <p>Pemohon: <strong class="text-white font-sans">{{ $reservation['applicant_name'] }}</strong></p>
                <p>Fasilitas: <strong class="text-white font-sans">{{ $reservation['facility_name'] }}</strong></p>
            </div>

            <!-- Mandatory Rejection Reason -->
            <div class="space-y-1.5">
                <label for="rejection_reason" class="block font-mono text-[11px] uppercase tracking-wider text-[#888888] font-medium">
                    Alasan Penolakan <span class="text-[#ff453a]">*</span>
                </label>
                <textarea 
                    id="rejection_reason" 
                    name="rejection_reason" 
                    rows="3" 
                    required 
                    placeholder="Tuliskan alasan penolakan secara jelas (misal: Kegiatan bentrok dengan agenda rektorat, fasilitas memerlukan sterilisasi, dll)..."
                    class="w-full bg-[#0a0a0a] border border-[#333333] text-white text-xs rounded-[6px] p-3 focus:outline-none focus:border-[#ff453a] transition-all placeholder-[#666666]"
                ></textarea>
            </div>

            <div class="pt-3 flex items-center justify-end gap-2 border-t border-[#333333]">
                <button 
                    type="button" 
                    onclick="closeRejectModal()" 
                    class="bg-[#171717] hover:bg-[#222222] text-white border border-[#333333] font-medium text-xs px-3.5 py-1.5 rounded-[6px] transition">
                    Batal
                </button>
                <button 
                    type="submit" 
                    class="bg-[#ee0000] hover:bg-[#c50000] text-white font-medium text-xs px-4 py-1.5 rounded-[6px] transition shadow-none">
                    Konfirmasi Penolakan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openRejectModal() {
        document.getElementById('rejectModal').classList.remove('hidden');
    }
    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
    }
</script>
