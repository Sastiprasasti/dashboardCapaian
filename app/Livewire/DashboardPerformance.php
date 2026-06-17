<?php

namespace App\Livewire;

use Livewire\Component;
use Exception;

class DashboardPerformance extends Component
{
    public $viewMode = 'sls'; // Pilihan: 'sls', 'pml', atau 'ppl'
    public $selectedKecamatan = 'BANYURESMI'; // Sesuai pilihan awal Anda sebelumnya
    public $selectedDesa = '';
    public $listKecamatan = [];
    public $listDesa = [];
    public $rekap = ['total_submitted' => 0, 'total_open' => 0, 'total_target' => 0];
    public $petugasData = ['pml' => 0, 'ppl' => 0];
    public $tableData = [];
    public $errorMessage = '';

    // Master Data 42 Kecamatan se-Kabupaten Garut (7-Digit)
    private $kodeKecamatanGarut = [
        '3205010' => 'CISEWU',
        '3205011' => 'CARINGIN',
        '3205020' => 'TALEGONG',
        '3205030' => 'BUNGBULANG',
        '3205031' => 'MEKARMUKTI',
        '3205040' => 'PAMULIHAN',
        '3205050' => 'PAKENJENG',
        '3205060' => 'CIKELET',
        '3205070' => 'PAMEUNGPEUK',
        '3205080' => 'CIBALONG',
        '3205090' => 'CISOMPET',
        '3205100' => 'PEUNDEUY',
        '3205110' => 'SINGAJAYA',
        '3205111' => 'CIHURIP',
        '3205120' => 'CIKAJANG',
        '3205130' => 'BANJARWANGI',
        '3205140' => 'CILAWU',
        '3205150' => 'BAYONGBONG',
        '3205151' => 'CIGEDUG',
        '3205160' => 'CISURUPAN',
        '3205161' => 'SUKARESMI',
        '3205170' => 'SAMARANG',
        '3205171' => 'PASIRWANGI',
        '3205181' => 'TAROGONG KIDUL',
        '3205182' => 'TAROGONG KALER',
        '3205190' => 'GARUT KOTA',
        '3205200' => 'KARANGPAWITAN',
        '3205210' => 'WANARAJA',
        '3205211' => 'SUCINARAJA',
        '3205212' => 'PANGATIKAN',
        '3205220' => 'SUKAWENING',
        '3205221' => 'KARANGTENGAH',
        '3205230' => 'BANYURESMI',
        '3205240' => 'LELES',
        '3205250' => 'LEUWIGOONG',
        '3205260' => 'CIBATU',
        '3205261' => 'KERSAMANAH',
        '3205270' => 'CIBIUK',
        '3205280' => 'KADUNGORA',
        '3205290' => 'BLUBUR LIMBANGAN',
        '3205300' => 'SELAAWI',
        '3205310' => 'MALANGBONG'
    ];

    public function mount()
    {
        $this->listKecamatan = array_values($this->kodeKecamatanGarut);
        sort($this->listKecamatan);

        $this->loadData();
    }

    public function updatedViewMode()
    {
        $this->loadData();
        $this->dispatchChart();
    }

    public function updatedSelectedKecamatan()
    {
        $this->selectedDesa = '';
        $this->loadData();
        $this->dispatchChart();
    }

    public function updatedSelectedDesa()
    {
        $this->loadData();
        $this->dispatchChart();
    }

    private function dispatchChart()
    {
        try {
            $this->dispatch('updateChart', data: $this->petugasData);
        } catch (Exception $e) {
        }
    }

    private function detectDelimiter($filePath)
    {
        if (!file_exists($filePath)) return ',';
        $file = fopen($filePath, 'r');
        $firstLine = fgets($file);
        fclose($file);
        return (substr_count($firstLine, ';') > substr_count($firstLine, ',')) ? ';' : ',';
    }

