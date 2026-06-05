<?php
namespace App\Http\Controllers\HRConnect;

use App\Http\Controllers\Controller;
use App\Imports\HRConnect\MasterReasonImport;
use App\MasterReason;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class MasterReasonController extends Controller
{
    public function index()
    {
        $data['title'] = 'Master Data - Reason S2';

        $data['list_tipe'] = MasterReason::select('tipe')
            ->distinct()
            ->whereNotNull('tipe')
            ->where('tipe', '!=', '')
            ->orderBy('tipe', 'asc')
            ->get();

        return view('hr-connect.masters.reason', $data);
    }

    public function getData()
    {
        $reason = MasterReason::orderBy('tipe', 'asc')->orderBy('kode_reason', 'asc');

        return DataTables::of($reason)
            ->addColumn('action', function ($row) {
                $btnEdit = '<button class="btn btn-sm btn-outline-primary" onclick="editData(' . $row->id . ', \'' . $row->tipe . '\', \'' . $row->kode_reason . '\', \'' . $row->nama_reason . '\')"><i class="ri-pencil-line"></i> Edit</button>';

                if ($row->is_active == 'Y') {
                    $btnStatus = '<button class="btn btn-sm btn-outline-danger ms-1" onclick="updateStatus(' . $row->id . ', \'N\')">Nonaktifkan</button>';
                } else {
                    $btnStatus = '<button class="btn btn-sm btn-outline-success ms-1" onclick="updateStatus(' . $row->id . ', \'Y\')">Aktifkan</button>';
                }

                return $btnEdit . $btnStatus;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function storeOrUpdate(Request $req)
    {
        try {
            $reason = $req->id ? MasterReason::find($req->id) : new MasterReason();

            $reason->tipe        = $req->tipe;
            $reason->kode_reason = $req->kode_reason;
            $reason->nama_reason = $req->nama_reason;
            $reason->save();

            return response()->json([
                'success' => true,
                'message' => 'Data reason berhasil disimpan!',
            ]);
        } catch (\Throwable $e) {
            Log::error('Error add/update reason: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function updateStatus(Request $req)
    {
        try {
            MasterReason::where('id', $req->id)
                ->update([
                    'is_active' => $req->status,
                ]);

            $status = $req->status == 'Y' ? 'Aktif' : 'Nonaktif';
            return response()->json([
                'success' => true,
                'message' => 'Status reason berhasil diupdate ke ' . $status,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate status: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function uploadExcel(Request $req)
    {
        $req->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:5120',
        ]);
        // $file = $req->file('excel_file');

        DB::beginTransaction();

        try {
            DB::table('master_reason_s2')->delete();
            Excel::import(new MasterReasonImport(), $req->file('excel_file'));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data Excel berhasil diimpor ke database!',
            ]);
        } catch (\Throwable $e) {
            DB::rollback();
            Log::error('Upload Excel Failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengimpor data. Pastikan format kolom sesuai dengan template HR.',
            ], 500);
        }
    }
}
