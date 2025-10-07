<?php

namespace App\Http\Controllers\System5R\Report;

use App\Http\Controllers\Controller;
use App\Models\System5R\DepartmentComittee;
use App\Models\System5R\Jadwal;
use App\Models\System5R\Jawaban;
use App\Models\System5R\JawabanGroup;
use App\Models\System5R\MasterDepartment;
use App\Models\System5R\MasterGroup;
use App\Models\System5R\MasterGroupJuriDepartment;
use App\Models\System5R\MasterWorkspace;
use App\Models\System5R\Periode;
use Illuminate\Http\Request;

class CommitteeController extends Controller
{
    // public function index()
    // {
    //     $myDepartment = DepartmentComittee::where('nik_committee', auth()->user()->username)->get();

    //     if($myDepartment->count() == 0) {
    //         $workspace = null;
    //     }else{
    //         // $data = MasterDepartment::whereIn('id_department', 
    //         // $myDepartment->pluck('id_department')->toArray()
    //         // )->get();


    //         $workspace = MasterWorkspace::whereHas('departments', function ($query) use ($myDepartment) {
    //             $query->whereIn('id_department', $myDepartment->pluck('id_department')->toArray());
    //         })->get();

    //         // Filter departments
    //         $workspace = $workspace->map(function ($_item) use ($myDepartment) {
    //             $_item->departments = $_item->departments->filter(function ($item) use ($myDepartment) {
    //                 return $myDepartment->where('id_department', $item->id_department)->count() > 0;
    //             });

    //             return $_item;
    //         });

    //         if(isset($_GET['filter_jadwal'])) {
    //             $workspace = $workspace->map(function ($_item) {
    //                 $_data = $_item->departments->map(function ($item) {
    //                     $periode = Periode::where('id_jadwal', $_GET['filter_jadwal'])->get();
    //                     $id_department = $item->id_department;
        
    //                     $periode = $periode->map(function ($_item, $key) use ($id_department) {
    //                         $__group = MasterGroup::where('id_department', $id_department)->get();
        
    //                         $id_periode = $_item->id_periode;
        
    //                         $_group = $__group->map(function ($__item, $key) use ($id_periode) {
    //                             $__item->jawabanGroup = JawabanGroup::where('id_group', $__item->id_group)
    //                             ->where('id_periode', $id_periode)
    //                             ->first();
        
    //                             $totalNilai = 0;
                                
    //                             if($__item->jawabanGroup != null) {
    //                                 $jawaban = Jawaban::where('id_jawaban_group', $__item->jawabanGroup->id_jawaban_group)->get();
                                    
    //                                 $totalNilai = $jawaban->sum('nilai')*($__item->persentase/100);
    //                             }
        
    //                             $__item->totalNilai = $totalNilai;
        
    //                             return $__item;
    //                         });
        
    //                         $juri_periode = MasterGroupJuriDepartment::where('id_department', $id_department)
    //                         ->where('id_periode', $id_periode)
    //                         ->with('group.anggota')
    //                         ->first();
        
    //                         // dd($juri_periode->group->anggota);
        
    //                         if($juri_periode != null) {
    //                             $juri_array = $juri_periode->group->anggota->map(function ($item, $key) {
    //                                 return $item->nama_juri;
    //                             })->toArray();
                                
    //                             $_item->juri = $juri_array;
    //                         }else{
    //                             $_item->juri = [];
    //                         }
        
        
    //                         $_group = $_group->filter(function ($__item, $key) {
    //                             return $__item->jawabanGroup != null && $__item->jawabanGroup->status == 'approved';
    //                         });
        
    //                         $_item->group = $_group;
    //                         $_item->totalNilai = $_group->sum('totalNilai');
        
    //                         return $_item;
    //                     });
        
    //                     $item->periode = $periode;
        
    //                     if($item->periode->where('totalNilai', '!=', '0')->count() == 0) {
    //                         $__total = 0;
    //                     }else{
    //                         $__total = $item->periode->sum('totalNilai')/$item->periode->where('totalNilai', '!=', '0')->count();
    //                     }
        
