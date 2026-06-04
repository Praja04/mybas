<?php
namespace App\Http\Controllers\HRConnect;

use App\HrKaryawan;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

class ReportKaryawanMasukController extends Controller
{
    public function index()
    {
        $data['title'] = 'Report Karyawan Masuk';

        $data['kodeDivisi'] = HrKaryawan::whereNotNull('kode_divisi')->where('kode_divisi', '!=', '')->distinct()->pluck('kode_divisi')->toArray();
        $data['kodeBagian'] = HrKaryawan::whereNotNull('kode_bagian')->where('kode_bagian', '!=', '')->distinct()->pluck('kode_bagian')->toArray();
        $data['kodeGroup']  = HrKaryawan::whereNotNull('kode_group')->where('kode_group', '!=', '')->distinct()->pluck('kode_group')->toArray();

        // Bersih! Karena kolomnya ENUM, kita langsung tarik aja dengan aman
        // $data['lokers'] = DB::table('loker_transaksi')
        //     ->select('kode_rak as kode_blok', 'no_loker')
        //     ->where('tipe_transaksi', 'MASUK')
        //     ->distinct()
        //     ->orderBy('kode_rak', 'asc')
        //     ->orderBy('no_loker', 'asc')
        //     ->get();

        return view('hr-connect.report.karyawan_masuk', $data);
    }

    public function getFilterBulanTahun(Request $req)
    {
        $search = $req->input('q');
        $page   = $req->input('page', 1);

        $allMonth = Cache::remember('list_bulan_karyawan_masuk_ga', now()->addHours(24), function () {
            return HrKaryawan::select(
                DB::raw("DATE_FORMAT(tanggal_masuk, '%Y-%m') as id_bulan"),
                DB::raw("DATE_FORMAT(tanggal_masuk, '%M %Y') as text")
            )
                ->where('in_kode_group', 'Y')
                ->where(function ($query) {
                    $query->where(function ($q) {
                        $q->where([
                            'in_complete' => 'Y',
                            'is_goobag'   => 'Y',
                        ]);
                    })
                        ->orWhere('p_no', 'Y');
                })
                ->whereNotNull('tanggal_masuk')
                ->where('tanggal_masuk', '!=', '0000-00-00')
                ->distinct()
                ->orderBy('id_bulan', 'desc')
                ->get()
                ->map(function ($item) {
                    return (object) [
                        'id'   => $item->id_bulan,
                        'text' => $item->text,
                    ];
                });
        });

        if (! empty($search)) {
            $allMonth = $allMonth->filter(function ($item) use ($search) {
                return stripos(strtolower($item->text), strtolower($search)) !== false;
            })->values();
        }

        $perPage = 10;
        $offset  = ($page - 1) * $perPage;
        $items   = $allMonth->slice($offset, $perPage)->values();

        return response()->json([
            'results'    => $items,
            'pagination' => [
                'more' => ($offset + $perPage) < $allMonth->count(),
            ],
        ]);
    }

    public function getData(Request $req)
    {
        $report = HrKaryawan::select(
            'id',
            'nama',
            'nik',
            'kode_divisi',
            'kode_bagian',
            'kode_group',
            'tanggal_masuk',
            'p_in',
            'p_no',
            'in_complete',
            'active'
            // 'loker_transaksi.kode_rak as kode_blok',
            // 'loker_transaksi.no_loker'
        )
            ->where('in_kode_group', 'Y')
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->where([
                        'in_complete' => 'Y',
                        'is_goobag'   => 'Y',
                    ]);
                })
                    ->orWhere('p_no', 'Y');
            })
            ->whereNotNull('tanggal_masuk')
            ->where('tanggal_masuk', '!=', '0000-00-00');
// ->orderBy('tanggal_masuk', 'desc');

        if (! empty($req->tanggal)) {
            $parts = explode('-', $req->tanggal);

            if (count($parts) == 2) {
                $report->whereYear('tanggal_masuk', $parts[0])
                    ->whereMonth('tanggal_masuk', $parts[1]);
            }
        }

        $report->orderBy('tanggal_masuk', 'desc');

        return Datatables::of($report)
            ->addColumn('status_in', function ($row) {
                if ($row->p_no == 'Y') {
                    return 'NO-IN';
                } elseif ($row->p_in == 'Y' || $row->in_complete == 'Y' || $row->active == 'Y') {
                    return 'IN';
                }

                return 'BELUM DISET';
            })
            ->make(true);
    }
}
