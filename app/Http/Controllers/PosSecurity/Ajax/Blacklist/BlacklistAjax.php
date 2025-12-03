<?php

namespace App\Http\Controllers\PosSecurity\Ajax\Blacklist;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PosSecurity\Logging\BlacklistIdentitas;

class BlacklistAjax extends Controller
{
    public function show(Request $request)
    {
        $id = $request->query('id');

        try {
            if (!$id) {
                return response()->json(['success' => false, 'message' => 'ID tidak ditemukan'], 400);
            }

            $blacklist = BlacklistIdentitas::with('transaksi')->find($id);

            if (!$blacklist) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID tidak ditemukan',
                ]);
            }

            $visitor = $blacklist->transaksi;

            $fotoArray = json_decode(optional($visitor)->foto, true);
            $fotoDiri = is_array($fotoArray) ? ($fotoArray[0] ?? null) : null;

            return response()->json([
                'success' => true,
                'data' => [
                    'nama' => $blacklist->nama,
                    'no_identitas' => $blacklist->no_identitas,
                    'jenis_identitas' => $blacklist->jenis_identitas,
                    'tanggal_lahir' => optional($blacklist->tanggal_lahir)->format('d-m-Y'),
                    'alasan_blacklist' => $blacklist->alasan_blacklist,
                    'tanggal_blacklist' => optional($blacklist->tanggal_blacklist)->format('d-m-Y H:i'),
                    'diblacklist_oleh' => $blacklist->diblacklist_oleh,
                    'aktif' => $blacklist->aktif,

                    'foto_diri_url' => $fotoDiri,
                    'foto_ktp_url' => optional($visitor)->imgvisitorpathin,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }
    }
}
