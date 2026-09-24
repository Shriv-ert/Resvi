@extends('layouts.app')

@section('title', 'Riwayat Laporan Masalah Fasilitas')

@section('content')
<div class="max-w-[1200px] mx-auto px-4 sm:px-6 py-8">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-[6px] text-xs font-mono text-[#ff453a] bg-[#2b0000] border border-[#550000] mb-3">
                <span class="w-1.5 h-1.5 rounded-full bg-[#ff453a]"></span>
                <span>Portal Pengguna · Pelaporan Sarpras</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-semibold text-white tracking-[-0.03em]">
                Riwayat dan status laporan saya.
            </h1>
            <p class="text-xs sm:text-sm text-[#a1a1a1] mt-1">
                Pantau progres penanganan kendala atau kerusakan sarpras yang Anda laporkan.
            </p>
        </div>

        <div>
            <a href="{{ route('user.reports.create', ['as_user' => 1]) }}" 
               class="bg-white hover:bg-[#e6e6e6] text-black font-medium text-xs px-4 py-2 rounded-full transition flex items-center gap-2 active:scale-95 shadow-none">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Buat Laporan Baru
            </a>
        </div>
    </div>

    <!-- Reports Table: Crisp border, micro 6px radius, NO soft shadows -->
    <div class="bg-[#111111] border border-[#333333] rounded-[6px] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-[#a1a1a1]">
                <thead class="bg-[#161616] border-b border-[#333333] text-[10px] font-mono uppercase text-[#888888] tracking-wider">
                    <tr>
                        <th class="py-3 px-5 font-medium">No. Laporan</th>
                        <th class="py-3 px-5 font-medium">Fasilitas</th>
                        <th class="py-3 px-5 font-medium">Kategori</th>
                        <th class="py-3 px-5 font-medium">Tanggal Lapor</th>
                        <th class="py-3 px-5 font-medium">Status Penanganan</th>
                        <th class="py-3 px-5 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#333333] text-xs">
                    @forelse($reports as $rep)
                        <tr class="hover:bg-[#171717] transition-colors">
                            <td class="py-3.5 px-5 font-mono text-white font-medium">
                                {{ $rep['report_number'] }}
                            </td>
                            <td class="py-3.5 px-5 font-medium text-white">
                                {{ $rep['facility_name'] }}
                            </td>
                            <td class="py-3.5 px-5 text-[#a1a1a1] capitalize font-mono text-[11px]">
                                {{ str_replace('_', ' ', $rep['category']) }}
                            </td>
                            <td class="py-3.5 px-5 text-[#888888] font-mono text-[11px]">
                                {{ $rep['created_at'] }}
                            </td>
                            <td class="py-3.5 px-5">
                                @if($rep['status'] === 'baru')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#f5a623] bg-[#2b1e00] border border-[#553c00]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#f5a623] animate-pulse"></span>
                                        Baru
                                    </span>
                                @elseif($rep['status'] === 'diproses')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#0070f3] bg-[#001733] border border-[#003b80]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#0070f3] animate-pulse"></span>
                                        Diproses
                                    </span>
                                @elseif($rep['status'] === 'selesai')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#50e3c2] bg-[#002b11] border border-[#005522]">
                                        Selesai
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#ff453a] bg-[#2b0000] border border-[#550000]">
                                        Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-5 text-right">
                                <a href="{{ route('user.reports.show', ['report' => $rep['id'], 'as_user' => 1]) }}" 
                                   class="text-white hover:text-[#0070f3] font-medium transition inline-flex items-center gap-1 text-xs">
                                    Detail →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-[#888888]">
                                Belum ada laporan kerusakan yang pernah dibuat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
