<?php

namespace App\Http\Controllers\System5R;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\System5R\Jadwal;
use App\Models\System5R\Periode;
use Carbon\Carbon;

class MasterPenilaianController extends Controller
{
    public function index()
    {
        return view('system5r.master-penilaian.index');
    }

    // private function tahun
    // 
    private function generateJadwalID()
    {

        $tahunSekarang = Carbon::now()->year;
        $tahunTerakhir = Jadwal::max('tahun');
        if (!$tahunTerakhir || $tahunSekarang > $tahunTerakhir) {
            $tahunTerakhir = $tahunSekarang;
        } else {
            $tahunTerakhir++;
        }
        $idJadwalBaru = 'J' . $tahunTerakhir;

        return $idJadwalBaru;
    }

    // method untuk store tahun
    public function store(Request $request)
    {
        $request->validate([
            'tahun' => 'required|numeric',
        ]);


        $periode = Jadwal::firstOrNew(['tahun' => $request->tahun]);
        if (!$periode->exists) {
            $idJadwalBaru = $this->generateJadwalID();
            $periode->id_jadwal = $idJadwalBaru;
            $periode->save();

            return response()->json(['success' => 1, 'message' => 'Berhasil membuat jadwal penilaian']);
        } else {
            return response()->json(['success' => 0, 'message' => 'Tahun sudah ada'], 400);
        }
    }


    private function generatePeriode()
    {
        $lastPeriode = Periode::orderBy('id_periode', 'desc')->first();

        if ($lastPeriode) {
            $lastId = substr($lastPeriode->id_periode, 1);
            $nextId = str_pad($lastId + 1, 3, '0', STR_PAD_LEFT);
            $newId = 'P' . $nextId;
        } else {
            $newId = 'P001';
        }

        return $newId;
    }

    public function storePenilaian(Request $request)
    {
        $request->validate([
            'id_jadwal' => 'required|string|max:10',
            'nama_periode' => 'required|string|max:50',
            'keterangan' => 'nullable|string',
        ]);

        $id_periode = $this->generatePeriode();

        $periode = new Periode;
        $periode->id_periode = $id_periode;
        $periode->id_jadwal = $request->id_jadwal;
        $periode->nama_periode = $request->nama_periode;
        $periode->keterangan = $request->keterangan;
        $periode->save();

        return response()->json(['success' => 1, 'message' => 'Berhasil menambahkan periode penilaian']);
    }


    public function dataPeriode()
    {
        $jadwalData = Jadwal::all();

        $combinedData = [];

        foreach ($jadwalData as $jadwal) {
            $periodes = Periode::where('id_jadwal', $jadwal->id_jadwal)->orderBy('nama_periode')->get();

            $namaPeriodes = $periodes->map(function ($periode) {
                return $periode->nama_periode;
            })->implode(', ');

            $keterangans = $periodes->pluck('keterangan')->implode(', ');

            $combinedData[] = [
                'id_jadwal' => $jadwal->id_jadwal,
                'tahun' => $jadwal->tahun,
                'id_periode' => $jadwal->id_periode,
                'nama_periode' => $namaPeriodes,
                'keterangan' => $keterangans,
            ];
        }

        $response = [
            'data' => $combinedData,
            'status' => 'success',
            'code' => 200,
        ];

        return response()->json($response);
    }

    public function deletePeriode(Request $request)
    {
    }
}
