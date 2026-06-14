<div class="font-sans text-slate-800">
    
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h3 class="text-lg font-bold text-slate-900">Kabupaten Garut</h3>
            <p class="text-xs text-slate-500">Monitoring real-time petugas lapangan</p>
        </div>
        
        <div class="flex items-center gap-2 bg-white p-2 rounded-xl shadow-sm border border-slate-200 w-full sm:w-72">
            <span class="text-slate-400 pl-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" /></svg>
            </span>
            <select wire:model.live="selectedKecamatan" class="w-full bg-transparent border-none text-sm text-slate-700 font-medium focus:ring-0 focus:outline-none cursor-pointer py-1">
                <option value="">Semua Kecamatan</option>
                @foreach($listKecamatan as $kec)
                    <option value="{{ $kec }}">{{ $kec }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        
        <div class="lg:col-span-1 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div>
                <h4 class="text-sm font-semibold text-slate-900">Jumlah Petugas</h4>
                <p class="text-xs text-slate-400 mb-4">Rasio PML vs PPL aktif</p>
            </div>
            <div class="relative w-full h-52 flex items-center justify-center" wire:ignore>
                <canvas id="petugasChart"></canvas>
            </div>
        </div>

        <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <h4 class="text-sm font-semibold text-slate-900 mb-4">Rekap Progress Assignment</h4>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-emerald-50/60 border border-emerald-100 rounded-xl p-5">
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-700 block mb-1">Telah Submit</span>
                    <div class="flex items-baseline gap-2">
                        <span class="text-3xl font-extrabold text-emerald-900 tracking-tight">{{ number_format($rekap['total_submitted']) }}</span>
                        <span class="text-xs text-emerald-600 font-medium">berkas</span>
                    </div>
                </div>

                <div class="bg-amber-50/60 border border-amber-100 rounded-xl p-5">
                    <span class="text-xs font-bold uppercase tracking-wider text-amber-700 block mb-1">Masih Open</span>
                    <div class="flex items-baseline gap-2">
                        <span class="text-3xl font-extrabold text-amber-900 tracking-tight">{{ number_format($rekap['total_open']) }}</span>
                        <span class="text-xs text-amber-600 font-medium">berkas</span>
                    </div>
                </div>

                <div class="bg-blue-50/60 border border-blue-100 rounded-xl p-5">
                    <span class="text-xs font-bold uppercase tracking-wider text-blue-700 block mb-1">Total Target</span>
                    <div class="flex items-baseline gap-2">
                        <span class="text-3xl font-extrabold text-blue-900 tracking-tight">{{ number_format($rekap['total_target']) }}</span>
                        <span class="text-xs text-blue-600 font-medium">target</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h4 class="text-sm font-semibold text-slate-900">Rincian Capaian SLS</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-semibold text-xs tracking-wider uppercase">
                        <th class="py-3 px-6">ID SLS</th>
                        <th class="py-3 px-6">Kecamatan</th>
                        <th class="py-3 px-6">Desa</th>
                        <th class="py-3 px-6">Nama PML</th>
                        <th class="py-3 px-6 text-right">Jumlah Submit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                    @forelse($tableData as $row)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-3.5 px-6 font-mono text-xs text-slate-500">{{ $row['idsls'] }}</td>
                        <td class="py-3.5 px-6">{{ $row['kecamatan'] }}</td>
                        <td class="py-3.5 px-6">{{ $row['desa'] }}</td>
                        <td class="py-3.5 px-6 text-slate-900">{{ $row['nama_pml'] }}</td>
                        <td class="py-3.5 px-6 text-right font-bold text-slate-900">{{ number_format($row['jumlah_submit']) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-10 text-center text-slate-400 text-sm">Tidak ada data untuk kecamatan ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('livewire:init', () => {
            const ctx = document.getElementById('petugasChart').getContext('2d');
            
            let petugasChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['PML', 'PPL'],
                    datasets: [{
                        data: [{{ $petugasData['pml'] }}, {{ $petugasData['ppl'] }}],
                        backgroundColor: ['#3b82f6', '#f59e0b'],
                        borderRadius: 6,
                        barThickness: 40
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                        x: { grid: { display: false } }
                    }
                }
            });

            Livewire.on('updateChart', (event) => {
                petugasChart.data.datasets[0].data = [event.data.pml, event.data.ppl];
                petugasChart.update();
            });
        });
    </script>
</div>