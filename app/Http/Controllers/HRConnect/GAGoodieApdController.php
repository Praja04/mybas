<?php
namespace App\Http\Controllers\HRConnect;

use App\HrGoodieApd;
use App\HrKaryawan;
use App\Http\Controllers\Controller;
use App\Jobs\HRConnect\GoodieNotify;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $id        = $req->id;
        $confirm   = $req->confirm;
        $tgl_masuk = $req->tgl_masuk;

        HrKaryawan::where('tanggal_masuk', $tgl_masuk)
            ->update([
                'is_goobag' => 'Y',
            ]);

        $count = $req->jumlah;

        $email_hr_karyawan = User::whereHas('group.permissions', function ($query) {
            $query->where('codename', 'hr_connect_notified_in');
        })->select('email')
            ->whereNotNull('email')
            ->groupBy('email')
            ->get();
        $email = $email_hr_karyawan->first();
        $to    = $email_hr_karyawan->pluck('email')->toArray();
        GoodieNotify::dispatch($to, $count, $tgl_masuk);

        return response()->json(['msg' => 'Data berhasil diperbarui!']);
    }

    public function confirmAll(Request $req)
    {
        try {

            HrKaryawan::where('is_goobag', 'N')
                ->update([
                    'is_goobag' => 'Y',
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Seluruh persiapan Goodie Bag berhasil dikonfirmasi!',
            ]);
        } catch (\Exception $e) {
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
