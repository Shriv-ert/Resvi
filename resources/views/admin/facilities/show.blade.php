@extends('layouts.admin')

@php
    $fId = is_array($facility) ? $facility['id'] : $facility->id;
    $fName = is_array($facility) ? $facility['name'] : $facility->name;
    $fTypeLabel = is_array($facility) ? ucfirst($facility['type']) : ($facility->type instanceof \App\Enums\FacilityType ? $facility->type->label() : ucfirst($facility->type));
    $fLocation = is_array($facility) ? $facility['location'] : $facility->location;
    $fCapacity = is_array($facility) ? ($facility['capacity'] ?? null) : $facility->capacity;
    $fDescription = is_array($facility) ? $facility['description'] : $facility->description;
    $fOperational = is_array($facility) ? $facility['operational_status'] : ($facility->operational_status instanceof \BackedEnum ? $facility->operational_status->value : $facility->operational_status);
    $fIsActive = is_array($facility) ? ($facility['is_active'] ?? true) : $facility->is_active;

    if (is_array($facility)) {
        $photoUrl = $facility['photo'] ?? null;
        $statusLogs = [];
    } else {
        $primaryPhoto = $facility->photos?->firstWhere('is_primary', true) ?? $facility->photos?->first();
        $photoUrl = $facility->primary_photo_url ?? ($primaryPhoto ? $primaryPhoto->url : null);
        $statusLogs = $facility->statusLogs ?? collect();
    }
@endphp

@section('title', 'Detail Fasilitas · ' . $fName)