    public function loadData()
    {
        ini_set('auto_detect_line_endings', true);

        $absolutePath1 = $this->resolveFilePath('csv/file1.csv');
        $absolutePath2 = $this->resolveFilePath('csv/file2.csv');

        if (!$absolutePath1) {
            $this->errorMessage = "File status transaksi (file1.csv) tidak ditemukan.";
            $this->loadMockData();
            return;
        }

        try {
            $delimiter1 = $this->detectDelimiter($absolutePath1);
            $delimiter2 = $this->detectDelimiter($absolutePath2);

            $masterSlsMapping = [];
            $desaCollector = [];
            $aggregatedOutput = [];

            $currentFilterKec = strtoupper(trim($this->selectedKecamatan));
            $currentFilterDesa = strtoupper(trim($this->selectedDesa));

            // --- LANGKAH 1: EKSTRAK KAMUS RELASI PENUH DARI FILE 2 (MASTER) ---
            if ($absolutePath2 && ($handle2 = fopen($absolutePath2, "r")) !== FALSE) {
                $headers2 = $this->cleanHeaders(fgetcsv($handle2, 3000, $delimiter2));

                $idSlsIdx   = $this->findKeyIndex(['IDSUBSLS_25_2'], $headers2);
                $namaSlsIdx = $this->findKeyIndex(['Nama SLS', 'nama sls', 'NAMA SLS'], $headers2);
                $desaIdx    = $this->findKeyIndex(['NMDESA', 'NM_DESA', 'nama desa', 'NAMA DESA'], $headers2);
                $pmlIdx     = $this->findKeyIndex(['Nama PML', 'nama pml', 'NAMA PML'], $headers2);
                $pplIdx     = $this->findKeyIndex(['Nama PPL', 'nama ppl', 'NAMA PPL', 'Nama Pencacah', 'Pencacah'], $headers2);

                while (($data2 = fgetcsv($handle2, 3000, $delimiter2)) !== FALSE) {
                    if (empty(array_filter($data2))) continue;

                    $idKey    = ($idSlsIdx !== null && isset($data2[$idSlsIdx])) ? preg_replace('/[^0-9]/', '', trim((string)$data2[$idSlsIdx])) : null;
                    $namaSls  = ($namaSlsIdx !== null && isset($data2[$namaSlsIdx])) ? trim($data2[$namaSlsIdx]) : '';
                    $desaName = ($desaIdx !== null && isset($data2[$desaIdx])) ? strtoupper(trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $data2[$desaIdx]))) : '';

                    $pmlName  = ($pmlIdx !== null && isset($data2[$pmlIdx]) && trim($data2[$pmlIdx]) !== '') ? strtoupper(trim($data2[$pmlIdx])) : 'PML TANPA NAMA';
                    $pplName  = ($pplIdx !== null && isset($data2[$pplIdx]) && trim($data2[$pplIdx]) !== '') ? strtoupper(trim($data2[$pplIdx])) : 'PPL TANPA NAMA';

                    if ($idKey) {
                        $masterSlsMapping[$idKey] = [
                            'nama_sls' => $namaSls ?: 'RT/RW Tidak Tertera',
                            'desa'     => $desaName ?: 'DESA LUAR',
                            'pml'      => $pmlName,
                            'ppl'      => $pplName
                        ];
                    }
                }
                fclose($handle2);
            }

            // --- LANGKAH 2: PROSES DATA TRANSAKSI FILE 1 ---
            if (($handle1 = fopen($absolutePath1, "r")) !== FALSE) {
                $headers1 = $this->cleanHeaders(fgetcsv($handle1, 3000, $delimiter1));

                $identityIdx = $this->findKeyIndex(['codeIdentity', 'codeidentity'], $headers1);
                $fullCodeIdx = $this->findKeyIndex(['fullCode'], $headers1);
                $statusIdx   = $this->findKeyIndex(['assignmentStatusAlias'], $headers1);

                while (($data1 = fgetcsv($handle1, 3000, $delimiter1)) !== FALSE) {
                    if (empty(array_filter($data1))) continue;

                    $status = ($statusIdx !== null && isset($data1[$statusIdx])) ? strtoupper(trim($data1[$statusIdx])) : '';
                    $rawCode = ($fullCodeIdx !== null && isset($data1[$fullCodeIdx])) ? preg_replace('/[^0-9]/', '', trim((string)$data1[$fullCodeIdx])) : null;

                    // Ambil 16 digit terdepan dari codeIdentity
                    $rawIdentity = ($identityIdx !== null && isset($data1[$identityIdx])) ? trim((string)$data1[$identityIdx]) : '';
                    $lookupKey = preg_replace('/[^0-9]/', '', substr($rawIdentity, 0, 16));

                    $baseCode = $lookupKey ?: $rawCode;

                    if ($baseCode && strlen($baseCode) >= 7) {
                        $kecKode = substr($baseCode, 0, 7);
                        $detectedKec = $this->kodeKecamatanGarut[$kecKode] ?? 'GARUT KOTA';

                        // Tarik info metadata dari master mapping file2
                        $metaInfo = isset($masterSlsMapping[$lookupKey]) ? $masterSlsMapping[$lookupKey] : null;

                        $detectedDesa = $metaInfo['desa'] ?? ('DESA ' . substr($baseCode, 7, 3));
                        $detectedPml  = $metaInfo['pml'] ?? 'PML BELUM TERDAFTAR';
                        $detectedPpl  = $metaInfo['ppl'] ?? 'PPL BELUM TERDAFTAR';
                        $detectedSls  = $metaInfo['nama_sls'] ?? 'Nama SLS Tidak Terdaftar';

                        // A. Kolektor Dropdown Berjenjang Desa
                        if ($currentFilterKec && $detectedKec === $currentFilterKec) {
                            $desaCollector[$detectedDesa] = true;
                        }

                        // B. Jaring Filter Wilayah
                        if ($currentFilterKec && $detectedKec !== $currentFilterKec) continue;
                        if ($currentFilterDesa && $detectedDesa !== $currentFilterDesa) continue;

                        // C. Penentuan Key Grouping Aggregation Berbasis Mode View
                        $slsGroupCode = substr($baseCode, 0, 16);

                        if ($this->viewMode === 'pml') {
                            $groupKey = $detectedPml;
                            $labelUtama = $detectedPml;
                            $labelSls = 'Cakupan Tim Kerja (PML)';
                        } elseif ($this->viewMode === 'ppl') {
                            $groupKey = $detectedPpl;
                            $labelUtama = $detectedPpl;
                            $labelSls = 'Beban Cacah Pencacah (PPL)';
                        } else {
                            $groupKey = $slsGroupCode;
                            $labelUtama = $slsGroupCode;
                            $labelSls = $detectedSls;
                        }

                        // D. Inisialisasi Struktur Array Output Baris Tabel
                        if (!isset($aggregatedOutput[$groupKey])) {
                            $aggregatedOutput[$groupKey] = [
                                'label_utama' => $labelUtama,
                                'label_sls'   => $labelSls,
                                'desa'        => ($this->viewMode === 'sls') ? $detectedDesa : ($currentFilterDesa ?: 'Multi Desa'),
                                'kecamatan'   => $detectedKec,
                                'open'        => 0,
                                'submit'      => 0,
                                'beban'       => 0
                            ];
                        }

                        // E. Akumulasi Status
                        if ($status === 'OPEN' || $status === 'DRAFT') {
                            $aggregatedOutput[$groupKey]['open']++;
                        } elseif (
                            str_contains($status, 'SUBMIT') ||
                            str_contains($status, 'APPROVE') ||
                            $status === 'CLOSE' ||
                            $status === 'READY'
                        ) {
                            $aggregatedOutput[$groupKey]['submit']++;
                        } else {
                            $aggregatedOutput[$groupKey]['submit']++;
                        }

                        $aggregatedOutput[$groupKey]['beban']++;
                    }
                }
                fclose($handle1);
            }

