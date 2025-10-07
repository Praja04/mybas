<?php

namespace App\Http\Controllers\System5R;

use Illuminate\Http\Request;
use App\Models\System5R\Jadwal;
use App\Models\System5R\Jawaban;
use App\Models\System5R\Periode;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\System5R\MasterGroup;
use App\Models\System5R\JawabanGroup;
use App\Models\System5R\MasterWorkspace;
use App\Models\System5R\MasterDepartment;
use App\Models\System5R\MasterGroupJuriDepartment;

class DashboardController extends Controller
{


    
// public function index()
// {
   

//     $workspace = MasterWorkspace::get();
//     $allJadwal = Jadwal::all();

//     // Ambil semua departemen aktif
//     $allDepartments = collect($workspace)->flatMap(function ($workspaceItem) {
//         return $workspaceItem->departments->where('is_active', 'Y');
//     })->unique('id_department')->values();

//     $departmentLabels = [];
//     $departmentValues = [];

//     $departmentPeriodLabels = []; 
//     $departmentPeriodValues = []; 

//     if (isset($_GET['filter_jadwal'])) {
//         $selectedDepartment = $_GET['filter_department'] ?? null;

//         $workspace = $workspace->map(function ($_item) use ($selectedDepartment) {
//             $_data = $_item->departments
//                 ->where('is_active', 'Y')
//                 ->when($selectedDepartment, function ($query) use ($selectedDepartment) {
//                     return $query->where('id_department', $selectedDepartment);
//                 })
//                 ->map(function ($item) {
//                     $periode = Periode::where('id_jadwal', $_GET['filter_jadwal'])->get();
//                     $id_department = $item->id_department;

//                     $periode = $periode->map(function ($_item, $key) use ($id_department) {
//                         $__group = MasterGroup::where('id_department', $id_department)->get();

//                         $id_periode = $_item->id_periode;

//                         $_group = $__group->map(function ($__item, $key) use ($id_periode) {
//                             $__item->jawabanGroup = JawabanGroup::where('id_group', $__item->id_group)
//                                 ->where('id_periode', $id_periode)
//                                 ->first();

//                             $totalNilai = 0;

//                             if ($__item->jawabanGroup != null) {
//                                 $jawaban = Jawaban::where('id_jawaban_group', $__item->jawabanGroup->id_jawaban_group)->get();

//                                 $nilaiDasar = $jawaban->sum('nilai');

//                                 // Menentukan nilai maksimum berdasarkan status digitalisasi
//                                 $nilaiMaksimal = ($__item->is_digitalisasi == 'Y') ? 72 : 60;

//                                 // Normalisasi ke skala 100
//                                 $totalNilai = ($nilaiDasar / $nilaiMaksimal) * 100;
//                             }

//                             $__item->totalNilai = round($totalNilai, 2); // Bulatkan untuk kemudahan tampilan

//                             return $__item;
//                         });

//                         $_group = $_group->filter(function ($__item, $key) {
//                             return $__item->jawabanGroup != null && $__item->jawabanGroup->status == 'approved';
//                         });

//                         $_item->group = $_group;
//                         $_item->totalNilai = round($_group->avg('totalNilai'), 2); // Rata-rata per periode

//                         return $_item;
//                     });

//                     $item->periode = $periode;

//                     $approvedPeriode = $item->periode->where('totalNilai', '>', 0);

//                     $item->__total = $approvedPeriode->count() > 0
//                         ? round($approvedPeriode->avg('totalNilai'), 2)
//                         : 0;

//                     return $item;
//                 });

//             $_item->departments = $_data;
//             return $_item;
//         });

//         // Data grafik pertama: rata-rata nilai per departemen
//         foreach ($workspace as $_item) {
//             foreach ($_item->departments as $dept) {
//                 if (isset($dept->__total) && $dept->__total > 0) {
//                     $departmentLabels[] = $dept->id_department;
//                     $departmentValues[] = $dept->__total;
//                 }
//             }
//         }

//         // Data grafik kedua: nilai per periode untuk departemen terpilih
//         if ($selectedDepartment) {
//             foreach ($workspace as $_item) {
//                 foreach ($_item->departments as $dept) {
//                     if ($dept->id_department === $selectedDepartment && $dept->periode->isNotEmpty()) {
//                         $departmentPeriodLabels = $dept->periode->pluck('nama_periode'); // Label periode
//                         $departmentPeriodValues = $dept->periode->pluck('totalNilai'); // Nilai-nilai
//                     }
//                 }
//             }
//         }
//     }

//     return view('system5r.dashboard.index', compact(
//         'workspace',
//         'allJadwal',
//         'allDepartments',
//         'departmentLabels',
//         'departmentValues',
//         'departmentPeriodLabels',
//         'departmentPeriodValues'
//     ));
// }


// public function index()
// {
//     $workspace = MasterWorkspace::get();
//     $allJadwal = Jadwal::all();

//     // Ambil semua departemen aktif
//     $allDepartments = collect($workspace)->flatMap(function ($workspaceItem) {
//         return $workspaceItem->departments->where('is_active', 'Y');
//     })->unique('id_department')->values();

//     $departmentLabels = [];
//     $departmentValues = [];
//     $departmentPeriodLabels = []; 
//     $departmentPeriodValues = []; 

//     if (isset($_GET['filter_jadwal'])) {
//         $selectedDepartment = $_GET['filter_department'] ?? null;
//         $selectedJadwal = $_GET['filter_jadwal'];
        
//         // Ambil data jadwal untuk menentukan tahun
//         $jadwalData = Jadwal::find($selectedJadwal);
//         $tahunJadwal = $jadwalData ? date('Y', strtotime($jadwalData->tanggal_mulai)) : date('Y');

//         $workspace = $workspace->map(function ($_item) use ($selectedDepartment, $selectedJadwal, $tahunJadwal) {
//             $_data = $_item->departments
//                 ->where('is_active', 'Y')
//                 ->when($selectedDepartment, function ($query) use ($selectedDepartment) {
//                     return $query->where('id_department', $selectedDepartment);
//                 })
//                 ->map(function ($item) use ($selectedJadwal, $tahunJadwal) {
//                     $periode = Periode::where('id_jadwal', $selectedJadwal)->get();
//                     $id_department = $item->id_department;

//                     $periode = $periode->map(function ($_item, $key) use ($id_department, $tahunJadwal) {
//                         $__group = MasterGroup::where('id_department', $id_department)->get();
//                         $id_periode = $_item->id_periode;

//                         $_group = $__group->map(function ($__item, $key) use ($id_periode, $tahunJadwal) {
//                             $__item->jawabanGroup = JawabanGroup::where('id_group', $__item->id_group)
//                                 ->where('id_periode', $id_periode)
//                                 ->first();

//                             $totalNilai = 0;

//                             if ($__item->jawabanGroup != null) {
//                                 $jawaban = Jawaban::where('id_jawaban_group', $__item->jawabanGroup->id_jawaban_group)->get();
                                
//                                 // Tentukan perhitungan berdasarkan tahun
//                                 if ($tahunJadwal >= 2025) {
//                                     // Perhitungan untuk 2025 ke atas (normalisasi ke skala 100)
//                                     $nilaiDasar = $jawaban->sum('nilai');
                                    
//                                     // Menentukan nilai maksimum berdasarkan status digitalisasi
//                                     $nilaiMaksimal = ($__item->is_digitalisasi == 'Y') ? 72 : 60;
                                    
//                                     // Normalisasi ke skala 100
//                                     $totalNilai = ($nilaiDasar / $nilaiMaksimal) * 100;
//                                 } else {
//                                     // Perhitungan untuk 2023-2024 (persentase langsung)
//                                     $totalNilai = $jawaban->sum('nilai') * ($__item->persentase / 100);
//                                 }
//                             }

//                             $__item->totalNilai = round($totalNilai, 2);
//                             return $__item;
//                         });

//                         // Filter hanya yang approved
//                         $_group = $_group->filter(function ($__item, $key) {
//                             return $__item->jawabanGroup != null && $__item->jawabanGroup->status == 'approved';
//                         });

//                         $_item->group = $_group;
                        
//                         // Perhitungan total per periode berdasarkan tahun
//                         if ($tahunJadwal >= 2025) {
//                             // Untuk 2025: rata-rata nilai grup
//                             $_item->totalNilai = $_group->count() > 0 ? round($_group->avg('totalNilai'), 2) : 0;
//                         } else {
//                             // Untuk 2023-2024: jumlah nilai grup
//                             $_item->totalNilai = round($_group->sum('totalNilai'), 2);
//                         }

//                         return $_item;
//                     });

//                     $item->periode = $periode;

//                     // Perhitungan total departemen berdasarkan tahun
//                     $approvedPeriode = $item->periode->where('totalNilai', '>', 0);
                    
//                     if ($tahunJadwal >= 2025) {
//                         // Untuk 2025: rata-rata dari periode yang approved
//                         $item->__total = $approvedPeriode->count() > 0
//                             ? round($approvedPeriode->avg('totalNilai'), 2)
//                             : 0;
//                     } else {
//                         // Untuk 2023-2024: rata-rata dari periode yang tidak nol
//                         $nonZeroPeriode = $item->periode->where('totalNilai', '!=', 0);
//                         $item->__total = $nonZeroPeriode->count() > 0
//                             ? round($item->periode->sum('totalNilai') / $nonZeroPeriode->count(), 2)
//                             : 0;
//                     }

//                     return $item;
//                 });

//             $_item->departments = $_data;
//             return $_item;
//         });

//         // Data grafik pertama: rata-rata nilai per departemen
//         foreach ($workspace as $_item) {
//             foreach ($_item->departments as $dept) {
//                 if (isset($dept->__total) && $dept->__total > 0) {
//                     $departmentLabels[] = $dept->id_department;
//                     $departmentValues[] = $dept->__total;
//                 }
//             }
//         }

//         // Data grafik kedua: nilai per periode untuk departemen terpilih
//         if ($selectedDepartment) {
//             foreach ($workspace as $_item) {
//                 foreach ($_item->departments as $dept) {
//                     if ($dept->id_department === $selectedDepartment && $dept->periode->isNotEmpty()) {
//                         $departmentPeriodLabels = $dept->periode->pluck('nama_periode')->toArray();
//                         $departmentPeriodValues = $dept->periode->pluck('totalNilai')->toArray();
//                     }
//                 }
//             }
//         }
//     }

//     return view('system5r.dashboard.index', compact(
//         'workspace',
//         'allJadwal',
//         'allDepartments',
//         'departmentLabels',
//         'departmentValues',
//         'departmentPeriodLabels',
//         'departmentPeriodValues'
//     ));

// }



// PERHITUNGAN UNUTK NILAI PER PERIODE
public function index()
{
    $workspace = MasterWorkspace::get();
    $allJadwal = Jadwal::all();

    // Ambil semua departemen aktif
    $allDepartments = collect($workspace)->flatMap(function ($workspaceItem) {
        return $workspaceItem->departments->where('is_active', 'Y');
    })->unique('id_department')->values();

    $departmentLabels = [];
    $departmentValues = [];
    $departmentPeriodLabels = []; 
    $departmentPeriodValues = []; 

    if (isset($_GET['filter_jadwal'])) {
        $selectedDepartment = $_GET['filter_department'] ?? null;
        $selectedJadwal = $_GET['filter_jadwal'];
        
        // Ambil data jadwal untuk menentukan tahun
        $jadwalData = Jadwal::find($selectedJadwal);
        
        // Debug: Cek apakah jadwal ditemukan
        if (!$jadwalData) {
            dd("Jadwal tidak ditemukan dengan ID: " . $selectedJadwal, "Available Jadwal:", Jadwal::all());
        }
        
        // Tentukan tahun berdasarkan ID jadwal atau field lain yang tersedia
        $tahunJadwal = $this->getTahunFromJadwal($jadwalData);
        
        // Debug: Log untuk melihat tahun yang terdeteksi
        // dd("Tahun Jadwal: " . $tahunJadwal, "Jadwal Data: ", $jadwalData);

        $workspace = $workspace->map(function ($_item) use ($selectedDepartment, $selectedJadwal, $tahunJadwal) {
            $_data = $_item->departments
                ->where('is_active', 'Y')
                ->when($selectedDepartment, function ($query) use ($selectedDepartment) {
                    return $query->where('id_department', $selectedDepartment);
                })
                ->map(function ($item) use ($selectedJadwal, $tahunJadwal) {
                    $periode = Periode::where('id_jadwal', $selectedJadwal)->get();
                    $id_department = $item->id_department;

                    $periode = $periode->map(function ($_item, $key) use ($id_department, $tahunJadwal) {
                        $__group = MasterGroup::where('id_department', $id_department)->get();
                        $id_periode = $_item->id_periode;

                        $_group = $__group->map(function ($__item, $key) use ($id_periode, $tahunJadwal) {
                            $__item->jawabanGroup = JawabanGroup::where('id_group', $__item->id_group)
                                ->where('id_periode', $id_periode)
                                ->first();

                            $totalNilai = 0;

                            if ($__item->jawabanGroup != null) {
                                $jawaban = Jawaban::where('id_jawaban_group', $__item->jawabanGroup->id_jawaban_group)->get();
                                
                                // Tentukan perhitungan berdasarkan tahun
                                if ($tahunJadwal >= 2025) {
                                    // Perhitungan untuk 2025 ke atas (normalisasi ke skala 100 + bobot)
                                    if ($__item->jawabanGroup->status == 'approved') {
                                        $nilaiDasar = $jawaban->sum('nilai');
                                        
                                        // Menentukan nilai maksimum berdasarkan status digitalisasi
                                        $nilaiMaksimal = ($__item->is_digitalisasi == 'Y') ? 72 : 60;
                                        
                                        // Hitung normalisasi: (nilaiDasar / nilaiMaksimal) × 100 × bobot
                                        $normalisasi = ($nilaiDasar / max($nilaiMaksimal, 1)) * 100;
                                        $totalNilai = $normalisasi * ($__item->persentase / 100); // Terapkan bobot
                                        
                                        // Debug: uncomment untuk melihat detail perhitungan
                                        // error_log("Grup ID: {$__item->id_group}, Nilai Dasar: $nilaiDasar, Nilai Maksimal: $nilaiMaksimal, Normalisasi: $normalisasi, Persentase: {$__item->persentase}%, Total: $totalNilai");
                                    }
                                } else {
                                    // Perhitungan untuk 2023-2024 (persentase langsung)
                                    $totalNilai = $jawaban->sum('nilai') * ($__item->persentase / 100);
                                }
                            }

                            $__item->totalNilai = round($totalNilai, 2);
                            return $__item;
                        });

                        // Filter hanya yang approved
                        $_group = $_group->filter(function ($__item, $key) {
                            return $__item->jawabanGroup != null && $__item->jawabanGroup->status == 'approved';
                        });

                        $_item->group = $_group;
                        
                        // Perhitungan total per periode berdasarkan tahun
                        if ($tahunJadwal >= 2025) {
                            // Untuk 2025: jumlah dari semua nilai grup (bukan rata-rata)
                            $_item->totalNilai = round($_group->sum('totalNilai'), 2);
                        } else {
                            // Untuk 2023-2024: jumlah nilai grup
                            $_item->totalNilai = round($_group->sum('totalNilai'), 2);
                        }

                        return $_item;
                    });

                    $item->periode = $periode;

                    // Perhitungan total departemen berdasarkan tahun
                    if ($tahunJadwal >= 2025) {
                        // Untuk 2025: rata-rata dari periode yang approved
                        $approvedPeriode = $item->periode->where('totalNilai', '>', 0);
                        $item->__total = $approvedPeriode->count() > 0
                            ? round($approvedPeriode->avg('totalNilai'), 2)
                            : 0;
                    } else {
                        // Untuk 2023-2024: rata-rata dari periode yang tidak nol
                        $nonZeroPeriode = $item->periode->where('totalNilai', '!=', 0);
                        $item->__total = $nonZeroPeriode->count() > 0
                            ? round($item->periode->sum('totalNilai') / $nonZeroPeriode->count(), 2)
                            : 0;
                    }

                    return $item;
                });

            $_item->departments = $_data;
            return $_item;
        });

        // Data grafik pertama: rata-rata nilai per departemen
        foreach ($workspace as $_item) {
            foreach ($_item->departments as $dept) {
                if (isset($dept->__total) && $dept->__total > 0) {
                    $departmentLabels[] = $dept->id_department;
                    $departmentValues[] = $dept->__total;
                }
            }
        }

        // Data grafik kedua: nilai per periode untuk departemen terpilih
        if ($selectedDepartment) {
            foreach ($workspace as $_item) {
                foreach ($_item->departments as $dept) {
                    if ($dept->id_department === $selectedDepartment && $dept->periode->isNotEmpty()) {
                        $departmentPeriodLabels = $dept->periode->pluck('nama_periode')->toArray();
                        $departmentPeriodValues = $dept->periode->pluck('totalNilai')->toArray();
                    }
                }
            }
        }
    }

    return view('system5r.dashboard.index', compact(
        'workspace',
        'allJadwal',
        'allDepartments',
        'departmentLabels',
        'departmentValues',
        'departmentPeriodLabels',
        'departmentPeriodValues'
    ));
}

/**
 * Tentukan tahun berdasarkan data jadwal
 */
private function getTahunFromJadwal($jadwalData)
{
    // Coba ekstrak tahun dari ID jadwal
    if (preg_match('/J(\d{4})/', $jadwalData->id_jadwal, $matches)) {
        return (int)$matches[1];
    }
    
    // Jika ada field tanggal_mulai
    if (isset($jadwalData->tanggal_mulai) && $jadwalData->tanggal_mulai) {
        return (int)date('Y', strtotime($jadwalData->tanggal_mulai));
    }
    
    // Jika ada field created_at
    if (isset($jadwalData->created_at) && $jadwalData->created_at) {
        return (int)date('Y', strtotime($jadwalData->created_at));
    }
    
    // Fallback ke tahun sekarang
    return (int)date('Y');
}
    
}