    //                     $item->__total = $__total;
        
    //                     return $item;
    //                 });

    //                 $_item->departments = $_data;
    //                 return $_item;
    //             });
    //         }

    //     }

    //     $allJadwal = Jadwal::all();

    //     return view('system5r.report.management.index', compact('workspace', 'allJadwal'));
    // }
    // public function index()
    // {
    //     $myDepartment = DepartmentComittee::where('nik_committee', auth()->user()->username)->get();

    //     if ($myDepartment->count() == 0) {
    //         $workspace = null;
    //     } else {
    //         $workspace = MasterWorkspace::whereHas('departments', function ($query) use ($myDepartment) {
    //             $query->whereIn('id_department', $myDepartment->pluck('id_department')->toArray());
    //         })->get();

    //         // Filter departments
    //         $workspace = $workspace->map(function ($_item) use ($myDepartment) {
    //             $_item->departments = $_item->departments->filter(function ($item) use ($myDepartment) {
    //                 return $myDepartment->where('id_department', $item->id_department)->count() > 0;
    //             });

    //             return $_item;
    //         });

    //         if (isset($_GET['filter_jadwal'])) {
    //             $workspace = $workspace->map(function ($_item) {
    //                 $_data = $_item->departments->map(function ($item) {
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

    //                             if ($__item->jawabanGroup && $__item->jawabanGroup->status == 'approved') {
    //                                 $jawaban = Jawaban::where('id_jawaban_group', $__item->jawabanGroup->id_jawaban_group)->get();

    //                                 // Jumlah nilai jawaban
    //                                 $nilaiDasar = $jawaban->sum('nilai');

    //                                 // Tentukan nilai maksimum
    //                                 $nilaiMaksimal = ($__item->is_digitalisasi == 'Y') ? 72 : 60;

    //                                 // Hitung normalisasi: (nilaiDasar / nilaiMaksimal) × 100 × bobot
    //                                 $totalNilai = ($nilaiDasar / max($nilaiMaksimal, 1)) * 100;
    //                                 $totalNilai = $totalNilai * ($__item->persentase / 100); // Terapkan bobot
    //                             }

    //                             $__item->totalNilai = round($totalNilai, 2); // Bulatkan untuk tampilan

    //                             return $__item;
    //                         });

    //                         // Ambil juri
    //                         $juri_periode = MasterGroupJuriDepartment::where('id_department', $id_department)
    //                             ->where('id_periode', $id_periode)
    //                             ->with('group.anggota')
    //                             ->first();

    //                         $_item->juri = $juri_periode
    //                             ? $juri_periode->group->anggota->pluck('nama_juri')->toArray()
    //                             : [];

    //                         // Filter hanya group yang disetujui
    //                         $_group = $_group->filter(fn($g) => $g->jawabanGroup && $g->jawabanGroup->status == 'approved');

    //                         // Simpan grup dan total nilai
    //                         $_item->group = $_group;
    //                         $_item->totalNilai = round($_group->sum('totalNilai'), 2);

    //                         return $_item;
    //                     });

    //                     $item->periode = $periode;

    //                     // Hitung rata-rata dari periode yang bukan nol
    //                     $approvedPeriode = $item->periode->where('totalNilai', '>', 0);
    //                     $item->__total = $approvedPeriode->count() > 0
    //                         ? round($approvedPeriode->avg('totalNilai'), 2)
    //                         : 0;

    //                     return $item;
    //                 });

    //                 $_item->departments = $_data;
    //                 return $_item;
    //             });
    //         }
    //     }

    //     $allJadwal = Jadwal::all();

    //     return view('system5r.report.management.index', compact('workspace', 'allJadwal'));
    // }


    //  public function pendamping()
    // {
    //     $myDepartment = DepartmentComittee::where('nik_committee', auth()->user()->username)->get();

    //     if ($myDepartment->count() == 0) {
    //         $workspace = null;
    //     } else {
    //         $workspace = MasterWorkspace::whereHas('departments', function ($query) use ($myDepartment) {
    //             $query->whereIn('id_department', $myDepartment->pluck('id_department')->toArray());
    //         })->get();

