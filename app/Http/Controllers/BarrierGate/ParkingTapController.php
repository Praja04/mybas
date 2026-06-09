<?php
namespace App\Http\Controllers\BarrierGate;

use App\Http\Controllers\Controller;
use App\Http\Requests\ParkingHistoryRequest;
use App\Http\Requests\SnCardRequest;
use App\ParkingHistories;
use App\RfidCard;

class ParkingTapController extends Controller
{
    public function storeCard(SnCardRequest $request)
    {
        $rfidCard = RfidCard::firstOrCreate([
            'sn_card' => $request->sn_card,
        ]);

        if ($rfidCard->wasRecentlyCreated) {
            return response()->json([
                'success' => true,
                'message' => 'Nomor Kartu Berhasil Didistribusikan',
                'data'    => $rfidCard->only('sn_card'),
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Nomor Kartu Sudah Terdaftar',
                'data'    => $rfidCard->only('sn_card'),
            ], 409);
        }
    }

    public function parkingHistory(ParkingHistoryRequest $request)
    {
        $data              = $request->validated();
        $data['tapped_at'] = now();

        $histories = ParkingHistories::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Riwayat Parkir Berhasil Ditambahkan',
            'data'    => [
                'nik'       => $histories->nik,
                'sn_card'   => $histories->sn_card,
                'nama'      => $histories->nama,
                'tapped_at' => $histories->tapped_at->format('Y-m-d H:i:s'),
                'status'    => $histories->status,
            ],
        ], 201);
    }
}
