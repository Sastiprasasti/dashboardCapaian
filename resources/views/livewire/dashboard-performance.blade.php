<div class="bg-slate-950 p-6 rounded-lg text-white font-sans min-h-screen">

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-slate-900 border border-slate-800 p-6 rounded-xl shadow-lg relative overflow-hidden">
            <p class="text-xs font-semibold text-amber-400 uppercase tracking-wider mb-2">TOTAL STATUS OPEN & DRAFT </p>
            <div class="flex items-baseline gap-2">
                <span class="text-4xl font-extrabold tracking-tight text-white">{{ number_format($rekap['total_open']) }}</span>
                <span class="text-xs text-gray-500 font-medium">SLS</span>
            </div>
            <div class="absolute right-4 bottom-2 opacity-5 text-amber-400 text-7xl font-bold">📂</div>
        </div>

        <div class="bg-slate-900 border border-slate-800 p-6 rounded-xl shadow-lg relative overflow-hidden">
            <p class="text-xs font-semibold text-emerald-400 uppercase tracking-wider mb-2">TOTAL STATUS SUBMIT dan/atau APPROVE</p>
            <div class="flex items-baseline gap-2">
                <span class="text-4xl font-extrabold tracking-tight text-white">{{ number_format($rekap['total_submitted']) }}</span>
                <span class="text-xs text-gray-500 font-medium">SLS</span>
            </div>
            <div class="absolute right-4 bottom-2 opacity-5 text-emerald-400 text-7xl font-bold">✅</div>
        </div>

        <div class="bg-slate-900 border border-slate-800 p-6 rounded-xl shadow-lg relative overflow-hidden">
            <p class="text-xs font-semibold text-indigo-400 uppercase tracking-wider mb-2">TOTAL ASSIGNMENT FASIH</p>
            <div class="flex items-baseline gap-2">
                <span class="text-4xl font-extrabold tracking-tight text-white">{{ number_format($rekap['total_target']) }}</span>
                <span class="text-xs text-gray-500 font-medium">Beban Tugas</span>
            </div>
            <div class="absolute right-4 bottom-2 opacity-5 text-indigo-400 text-7xl font-bold">📊</div>
        </div>
    </div>

    <div class="bg-slate-900 border border-slate-800 p-4 rounded-xl flex flex-wrap gap-4 items-center justify-between mb-6">
        
        <div class="flex flex-wrap gap-4">
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Filter Kecamatan</label>
                <select wire:model.live="selectedKecamatan" class="bg-slate-950 text-white rounded-lg p-2 text-sm border border-slate-800 focus:outline-none focus:border-indigo-500 w-52">
                    <option value="">Semua Kecamatan</option>
                    @foreach($listKecamatan as $kec)
                        <option value="{{ $kec }}">{{ $kec }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Filter Desa / Kelurahan</label>
                <select wire:model.live="selectedDesa" class="bg-slate-950 text-white rounded-lg p-2 text-sm border border-slate-800 focus:outline-none focus:border-indigo-500 w-52" {{ empty($selectedKecamatan) ? 'disabled' : '' }}>
                    <option value="">Semua Desa</option>
                    @foreach($listDesa as $desa)
                        <option value="{{ $desa }}">{{ $desa }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1 md:text-right">Tampilkan Ringkasan By</label>
            <select wire:model.live="viewMode" class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg p-2 text-sm font-semibold border border-indigo-500 cursor-pointer focus:outline-none transition">
                <option value="sls">📋 Per Kode / Wilayah SubSLS</option>
                <option value="pml">👤 Per Nama Pengawas (PML)</option>
                <option value="ppl">🧑‍💻 Per Nama Pencacah (PPL)</option>
            </select>
        </div>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden shadow-xl">
        <div class="p-4 border-b border-slate-800 bg-slate-900/50">
            <h3 class="text-sm font-bold tracking-wide text-gray-300 uppercase">
                Tabel Ringkasan Beban Kerja @if($viewMode === 'pml') Pengawas (PML) @elseif($viewMode === 'ppl') Pencacah (PPL) @else Wilayah Tugas SubSLS @endif
            </h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full text-left border-collapse">
                <thead class="bg-slate-950 text-gray-400 text-[11px] uppercase tracking-wider font-semibold border-b border-slate-800">
                    <tr>
                        <th class="p-4">{{ $viewMode === 'pml' ? 'Nama Pengawas (PML)' : 'Kode SubSLS' }}</th>

                    @if($viewMode == 'sls')
                        <th class="p-3 sm:p-4">Nama Wilayah SLS</th>
                    @endif
                        <th class="p-4">Kecamatan</th>
                        <th class="p-4">Desa</th>
                        <th class="p-4 text-amber-400 text-right">Total Open</th>
                        <th class="p-4 text-emerald-400 text-right">Total Submit</th>
                        <th class="p-4 text-indigo-400 text-right">Beban Kerja</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 text-sm text-gray-300">
                    @forelse($tableData as $row)
                        <tr class="hover:bg-slate-850 transition">
                            <td class="p-4 font-bold text-white tracking-wide">{{ $row['label_utama'] }}</td>

                            @if($viewMode == 'sls')
                            <td class="p-4 text-gray-400 max-w-xs truncate">{{ $row['label_sls'] }}</td>
                         @endif

                            {{-- <td class="p-4 text-gray-400 max-w-xs truncate">{{ $row['label_sls'] }}</td> --}}
                            <td class="p-4 text-gray-400">{{ $row['kecamatan'] }}</td>
                            <td class="p-4 text-gray-400">{{ $row['desa'] }}</td>
                            <td class="p-4 font-bold text-amber-500 bg-amber-500/5 text-right">{{ number_format($row['open']) }}</td>
                            <td class="p-4 font-bold text-emerald-500 bg-emerald-500/5 text-right">{{ number_format($row['submit']) }}</td>
                            <td class="p-4 font-bold text-indigo-400 bg-indigo-500/5 text-right">{{ number_format($row['beban']) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-12 text-center text-gray-500 font-medium">
                                Tidak ada entri data transaksi ditemukan untuk wilayah filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>