    //         // Filter departments
    //         $workspace = $workspace->map(function ($_item) use ($myDepartment) {
    //             $_item->departments = $_item->departments->filter(function ($item) use ($myDepartment) {
    //                 return $myDepartment->where('id_department', $item->id_department)->count() > 0;
    //             });

    //             return $_item;
    //         });

    //         if (isset($_GET['filter_jadwal'])) {
    //             $workspace = $workspace->map(function ($_item) {
    //                 $_data = $_item->departments->map(function ($item) {
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

    //                             if ($__item->jawabanGroup && $__item->jawabanGroup->status == 'approved') {
    //                                 $jawaban = Jawaban::where('id_jawaban_group', $__item->jawabanGroup->id_jawaban_group)->get();

    //                                 // Jumlah nilai jawaban
    //                                 $nilaiDasar = $jawaban->sum('nilai');

    //                                 // Tentukan nilai maksimum
    //                                 $nilaiMaksimal = ($__item->is_digitalisasi == 'Y') ? 72 : 60;

    //                                 // Hitung normalisasi: (nilaiDasar / nilaiMaksimal) × 100 × bobot
    //                                 $totalNilai = ($nilaiDasar / max($nilaiMaksimal, 1)) * 100;
    //                                 $totalNilai = $totalNilai * ($__item->persentase / 100); // Terapkan bobot
    //                             }

    //                             $__item->totalNilai = round($totalNilai, 2); // Bulatkan untuk tampilan

    //                             return $__item;
    //                         });

    //                         // Ambil juri
    //                         $juri_periode = MasterGroupJuriDepartment::where('id_department', $id_department)
    //                             ->where('id_periode', $id_periode)
    //                             ->with('group.anggota')
    //                             ->first();

    //                         $_item->juri = $juri_periode
    //                             ? $juri_periode->group->anggota->pluck('nama_juri')->toArray()
    //                             : [];

    //                         // Filter hanya group yang disetujui
    //                         $_group = $_group->filter(fn($g) => $g->jawabanGroup && $g->jawabanGroup->status == 'approved');

    //                         // Simpan grup dan total nilai
    //                         $_item->group = $_group;
    //                         $_item->totalNilai = round($_group->sum('totalNilai'), 2);

    //                         return $_item;
    //                     });

    //                     $item->periode = $periode;

    //                     // Hitung rata-rata dari periode yang bukan nol
    //                     $approvedPeriode = $item->periode->where('totalNilai', '>', 0);
    //                     $item->__total = $approvedPeriode->count() > 0
    //                         ? round($approvedPeriode->avg('totalNilai'), 2)
    //                         : 0;

    //                     return $item;
    //                 });

    //                 $_item->departments = $_data;
    //                 return $_item;
    //             });
    //         }
    //     }

    //     $allJadwal = Jadwal::all();

    //     return view('system5r.report.management.pendamping', compact('workspace', 'allJadwal'));
    // }


