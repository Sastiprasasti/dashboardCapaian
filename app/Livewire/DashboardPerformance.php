<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardPerformance extends Component
{
    public $selectedKecamatan = '';
    public $listKecamatan = [];
    public $rekap = ['total_submitted' => 0, 'total_open' => 0, 'total_target' => 0];
    public $petugasData = ['pml' => 0, 'ppl' => 0];
    public $tableData = [];

    public function mount()
    {
        if (Schema::hasTable('assignments')) {
            $this->listKecamatan = DB::table('assignments')->distinct()->pluck('kecamatan')->filter();
        } else {
            $this->listKecamatan = collect(['Garut Kota', 'Tarogong Kidul', 'Tarogong Kaler', 'Cilawu']);
        }
        $this->loadData();
    }

    public function updatedSelectedKecamatan()
    {
        $this->loadData();
        $this->dispatch('updateChart', data: $this->petugasData);
    }

    public function loadData()
    {
        if (Schema::hasTable('assignments')) {
            $query = DB::table('assignments');
            $petugasQuery = Schema::hasTable('petugas') ? DB::table('petugas') : null;

            if ($this->selectedKecamatan) {
                $query->where('kecamatan', $this->selectedKecamatan);
                if ($petugasQuery) $petugasQuery->where('kecamatan', $this->selectedKecamatan);
            }

            $this->rekap['total_submitted'] = (clone $query)->where('status', 'submitted')->count();
            $this->rekap['total_open'] = (clone $query)->where('status', 'open')->count();
            $this->rekap['total_target'] = (clone $query)->count();

            $this->petugasData['pml'] = $petugasQuery ? (clone $petugasQuery)->where('role', 'PML')->count() : 12;
            $this->petugasData['ppl'] = $petugasQuery ? (clone $petugasQuery)->where('role', 'PPL')->count() : 45;

            $this->tableData = $query->select('idsls', 'kecamatan', 'desa', 'nama_pml', 'jumlah_submit')->limit(5)->get()->map(fn($item) => (array)$item)->toArray();
        } else {
            $this->rekap = [
                'total_submitted' => $this->selectedKecamatan ? 150 : 1420,
                'total_open' => $this->selectedKecamatan ? 30 : 340,
                'total_target' => $this->selectedKecamatan ? 180 : 1760,
            ];

            $this->petugasData = [
                'pml' => $this->selectedKecamatan ? 5 : 45,
                'ppl' => $this->selectedKecamatan ? 18 : 120
            ];

            $this->tableData = [
                ['idsls' => '3205010001', 'kecamatan' => $this->selectedKecamatan ?: 'Garut Kota', 'desa' => 'Kota Kulon', 'nama_pml' => 'Ahmad Sodikin', 'jumlah_submit' => 45],
                ['idsls' => '3205010002', 'kecamatan' => $this->selectedKecamatan ?: 'Garut Kota', 'desa' => 'Regol', 'nama_pml' => 'Siti Aminah', 'jumlah_submit' => 38],
                ['idsls' => '3205020001', 'kecamatan' => $this->selectedKecamatan ?: 'Tarogong Kidul', 'desa' => 'Sukagalih', 'nama_pml' => 'Dedi Dermawan', 'jumlah_submit' => 52],
            ];
        }
    }

    public function render()
    {
        return view('livewire.dashboard-performance');
    }
}
