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
    public function index()
    {
        return view('hr-connect.ga.prepare-goodie-apd');
    }

    public function getData()
    {
        $goodies = HrKaryawan::select('tanggal_masuk', DB::raw('count(id) as count'))
            ->where([
                'in_kode_group' => 'Y',
                'in_complete'   => 'Y',
                'active'        => 'Y',
                'p_in'          => 'Y',
                'is_goobag'     => 'N',
                'is_excuse_out' => 'N',
                'p_no'          => 'N',
                'shutdown'      => 'N',
            ])
            ->whereNotNull('tanggal_masuk')
            ->where('tanggal_masuk', '!=', '0000-00-00')
            ->groupBy('tanggal_masuk')
            ->orderBy('tanggal_masuk', 'desc')
            ->get();

        return DataTables::of($goodies)->make(true);
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
                ->where([
                    'in_kode_group' => 'Y',
                    'in_complete'   => 'Y',
                    'is_goobag'     => 'N',
                    'is_excuse_out' => 'N',
                    'p_no'          => 'N',
                    'active'        => 'Y',
                    'shutdown'      => 'N',
                ])
                ->lockForUpdate()
                ->update([
                    'is_goobag' => 'Y',
                ]);

            $email_hr = User::whereHas('group.permissions', function ($query) {
                $query->where('codename', 'hr_connect_notified_in');
            })
                ->whereNotNull('email')
                ->pluck('email')
                ->unique()
                ->toArray();

            DB::commit();

            if (! empty($email_hr)) {
                GoodieNotify::dispatch($email_hr, $count, $tgl_masuk);
            }

            return response()->json([
                'success' => true,
                'msg'     => 'Proses konfirmasi berhasil dan email notifikasi sedang dikirim!',
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('GA Goodie Update Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'msg'     => 'Gagal memproses data. Terjadi kesalahan pada server.',
            ], 500);
        }
    }

    public function confirmAll(Request $req)
    {
        DB::beginTransaction();

        try {
            HrKaryawan::where([
                'in_kode_group' => 'Y',
                'in_complete'   => 'Y',
                'is_goobag'     => 'N',
                'is_excuse_out' => 'N',
                'p_no'          => 'N',
                'active'        => 'Y',

                'shutdown'      => 'N',
            ])
                ->whereNotNull('tanggal_masuk')
                ->where('tanggal_masuk', '!=', '0000-00-00')
                ->lockForUpdate()
                ->update([
                    'is_goobag' => 'Y',
                ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Seluruh persiapan Goodie Bag berhasil dikonfirmasi!',
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('GA Goodie Confirm All Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan konfirmasi data massal.',
            ], 500);
        }
    }
}
