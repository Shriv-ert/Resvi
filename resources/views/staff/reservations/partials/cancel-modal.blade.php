<!-- Modal Pembatalan Mendesak oleh Petugas -->
<div id="cancelModalStaff" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm hidden">
    <div class="bg-[#111111] border border-[#333333] rounded-[6px] max-w-lg w-full p-6 shadow-none relative animate-in fade-in zoom-in-95 duration-200">
        
        <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-[6px] bg-[#2b1e00] text-[#f5a623] flex items-center justify-center border border-[#553c00]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-white tracking-tight">Pembatalan Mendesak Petugas</h3>
                    <p class="text-xs text-[#888888]">Hanya untuk situasi darurat sebelum waktu mulai penggunaan.</p>
                </div>
            </div>
            <button type="button" onclick="closeCancelModalStaff()" class="text-[#888888] hover:text-white p-1 rounded-[6px]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('staff.reservations.cancel', ['reservation' => $reservation['id']]) }}" class="space-y-4">
            @csrf
            
            <div class="p-3.5 rounded-[6px] bg-[#161616] border border-[#333333] text-xs text-[#a1a1a1] space-y-1 font-mono">
                <p>Nomor: <strong class="text-white">{{ $reservation['reservation_number'] }}</strong></p>
                <p>Pemohon: <strong class="text-white font-sans">{{ $reservation['applicant_name'] }}</strong></p>
                <p>Fasilitas: <strong class="text-white font-sans">{{ $reservation['facility_name'] }}</strong></p>
            </div>

            <!-- Mandatory Free-text Reason -->
            <div class="space-y-1.5">
                <label for="urgent_cancellation_reason" class="block font-mono text-[11px] uppercase tracking-wider text-[#888888] font-medium">
                    Alasan Pembatalan Mendesak <span class="text-[#f5a623]">*</span>
                </label>
                <textarea 
                    id="urgent_cancellation_reason" 
                    name="cancellation_reason" 
                    rows="3" 
                    required 
                    placeholder="Tuliskan kendala mendesak (misal: Terjadi kebocoran atap mendadak, korsleting listrik, atau pemadaman darurat)..."
                    class="w-full bg-[#0a0a0a] border border-[#333333] text-white text-xs rounded-[6px] p-3 focus:outline-none focus:border-[#f5a623] transition-all placeholder-[#666666]"
                ></textarea>
            </div>

            <div class="pt-3 flex items-center justify-end gap-2 border-t border-[#333333]">
                <button 
                    type="button" 
                    onclick="closeCancelModalStaff()" 
                    class="bg-[#171717] hover:bg-[#222222] text-white border border-[#333333] font-medium text-xs px-3.5 py-1.5 rounded-[6px] transition">
                    Batal
                </button>
                <button 
                    type="submit" 
                    class="bg-[#ee0000] hover:bg-[#c50000] text-white font-medium text-xs px-4 py-1.5 rounded-[6px] transition shadow-none">
                    Konfirmasi Pembatalan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCancelModalStaff() {
        document.getElementById('cancelModalStaff').classList.remove('hidden');
    }
    function closeCancelModalStaff() {
        document.getElementById('cancelModalStaff').classList.add('hidden');
    }
</script>
