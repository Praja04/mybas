<?php
namespace App\Http\Controllers\HRConnect;

use App\HrKaryawan;
use App\Http\Controllers\Controller;
use App\Jobs\HRConnect\GoodieNotify;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class GAGoodieApdController extends Controller
{
    public function getData()
    {
        $goodies = HrKaryawan::where('is_goobag', 'N')
            ->whereDate('tanggal_masuk', '>', '2024-10-01')
            ->select('tanggal_masuk', DB::raw('count(*) as count'))
            ->groupBy('tanggal_masuk');

        return DataTables::of($goodies)->make(true);
    }

    public function index()
    {
        return view('hr-connect.ga.prepare-goodie-apd');
    }

    public function updateData(Request $req)
    {
        $tgl_masuk = $req->tgl_masuk;
        $count     = $req->jumlah;

        if (empty($tgl_masuk)) {
            return response()->json(['success' => false, 'message' => 'Tanggal masuk tidak valid.'], 400);
        }

        DB::beginTransaction();

        try {
            HrKaryawan::where('tanggal_masuk', $tgl_masuk)
                ->where('is_goobag', 'N')
                ->lockForUpdate()
                ->update([
                    'is_goobag' => 'Y',
                ]);

            DB::commit();

            $email_hr = User::whereHas('group.permissions', function ($query) {
                $query->where('codename', 'hr_connect_notified_in');
            })->select('email')
                ->whereNotNull('email')
                ->groupBy('email')
                ->get();

            if ($email_hr->isNotEmpty()) {
                $to = $email_hr->pluck('email')->toArray();

                GoodieNotify::dispatch($to, $count, $tgl_masuk);
            }

            return response()->json(['success' => true, 'msg' => 'Proses konfirmasi berhasil dan email sedang dikirim!']);
        } catch (\Throwable $e) {
            DB::rollBack();
            
            Log::error('GA Goodie Update Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'msg'     => 'Gagal memproses data.',
            ], 500);
        }
    }

    public function confirmAll(Request $req)
    {
        DB::beginTransaction();

        try {
            HrKaryawan::where('is_goobag', 'N')
                ->whereDate('tanggal_masuk', '>', '2024-10-01')
                ->lockForUpdate()
                ->update([
                    'is_goobag' => 'Y',
                ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Seluruh persiapan Goodie Bag berhasil dikonfirmasi!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('GA Goodie Confirm All Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan konfirmasi data: ' . $e->getMessage(),
            ], 500);
        }
    }

    // public function updateDataDitolak(Request $req)
    // {
    //     $id      = $req->id;
    //     $confirm = $req->confirm;

    //     HrGoodieApd::where('id', $id)
    //         ->update([
    //             'confirmed' => $confirm,
    //         ]);

    //     return response()->json(['msg' => 'Data berhasil diperbarui!']);
    // }

    // public function remain(Request $req)
    // {
    //     $remain = HrGoodieApd::where([
    //         'tgl_masuk' => $req->tgl_masuk,
    //         'confirmed' => 'Y',
    //     ])->sum('jumlah_orang');

    //     return $remain;
    // }
}
