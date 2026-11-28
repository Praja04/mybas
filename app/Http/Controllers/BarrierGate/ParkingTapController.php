<?php
namespace App\Http\Controllers\BarrierGate;

use App\Http\Controllers\Controller;
use App\Http\Requests\ParkingHistoryRequest;
use App\Http\Requests\SnCardRequest;
use App\ParkingHistories;
use App\RfidCard;
use Illuminate\Http\Request;

class ParkingTapController extends Controller
{
    private function formatData($riwayat)
    {
        return [
            'nik'       => $riwayat->nik,
            'sn_card'   => $riwayat->sn_card,
            'nama'      => $riwayat->nama,
            'tapped_at' => $riwayat->tapped_at ? $riwayat->tapped_at->format('Y-m-d H:i:s') : null,
            'status'    => $riwayat->status,
        ];
    }
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

    public function getData(Request $req)
    {
        $mode = $req->query('mode', 'today');

        // $riwayatTerbaru = ParkingHistories::where('tapped_at', '!=', null)->latest()->first();

        // if (empty($riwayatTerbaru)) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Belum ada riwayat parkir perhari ini',
        //     ], 404);
        // }

        // if ($query == 'today') {
        //     $riwayatTerbaru = ParkingHistories::where('tapped_at', '!=', null)->whereDate('tapped_at', today())->latest()->first();
        // } else if ($query == 'all' || $query == null || $query == '') {
        //     $riwayatTerbaru = ParkingHistories::where('tapped_at', '!=', null)->latest()->get();
        // } else {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Query tidak valid',
        //     ], 400);
        // }

        // $data = [
        //     'nik'       => $riwayatTerbaru->nik,
        //     'sn_card'   => $riwayatTerbaru->sn_card,
        //     'nama'      => $riwayatTerbaru->nama,
        //     'tapped_at' => $riwayatTerbaru->tapped_at->format('Y-m-d H:i:s'),
        //     'status'    => $riwayatTerbaru->status,
        // ];

        // if ($query == '' || $query == null) {
        //     return response()->json([
        //         'success' => true,
        //         'message' => 'Data ditemukan',
        //         'data'    => $data,
        //     ], 200);
        // }

        if ($mode == 'all' || $mode == '' || $mode == null) {
            $paginator = ParkingHistories::where('tapped_at', '!=', null)->latest()->paginate(10);
            $paginator->getCollection()->transform([$this, 'formatData']);

            $data = $paginator;
        } elseif ($mode == 'today') {
            $data = ParkingHistories::where('tapped_at', '!=', null)
                ->whereDate('tapped_at', today())
                ->latest()
                ->get()
                ->map([$this, 'formatData']);
            // ->map(function ($riwayat) {
            //     return [
            //         'nik'       => $riwayat->nik,
            //         'sn_card'   => $riwayat->sn_card,
            //         'nama'      => $riwayat->nama,
            //         'tapped_at' => $riwayat->tapped_at->format('Y-m-d H:i:s'),
            //         'status'    => $riwayat->status,
            //     ];
            // });
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Query tidak valid',
            ], 400);
        }

        if ($data->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Belum ada riwayat parkir',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data ditemukan',
            'data'    => $data,
        ], 200);
    }

    public function parkingHistory(ParkingHistoryRequest $request)
    {
        $data              = $request->validated();
        $data['tapped_at'] = now();

        // $cardOwner = ParkingHistories::where('sn_card', $data['sn_card'])
        //     ->where('nik', $data['nik'])
        //     ->where('nama', '!=', $data['nama'])
        //     ->latest()
        //     ->first();

        // if (! empty($cardOwner)) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Ini bukan kartu Anda!',
        //         'data'    => [
        //             'nama' => $data['nama'],
        //         ],
        //     ], 404);
        // }

        // $notOwner = ParkingHistories::where('sn_card', $data['sn_card'])
        //     ->where('nik', '!=', $data['nik'])
        //     ->latest()
        //     ->first();

        // if (! empty($notOwner)) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Kartu sudah terikat dengan NIK lain',
        //         'data'    => $notOwner->only('nik'),
        //     ], 409);
        // }

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
            // 'nik'     => $data['nik'],
            // 'nama'    => $data['nama'],
            'sn_card' => $data['sn_card'],
        ])->latest()->first();

        if ($latestTap) {
            if ($latestTap->status == $data['status']) {
                if (strtoupper(trim($data['status'])) == 'IN') {
                    $message = 'Status kartu sudah terdaftar masuk';
                } else {
                    $message = 'Status kartu sudah terdaftar keluar';
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
                    'message' => 'Kartu belum pernah terdaftar masuk',
                    'data'    => [
                        'sn_card' => $data['sn_card'],
                        'status'  => $data['status'],
                    ],
                ], 409);
            }
        }

        $histories = ParkingHistories::create($data);

        if (strtoupper(trim($histories->status)) == 'IN') {
            $message = "Selamat Datang Di PT. Bumi Alam Segar";
        } else {
            $message = "Terima Kasih dan Sampai Jumpa Kembali";
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $this->formatData($histories),
        ], 201);
    }
}
