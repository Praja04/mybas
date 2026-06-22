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

        $cardOwner = ParkingHistories::where('sn_card', $data['sn_card'])
            ->where('nik', $data['nik'])
            ->where('nama', '!=', $data['nama'])
            ->latest()
            ->first();

        if (! empty($cardOwner)) {
            return response()->json([
                'success' => false,
                'message' => 'Ini bukan kartu Anda!',
                'data'    => [
                    'nama' => $data['nama'],
                ],
            ], 404);
        }

        $notOwner = ParkingHistories::where('sn_card', $data['sn_card'])
            ->where('nik', '!=', $data['nik'])
            ->latest()
            ->first();

        if (! empty($notOwner)) {
            return response()->json([
                'success' => false,
                'message' => 'Kartu sudah terikat dengan NIK lain',
                'data'    => $notOwner->only('nik'),
            ], 409);
        }

        // $possibleManyCards = ParkingHistories::where('nik', $data['nik'])->where('sn_card', '!=', $data['sn_card'])->exists();

        // if ($possibleManyCards) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'NIK ini sudah terdaftar menggunakan kartu lain.',
        //         'data'    => [
        //             'sn_card' => $data['sn_card'],
        //         ],
        //     ], 422);
        // }

        $latestTap = ParkingHistories::where([
            'nik'     => $data['nik'],
            'nama'    => $data['nama'],
            'sn_card' => $data['sn_card'],
        ])->latest()->first();

        if ($latestTap) {
            if ($latestTap->status == $data['status']) {
                if (strtoupper(trim($data['status'])) == 'IN') {
                    $message = 'Kartu sedang berada di dalam area parkir';
                } else {
                    $message = 'Kartu sedang berada di luar area parkir';
                }

                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'data'    => [
                        'sn_card' => $data['sn_card'],
                        'status'  => $data['status'],
                    ],
                ], 409);
            }
        } else {
            if (strtoupper(trim($data['status'])) == 'OUT') {
                return response()->json([
                    'success' => false,
                    'message' => 'Kartu sedang berada di luar area parkir',
                    'data'    => [
                        'sn_card' => $data['sn_card'],
                        'status'  => $data['status'],
                    ],
                ], 409);
            }
        }

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
