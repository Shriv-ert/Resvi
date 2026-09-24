@extends('layouts.admin')

@section('title', 'Tambah Fasilitas Baru')

@section('content')
<div class="mx-auto max-w-2xl space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs font-medium text-slate-500">
        <a href="{{ route('admin.facilities.index') }}" class="hover:text-slate-900 transition">Kelola Fasilitas</a>
        <span>/</span>
        <span class="text-slate-900">Tambah Fasilitas</span>
    </nav>

    <!-- Header Section -->
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Pendaftaran Fasilitas Baru</h1>
        <p class="mt-1 text-sm text-slate-600">
            Daftarkan objek fasilitas baru (ruang kelas, aula, laboratorium, alat, atau lapangan).
        </p>
    </div>

    <!-- Default Status Callout -->
    <div class="rounded-2xl border border-sky-100 bg-sky-50/70 p-4 text-sm text-sky-900 shadow-sm">
        <div class="flex items-start gap-3">
            <span class="text-sky-700 font-bold text-base leading-none">ℹ</span>
            <div class="text-xs leading-5">
                <span class="font-bold">Status Awal Fasilitas:</span>
                Fasilitas yang baru ditambahkan akan langsung berstatus master <strong>Aktif</strong> dan berstatus operasional <strong>Dapat digunakan</strong>, sehingga dapat segera diperiksa ketersediaannya di katalog publik.
            </div>
        </div>
    </div>

    <!-- Form Container -->
    <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm">
        <form method="POST" action="{{ route('admin.facilities.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-semibold text-slate-900">
                    Nama Fasilitas <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="Contoh: Lab Komputer Jaringan 1 atau Proyektor Epson EB-X500"
                    required
                    class="mt-2 block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm shadow-sm outline-none transition placeholder:text-slate-400 focus:border-sky-600 focus:ring-4 focus:ring-sky-100"
                >
                @error('name')
                    <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Grid: Type & Location -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <!-- Type -->
                <div>
                    <label for="type" class="block text-sm font-semibold text-slate-900">
                        Tipe Fasilitas <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="type"
                        name="type"
                        required
                        class="mt-2 block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm shadow-sm outline-none transition focus:border-sky-600 focus:ring-4 focus:ring-sky-100"
                    >
                        <option value="">Pilih Tipe...</option>
                        @foreach (\App\Enums\FacilityType::cases() as $typeCase)
                            <option value="{{ $typeCase->value }}" {{ old('type') === $typeCase->value ? 'selected' : '' }}>
                                {{ $typeCase->label() }}
                            </option>
                        @endforeach
                    </select>
                    @error('type')
                        <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Location -->
                <div>
                    <label for="location" class="block text-sm font-semibold text-slate-900">
                        Lokasi / Gedung <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="location"
                        name="location"
                        value="{{ old('location') }}"
                        placeholder="Contoh: Gedung D Lantai 2"
                        required
                        class="mt-2 block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm shadow-sm outline-none transition placeholder:text-slate-400 focus:border-sky-600 focus:ring-4 focus:ring-sky-100"
                    >
                    @error('location')
                        <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Capacity -->
            <div>
                <label for="capacity" class="block text-sm font-semibold text-slate-900">
                    Kapasitas (Orang)
                </label>
                <input
                    type="number"
                    id="capacity"
                    name="capacity"
                    value="{{ old('capacity') }}"
                    min="1"
                    placeholder="Contoh: 40 (kosongkan jika tipe Alat)"
                    class="mt-2 block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm shadow-sm outline-none transition placeholder:text-slate-400 focus:border-sky-600 focus:ring-4 focus:ring-sky-100"
                >
                <p id="capacityHelpText" class="mt-1.5 text-xs text-slate-500">
                    Boleh dikosongkan khusus untuk objek yang tidak memiliki kapasitas orang (seperti alat).
                </p>
                @error('capacity')
                    <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-semibold text-slate-900">
                    Deskripsi Lengkap Fasilitas <span class="text-red-500">*</span>
                </label>
                <textarea
                    id="description"
                    name="description"
                    rows="4"
                    required
                    placeholder="Jelaskan spesifikasi, fasilitas pendukung, atau aturan khusus..."
                    class="mt-2 block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm shadow-sm outline-none transition placeholder:text-slate-400 focus:border-sky-600 focus:ring-4 focus:ring-sky-100"
                >{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Photo Upload with Vanilla JS Preview -->
            <div>
                <label class="block text-sm font-semibold text-slate-900">
                    Foto Fasilitas (Opsional)
                </label>
                <div class="mt-2 flex items-center gap-4">
                    <div id="previewContainer" class="hidden">
                        <img id="imagePreview" src="#" alt="Pratinjau Foto" class="h-20 w-20 rounded-xl object-cover border border-slate-200 shadow-sm">
                    </div>
                    <input
                        type="file"
                        id="photo"
                        name="photo"
                        accept="image/png,image/jpeg,image/jpg,image/webp"
                        class="block w-full text-xs text-slate-500 file:mr-4 file:rounded-xl file:border-0 file:bg-sky-50 file:px-4 file:py-2.5 file:text-xs file:font-semibold file:text-sky-700 hover:file:bg-sky-100 transition cursor-pointer"
                    >
                </div>
                <p class="mt-1.5 text-xs text-slate-500">Format: JPG, JPEG, PNG, WEBP. Maksimal 2 MB.</p>
                @error('photo')
                    <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a
                    href="{{ route('admin.facilities.index') }}"
                    class="rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                >
                    Batal
                </a>
                <button
                    type="submit"
                    class="rounded-xl bg-sky-700 px-6 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-sky-800 focus:outline-none focus:ring-4 focus:ring-sky-200"
                >
                    Simpan & Daftarkan Fasilitas
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Vanilla JS untuk image preview & interaksi form
    document.addEventListener('DOMContentLoaded', function () {
        const photoInput = document.getElementById('photo');
        const previewContainer = document.getElementById('previewContainer');
        const imagePreview = document.getElementById('imagePreview');
        const typeSelect = document.getElementById('type');
        const capacityInput = document.getElementById('capacity');
        const capacityHelpText = document.getElementById('capacityHelpText');

        if (photoInput) {
            photoInput.addEventListener('change', function () {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        imagePreview.src = e.target.result;
                        previewContainer.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                } else {
                    previewContainer.classList.add('hidden');
                }
            });
        }

        if (typeSelect) {
            typeSelect.addEventListener('change', function () {
                if (this.value === 'alat') {
                    capacityHelpText.textContent = 'Objek bertipe Alat umumnya tidak memiliki kapasitas orang (biarkan kosong).';
                } else {
                    capacityHelpText.textContent = 'Masukkan jumlah daya tampung orang untuk ruangan/fasilitas ini.';
                }
            });
        }
    });
</script>
@endpush
@endsection
