<!-- Modal Konfirmasi Reaktivasi Fasilitas -->
<div
    id="reactivateFacilityModal"
    class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm transition-opacity duration-200"
    aria-labelledby="modal-title-reactivate"
    role="dialog"
    aria-modal="true"
>
    <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
        <div
            id="reactivateModalContent"
            class="relative w-full max-w-lg transform overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 text-left shadow-2xl transition-all sm:my-8 sm:p-8"
        >
            <!-- Modal Header -->
            <div class="flex items-start justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700 text-lg shadow-sm">
                        ↺
                    </span>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900" id="modal-title-reactivate">Aktifkan Kembali Fasilitas</h3>
                        <p class="text-xs text-slate-500">Kembalikan master data fasilitas ke katalog publik</p>
                    </div>
                </div>
                <button
                    type="button"
                    class="close-reactivate-modal rounded-lg p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition"
                    aria-label="Tutup"
                >
                    ✕
                </button>
            </div>

            <!-- Clarification Notice -->
            <div class="my-4 rounded-xl border border-sky-200 bg-sky-50 p-4 text-xs text-sky-900 space-y-2 leading-relaxed">
                <p class="font-bold">Preservasi Status Fisik Operasional:</p>
                <p>
                    Pengaktifan kembali data master ini <strong>tidak mengubah</strong> status operasional fasilitas.
                </p>
                @if ($facility->operational_status->value === 'dalam_perbaikan')
                    <div class="rounded-lg bg-amber-100/70 p-2.5 text-amber-900 font-medium">
                        ⚠ Fasilitas ini saat ini berstatus <strong>Dalam Perbaikan</strong>. Setelah diaktifkan kembali, fasilitas tetap belum dapat direservasi hingga Petugas mengubah status operasionalnya menjadi "Dapat Digunakan".
                    </div>
                @else
                    <p class="text-sky-800">
                        Fasilitas berstatus operasional <strong>Dapat digunakan</strong> dan akan langsung siap menerima reservasi baru.
                    </p>
                @endif
            </div>

            <!-- Facility Summary -->
            <div class="mb-4 rounded-xl bg-slate-50 p-3 text-xs text-slate-600">
                <p><strong>Fasilitas:</strong> {{ $facility->name }}</p>
                <p class="text-slate-500 mt-0.5">{{ $facility->type->label() }} — {{ $facility->location }}</p>
            </div>

            <!-- Form Reactivate -->
            <form method="POST" action="{{ route('admin.facilities.reactivate', $facility) }}">
                @csrf

                <!-- Modal Actions -->
                <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100">
                    <button
                        type="button"
                        class="close-reactivate-modal rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="rounded-xl bg-sky-700 px-5 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-sky-800 focus:outline-none focus:ring-4 focus:ring-sky-200"
                    >
                        Konfirmasi Aktifkan Kembali
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('reactivateFacilityModal');
        const openBtn = document.getElementById('openReactivateModalBtn');
        const closeBtns = document.querySelectorAll('.close-reactivate-modal');

        function openModal() {
            if (modal) {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }
        }

        function closeModal() {
            if (modal) {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        }

        if (openBtn) {
            openBtn.addEventListener('click', openModal);
        }

        closeBtns.forEach(btn => {
            btn.addEventListener('click', closeModal);
        });

        if (modal) {
            modal.addEventListener('click', function (e) {
                if (e.target === modal) {
                    closeModal();
                }
            });
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
                closeModal();
            }
        });
    });
</script>
