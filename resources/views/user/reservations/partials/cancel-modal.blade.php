<!-- Modal Pembatalan Reservasi Pengguna -->
<div id="cancelModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm hidden">
    <div class="bg-[#111111] border border-[#333333] rounded-[6px] max-w-lg w-full p-6 relative text-[#ededed]">
        
        <!-- Header -->
        <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-[4px] bg-[#2b0000] text-[#ff453a] flex items-center justify-center border border-[#550000]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-white tracking-tight">Konfirmasi Pembatalan Reservasi.</h3>
                    <p class="text-xs text-[#888888]">Tindakan ini bersifat permanen dan membebaskan jadwal fasilitas.</p>
                </div>
            </div>
            <button type="button" onclick="closeCancelModal()" class="text-[#888888] hover:text-white p-1 rounded-[4px]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form id="cancelForm" method="POST" action="{{ route('user.reservations.cancel', ['reservation' => $reservation['id'], 'as_user' => 1]) }}" class="space-y-4">
            @csrf
            
            <div class="p-3.5 rounded-[6px] bg-[#161616] border border-[#333333] text-xs text-[#a1a1a1] space-y-1 font-mono">
                <p>No. Reservasi: <strong class="text-white">{{ $reservation['reservation_number'] }}</strong></p>
                <p>Fasilitas: <strong class="text-white font-sans">{{ $reservation['facility_name'] }}</strong></p>
                <p>Waktu: <strong class="text-[#0070f3]">{{ $reservation['date'] }}, {{ $reservation['start_time'] }} - {{ $reservation['end_time'] }} WIB</strong></p>
            </div>

            <!-- Mandatory Cancellation Reason -->
            <div class="space-y-1.5">
                <label for="cancellation_reason" class="block font-mono text-[10px] uppercase tracking-wider text-[#888888] font-medium">
                    Alasan Pembatalan <span class="text-[#ff453a]">*</span>
                </label>
                <textarea 
                    id="cancellation_reason" 
                    name="cancellation_reason" 
                    rows="3" 
                    required 
                    placeholder="Contoh: Agenda perkuliahan dipindahkan ke hari lain, dosen berhalangan hadir, dll..."
                    class="w-full bg-[#0a0a0a] border border-[#333333] text-white text-xs rounded-[6px] p-3 focus:outline-none focus:border-[#ff453a] transition placeholder-[#666666]"
                ></textarea>
                <span class="text-[11px] text-[#888888] font-mono">Alasan pembatalan wajib diisi untuk log audit.</span>
            </div>

            <!-- Actions -->
            <div class="pt-3 flex items-center justify-end gap-2 border-t border-[#333333]">
                <button 
                    type="button" 
                    onclick="closeCancelModal()" 
                    class="bg-[#171717] hover:bg-[#222222] text-[#a1a1a1] hover:text-white border border-[#333333] font-medium text-xs px-4 py-2 rounded-full transition">
                    Kembali
                </button>
                <button 
                    type="submit" 
                    class="bg-[#2b0000] hover:bg-[#3b0000] text-[#ff453a] border border-[#550000] font-medium text-xs px-4 py-2 rounded-full transition shadow-none">
                    Konfirmasi Batalkan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCancelModal() {
        document.getElementById('cancelModal').classList.remove('hidden');
    }
    function closeCancelModal() {
        document.getElementById('cancelModal').classList.add('hidden');
    }
</script>

