<?php
namespace App\Http\Controllers\HRConnect;

use App\HrKaryawan;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

class ReportKaryawanKeluarController extends Controller
{
    public function index()
    {
        $data['title'] = 'Report Karyawan Keluar';

        $data['kodeDivisi'] = HrKaryawan::whereNotNull('kode_divisi')->where('kode_divisi', '!=', '')->distinct()->pluck('kode_divisi')->toArray();
        $data['kodeBagian'] = HrKaryawan::whereNotNull('kode_bagian')->where('kode_bagian', '!=', '')->distinct()->pluck('kode_bagian')->toArray();
        $data['kodeGroup']  = HrKaryawan::whereNotNull('kode_group')->where('kode_group', '!=', '')->distinct()->pluck('kode_group')->toArray();

        // Ambil sejarah loker khusus tipe KELUAR
        // $data['lokers'] = DB::table('loker_transaksi')
        //     ->select('kode_rak as kode_blok', 'no_loker')
        //     ->where('tipe_transaksi', 'KELUAR')
        //     ->distinct()
        //     ->orderBy('kode_rak', 'asc')
        //     ->orderBy('no_loker', 'asc')
        //     ->get();

        return view('hr-connect.report.karyawan_keluar', $data);
    }

    public function getFilterBulanTahun(Request $req)
    {
        $search = $req->input('q');
        $page   = $req->input('page', 1);

        $allMonths = Cache::remember('list_bulan_karyawan_keluar_ga', now()->addHours(24), function () {
            return HrKaryawan::select(
                DB::raw("DATE_FORMAT(tgl_shift_out, '%Y-%m') as id_bulan"),
                DB::raw("DATE_FORMAT(tgl_shift_out, '%M %Y') as text")
            )
                ->where([
                    'in_complete'   => 'Y',
                    'in_kode_group' => 'Y',
                    'is_excuse_out' => 'Y',
                    'out_complete'  => 'Y',
                ])
                ->whereNotNull('tgl_shift_out')
                ->where('tgl_shift_out', '!=', '0000-00-00')
                ->distinct()
                ->orderBy('id_bulan', 'desc')
                ->get()
                ->map(
                    function ($month) {
                        return (object) [
                            'id'   => $month->id_bulan,
                            'text' => $month->text,
                        ];
                    }
                );
        });

        if (! empty($search)) {
            $allMonths = $allMonths->filter(function ($item) use ($search) {
                return stripos(strtolower($item->text), strtolower($search)) !== false;
            })->values();
        }

        $perPage = 10;
        $offset  = ($page - 1) * $perPage;
        $items   = $allMonths->slice($offset, $perPage)->values();

        return response()->json([
            'results'    => $items,
            'pagination' => [
                'more' => ($offset + $perPage) < $allMonths->count(),
            ],
        ]);
    }

    public function getData(Request $req)
    {
        $report = HrKaryawan::where([
            'in_complete'   => 'Y',
            'in_kode_group' => 'Y',
            'is_excuse_out' => 'Y',
            'out_complete'  => 'Y',
            // 'checked_ir'    => 'N',
        ])
            ->whereNotNull('tgl_shift_out')
            ->where('tgl_shift_out', '!=', '0000-00-00');
        // ->select(
        //     'id',
        //     'nama',
        //     'nik',
        //     'kode_divisi',
        //     'kode_bagian',
        //     'kode_group',
        //     'p_in',
        //     'p_no',
        //     'tgl_shift_out'
        // )
        // ->orderBy('tgl_shift_out', 'desc');

        if (! empty($req->tanggal)) {
            $parts = explode('-', $req->tanggal);

            if (count($parts) == 2) {
                $report->whereYear('tgl_shift_out', $parts[0])
                    ->whereMonth('tgl_shift_out', $parts[1]);
            }
        }

        $report->orderBy('tgl_shift_out', 'desc');

        return Datatables::of($report)
            ->addColumn('status_in', function ($row) {
                if ($row->p_no == 'Y') {
                    return 'NO-IN';
                } elseif ($row->p_in == 'Y') {
                    return 'IN';
                }
                return 'BELUM DISET';
            })
            ->addColumn('alasan_ga', function ($row) {
                // Tarik keterangan terakhir dari loker_transaksi
                $transaksi = DB::table('loker_transaksi')
                    ->where('nik', $row->nik)
                    ->where('tipe_transaksi', 'KELUAR')
                    ->orderBy('created_at', 'desc')
                    ->first();

                return $transaksi ? $transaksi->keterangan : 'Tidak ada catatan GA';
            })
            ->make(true);
    }
}