    public function index()
{
    $myDepartment = DepartmentComittee::where('nik_committee', auth()->user()->username)->get();

    if ($myDepartment->count() == 0) {
        $workspace = null;
    } else {
        $workspace = MasterWorkspace::whereHas('departments', function ($query) use ($myDepartment) {
            $query->whereIn('id_department', $myDepartment->pluck('id_department')->toArray());
        })->get();

        // Filter departments
        $workspace = $workspace->map(function ($_item) use ($myDepartment) {
            $_item->departments = $_item->departments->filter(function ($item) use ($myDepartment) {
                return $myDepartment->where('id_department', $item->id_department)->count() > 0;
            });

            return $_item;
        });

        if (isset($_GET['filter_jadwal'])) {
            // Ambil data jadwal untuk menentukan tahun
            $selectedJadwal = $_GET['filter_jadwal'];
            $jadwalData = Jadwal::find($selectedJadwal);
            
            // Tentukan tahun berdasarkan jadwal
            $tahunJadwal = $this->getTahunFromJadwal($jadwalData);
            
            $workspace = $workspace->map(function ($_item) use ($tahunJadwal) {
                $_data = $_item->departments->map(function ($item) use ($tahunJadwal) {
                    $periode = Periode::where('id_jadwal', $_GET['filter_jadwal'])->get();
                    $id_department = $item->id_department;

                    $periode = $periode->map(function ($_item, $key) use ($id_department, $tahunJadwal) {
                        $__group = MasterGroup::where('id_department', $id_department)->get();
                        $id_periode = $_item->id_periode;

                        $_group = $__group->map(function ($__item, $key) use ($id_periode, $tahunJadwal) {
                            $__item->jawabanGroup = JawabanGroup::where('id_group', $__item->id_group)
                                ->where('id_periode', $id_periode)
                                ->first();

                            $totalNilai = 0;

                            if ($__item->jawabanGroup && $__item->jawabanGroup->status == 'approved') {
                                $jawaban = Jawaban::where('id_jawaban_group', $__item->jawabanGroup->id_jawaban_group)->get();

                                // Tentukan perhitungan berdasarkan tahun
                                if ($tahunJadwal >= 2025) {
                                    // Perhitungan untuk 2025 ke atas (normalisasi ke skala 100 + bobot)
                                    $nilaiDasar = $jawaban->sum('nilai');
                                    
                                    // Tentukan nilai maksimum berdasarkan status digitalisasi
                                    $nilaiMaksimal = ($__item->is_digitalisasi == 'Y') ? 72 : 60;
                                    
                                    // Hitung normalisasi: (nilaiDasar / nilaiMaksimal) × 100 × bobot
                                    $normalisasi = ($nilaiDasar / max($nilaiMaksimal, 1)) * 100;
                                    $totalNilai = $normalisasi * ($__item->persentase / 100); // Terapkan bobot
                                } else {
                                    // Perhitungan untuk 2023-2024 (persentase langsung)
                                    $totalNilai = $jawaban->sum('nilai') * ($__item->persentase / 100);
                                }
                            }

                            $__item->totalNilai = round($totalNilai, 2); // Bulatkan untuk tampilan

                            return $__item;
                        });

                        // Ambil juri
                        $juri_periode = MasterGroupJuriDepartment::where('id_department', $id_department)
                            ->where('id_periode', $id_periode)
                            ->with('group.anggota')
                            ->first();

                        $_item->juri = $juri_periode
                            ? $juri_periode->group->anggota->pluck('nama_juri')->toArray()
                            : [];

                        // Filter hanya group yang disetujui
                        $_group = $_group->filter(fn($g) => $g->jawabanGroup && $g->jawabanGroup->status == 'approved');

                        // Simpan grup dan total nilai
                        $_item->group = $_group;
                        $_item->totalNilai = round($_group->sum('totalNilai'), 2);

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
        }
    }

    $allJadwal = Jadwal::all();

    return view('system5r.report.management.index', compact('workspace', 'allJadwal'));
}

public function pendamping()
{
    $myDepartment = DepartmentComittee::where('nik_committee', auth()->user()->username)->get();

    if ($myDepartment->count() == 0) {
        $workspace = null;
    } else {
        $workspace = MasterWorkspace::whereHas('departments', function ($query) use ($myDepartment) {
            $query->whereIn('id_department', $myDepartment->pluck('id_department')->toArray());
        })->get();

        // Filter departments
        $workspace = $workspace->map(function ($_item) use ($myDepartment) {
            $_item->departments = $_item->departments->filter(function ($item) use ($myDepartment) {
                return $myDepartment->where('id_department', $item->id_department)->count() > 0;
            });

            return $_item;
        });

        if (isset($_GET['filter_jadwal'])) {
            // Ambil data jadwal untuk menentukan tahun
            $selectedJadwal = $_GET['filter_jadwal'];
            $jadwalData = Jadwal::find($selectedJadwal);
            
            // Tentukan tahun berdasarkan jadwal
            $tahunJadwal = $this->getTahunFromJadwal($jadwalData);
            
            $workspace = $workspace->map(function ($_item) use ($tahunJadwal) {
                $_data = $_item->departments->map(function ($item) use ($tahunJadwal) {
                    $periode = Periode::where('id_jadwal', $_GET['filter_jadwal'])->get();
                    $id_department = $item->id_department;

                    $periode = $periode->map(function ($_item, $key) use ($id_department, $tahunJadwal) {
                        $__group = MasterGroup::where('id_department', $id_department)->get();
                        $id_periode = $_item->id_periode;

                        $_group = $__group->map(function ($__item, $key) use ($id_periode, $tahunJadwal) {
                            $__item->jawabanGroup = JawabanGroup::where('id_group', $__item->id_group)
                                ->where('id_periode', $id_periode)
                                ->first();

                            $totalNilai = 0;

                            if ($__item->jawabanGroup && $__item->jawabanGroup->status == 'approved') {
                                $jawaban = Jawaban::where('id_jawaban_group', $__item->jawabanGroup->id_jawaban_group)->get();

                                // Tentukan perhitungan berdasarkan tahun
                                if ($tahunJadwal >= 2025) {
                                    // Perhitungan untuk 2025 ke atas (normalisasi ke skala 100 + bobot)
                                    $nilaiDasar = $jawaban->sum('nilai');
                                    
                                    // Tentukan nilai maksimum berdasarkan status digitalisasi
                                    $nilaiMaksimal = ($__item->is_digitalisasi == 'Y') ? 72 : 60;
                                    
                                    // Hitung normalisasi: (nilaiDasar / nilaiMaksimal) × 100 × bobot
                                    $normalisasi = ($nilaiDasar / max($nilaiMaksimal, 1)) * 100;
                                    $totalNilai = $normalisasi * ($__item->persentase / 100); // Terapkan bobot
                                } else {
                                    // Perhitungan untuk 2023-2024 (persentase langsung)
                                    $totalNilai = $jawaban->sum('nilai') * ($__item->persentase / 100);
                                }
                            }

                            $__item->totalNilai = round($totalNilai, 2); // Bulatkan untuk tampilan

                            return $__item;
                        });

                        // Ambil juri
                        $juri_periode = MasterGroupJuriDepartment::where('id_department', $id_department)
                            ->where('id_periode', $id_periode)
                            ->with('group.anggota')
                            ->first();

                        $_item->juri = $juri_periode
                            ? $juri_periode->group->anggota->pluck('nama_juri')->toArray()
                            : [];

                        // Filter hanya group yang disetujui
                        $_group = $_group->filter(fn($g) => $g->jawabanGroup && $g->jawabanGroup->status == 'approved');

                        // Simpan grup dan total nilai
                        $_item->group = $_group;
                        $_item->totalNilai = round($_group->sum('totalNilai'), 2);

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
        }
    }

    $allJadwal = Jadwal::all();

    return view('system5r.report.management.pendamping', compact('workspace', 'allJadwal'));
}

// Helper method untuk mendapatkan tahun dari jadwal
private function getTahunFromJadwal($jadwalData)
{
    // Implementasikan logika untuk mendapatkan tahun dari data jadwal
    // Contoh implementasi - sesuaikan dengan struktur data Anda
    if ($jadwalData) {
        // Jika ada field tahun langsung
        if (isset($jadwalData->tahun)) {
            return $jadwalData->tahun;
        }
        
        // Jika tahun ada di nama jadwal
        if (isset($jadwalData->nama_jadwal)) {
            preg_match('/(\d{4})/', $jadwalData->nama_jadwal, $matches);
            if (!empty($matches)) {
                return intval($matches[1]);
            }
        }
        
        // Jika tahun ada di created_at atau updated_at
        if (isset($jadwalData->created_at)) {
            return intval(date('Y', strtotime($jadwalData->created_at)));
        }
    }
    
    // Default return jika tidak ditemukan
    return 2025; // atau sesuaikan dengan kebutuhan
}
}