            // Susun dropdown desa secara alfabetis
            $this->listDesa = array_filter(array_keys($desaCollector));
            sort($this->listDesa);

            // --- LANGKAH 3: SINKRONISASI TOTAL REKAP KARTU ATAS ---
            if (count($aggregatedOutput) > 0) {
                $this->tableData = array_values($aggregatedOutput);

                $this->rekap = [
                    'total_open'      => collect($this->tableData)->sum('open'),
                    'total_submitted' => collect($this->tableData)->sum('submit'),
                    'total_target'    => collect($this->tableData)->sum('beban'),
                ];

                $uniquePml = collect($masterSlsMapping)->pluck('pml')->unique()->count();
                $uniquePpl = collect($masterSlsMapping)->pluck('ppl')->unique()->count();
                $this->petugasData = ['pml' => $uniquePml, 'ppl' => $uniquePpl];

                $this->errorMessage = '';
            } else {
                $this->rekap = ['total_submitted' => 0, 'total_open' => 0, 'total_target' => 0];
                $this->tableData = [];
                $this->petugasData = ['pml' => 0, 'ppl' => 0];
            }
        } catch (Exception $e) {
            $this->errorMessage = "Gagal memproses data: " . $e->getMessage();
            $this->loadMockData();
        }
    }

    private function resolveFilePath($filename)
    {
        $paths = [storage_path('app/' . $filename), storage_path('app/' . strtoupper($filename))];
        foreach ($paths as $path) {
            if (file_exists($path)) return $path;
        }
        return null;
    }

    private function cleanHeaders($headers)
    {
        if (!is_array($headers)) return [];
        return array_map(function ($h) {
            return trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $h));
        }, $headers);
    }

    private function findKeyIndex($needles, $haystack)
    {
        foreach ($needles as $needle) {
            $index = array_search($needle, $haystack);
            if ($index !== FALSE) return $index;
            $index = array_search(strtolower($needle), array_map('strtolower', $haystack));
            if ($index !== FALSE) return $index;
        }
        return null; // Diubah dari 0 ke null agar tidak salah deteksi kolom pertama jika tidak ketemu
    }

    private function loadMockData()
    {
        $this->tableData = [
            ['label_utama' => '3205200008001000', 'label_sls' => 'RT 01', 'desa' => 'SUKAMULYA', 'kecamatan' => 'KARANGPAWITAN', 'open' => 10, 'submit' => 40, 'beban' => 50]
        ];
    }

    public function render()
    {
        return view('livewire.dashboard-performance');
    }
}