@section('content')
<div class="max-w-[1200px] mx-auto px-4 sm:px-6 py-8">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs font-mono text-[#888888] mb-6">
        <a href="{{ route('admin.facilities.index') }}" class="hover:text-white transition">Master Fasilitas</a>
        <span>/</span>
        <span class="text-white truncate max-w-xs">{{ $fName }}</span>
    </nav>

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-semibold text-white tracking-[-0.03em] flex items-center gap-3">
                {{ $fName }}
                @if($fIsActive)
                    <span class="px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#50e3c2] bg-[#002b11] border border-[#005522]">Aktif</span>
                @else
                    <span class="px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#888888] bg-[#161616] border border-[#333333]">Nonaktif</span>
                @endif
            </h1>
            <p class="text-xs text-[#888888] mt-1 font-mono">
                Lokasi: <span class="text-white">{{ $fLocation }}</span> • Tipe: <span class="text-white">{{ $fTypeLabel }}</span>
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.facilities.edit', $facility) }}" 
               class="bg-[#171717] hover:bg-[#222222] text-white border border-[#333333] font-medium text-xs px-3.5 py-2 rounded-[6px] transition flex items-center gap-1.5 shadow-none">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Ubah Data
            </a>

            @if($fIsActive)
                <button type="button" 
                        onclick="document.getElementById('deactivateModal').classList.remove('hidden')"
                        class="bg-[#2b0000] hover:bg-[#3d0000] text-[#ff453a] border border-[#550000] font-medium text-xs px-3.5 py-2 rounded-[6px] transition flex items-center gap-1.5 shadow-none">
                    Nonaktifkan Fasilitas
                </button>
            @else
                <button type="button" 
                        onclick="document.getElementById('reactivateModal').classList.remove('hidden')"
                        class="bg-[#002b11] hover:bg-[#003d18] text-[#50e3c2] border border-[#005522] font-medium text-xs px-3.5 py-2 rounded-[6px] transition flex items-center gap-1.5 shadow-none">
                    Aktifkan Kembali
                </button>
            @endif
        </div>
    </div>

    <!-- Main Detail Card -->
    <div class="bg-[#111111] border border-[#333333] rounded-[6px] p-6 space-y-6">
        @if(!empty($photoUrl))
            <div class="h-64 rounded-[6px] overflow-hidden border border-[#333333] bg-[#161616]">
                <img src="{{ $photoUrl }}" alt="{{ $fName }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1497366216548-37526070297c?w=800&auto=format&fit=crop&q=80';">
            </div>
        @endif

        <!-- 3 Grid Info Cells -->
        <div class="grid grid-cols-1 sm:grid-cols-3 bg-[#333333] gap-[1px] border border-[#333333] rounded-[6px] overflow-hidden">
            <div class="bg-[#111111] p-4">
                <span class="text-[11px] text-[#888888] font-mono block mb-0.5">Kapasitas orang</span>
                <h3 class="text-sm font-semibold text-white font-mono">{{ $fCapacity ? $fCapacity . ' Orang' : '— (Unit Alat)' }}</h3>
            </div>

            <div class="bg-[#111111] p-4">
                <span class="text-[11px] text-[#888888] font-mono block mb-0.5">Status operasional fisik</span>
                <h3 class="text-sm font-semibold {{ $fOperational === 'dapat_digunakan' ? 'text-[#50e3c2]' : 'text-[#ff453a]' }}">
                    {{ $fOperational === 'dapat_digunakan' ? 'Dapat Digunakan' : 'Dalam Perbaikan' }}
                </h3>
                <span class="text-[10px] text-[#666666] font-mono">Dikelola oleh petugas</span>
            </div>

            <div class="bg-[#111111] p-4">
                <span class="text-[11px] text-[#888888] font-mono block mb-0.5">Status data master</span>
                <h3 class="text-sm font-semibold {{ $fIsActive ? 'text-[#50e3c2]' : 'text-[#888888]' }}">
                    {{ $fIsActive ? 'Aktif (Katalog Publik)' : 'Nonaktif (Arsip)' }}
                </h3>
                <span class="text-[10px] text-[#666666] font-mono">Dikelola oleh admin</span>
            </div>
        </div>

        <!-- Description -->
        <div>
            <h4 class="text-xs font-semibold text-[#888888] uppercase tracking-wider mb-2 font-mono">Deskripsi lengkap</h4>
            <div class="bg-[#161616] border border-[#333333] rounded-[6px] p-4 text-xs text-[#ededed] leading-relaxed whitespace-pre-line">
                {{ $fDescription }}
            </div>
        </div>
    </div>

    <!-- Status History & Audit Logs Section -->
    <div class="bg-[#111111] border border-[#333333] rounded-[6px] mt-8 overflow-hidden">
        <div class="border-b border-[#333333] bg-[#161616] px-6 py-4">
            <h2 class="text-sm font-semibold text-white">
                Riwayat Status & Mutasi Fasilitas
            </h2>
            <p class="text-xs text-[#888888] mt-0.5 font-mono">
                Rekam jejak perubahan status master data (oleh Admin) dan status operasional fisik (oleh Petugas).
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-[#ededed]">
                <thead class="border-b border-[#333333] bg-[#111111] text-[#888888] font-mono uppercase text-[10px] text-center">
                    <tr>
                        <th class="px-5 py-3 text-center">Waktu Mutasi</th>
                        <th class="px-5 py-3 text-center">Jenis Perubahan</th>
                        <th class="px-5 py-3 text-center">Transisi Nilai</th>
                        <th class="px-5 py-3 text-center">Alasan / Catatan</th>
                        <th class="px-5 py-3 text-center">Pelaku</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#222222]">
                    @forelse ($statusLogs as $log)
                        <tr class="hover:bg-[#161616] transition">
                            <td class="whitespace-nowrap px-5 py-3 font-mono text-[#888888]">
                                {{ $log->changed_at ? $log->changed_at->translatedFormat('d M Y, H:i') : '-' }} WIB
                            </td>
                            <td class="whitespace-nowrap px-5 py-3 font-medium text-white">
                                {{ $log->change_type->label() }}
                            </td>
                            <td class="whitespace-nowrap px-5 py-3 font-mono">
                                <span class="text-[#888888]">{{ $log->old_value }}</span>
                                <span class="mx-1 text-[#666666]">→</span>
                                <span class="text-white font-semibold">{{ $log->new_value }}</span>
                            </td>
                            <td class="px-5 py-3 text-[#a1a1a1] max-w-sm">
                                {{ $log->reason ?? '—' }}
                            </td>
                            <td class="whitespace-nowrap px-5 py-3 font-mono">
                                <div class="text-white">{{ $log->changedBy->name ?? 'Sistem' }}</div>
                                <div class="text-[10px] text-[#666666] uppercase">{{ $log->changedBy->role->label() ?? 'System' }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-6 text-center text-[#888888] font-mono">
                                Belum ada riwayat mutasi status yang tercatat untuk fasilitas ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@include('admin.facilities.partials.deactivate-modal', ['facility' => $facility])
@include('admin.facilities.partials.reactivate-modal', ['facility' => $facility])
@endsection
