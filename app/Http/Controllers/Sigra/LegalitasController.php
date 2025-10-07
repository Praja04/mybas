<?php

namespace App\Http\Controllers\Sigra;

use App\Http\Controllers\Controller;
use App\Models\Sigra\Legalitas;
use App\Models\Sigra\SertifikasiLegalitas;
use Illuminate\Http\Request;
use App\Models\LocalAttachment;
use App\Models\Sigra\Perusahaan;
use App\Exports\LegalitasExport;
use Maatwebsite\Excel\Facades\Excel;

class LegalitasController extends Controller
{

    public function exportLegalitas()
    {
        return Excel::download(new LegalitasExport, 'legalitas.xls');
    }

    public function index()
    {
        $perusahaan = Perusahaan::all();
        return view('sigra.legalitas', [
            'perusahaan' => $perusahaan
        ]);
    }

    public function store(Request $request)
    {
        $legalitas = new Legalitas;
        $legalitas->id_perusahaan = $request->perusahaan;
        $legalitas->nama_legalitas = $request->nama_perizinan;
        $legalitas->save();
        return response()->json(['success' => 1, 'message' => 'Berhasil membuat perizinan legalitas']);
    }

    public function getAll()
    {
        $sertifikasi_legalitas = [];
        $legalitas = Legalitas::where('status', '!=', 'deleted')->get();

        foreach ($legalitas as $key => $data) {
            if ($data->status == 'inactive') {
                $label_status = 'secondary';
            } else {
                $label_status = 'success';
            }

            $sertifikasi = SertifikasiLegalitas::where('id_legalitas', $data->id)->where('status', '!=', 'deleted')->orderBy('tanggal_habis', 'desc')->first();

            if ($sertifikasi != null) {
                $overdue = $this->expired($sertifikasi->tanggal_expired);
                if ($overdue >= 30) {
                    // Jika 30 hari lagi ke expired
                    $expired = 'warning';
                } elseif ($overdue > 0) {
                    $expired = 'danger';
                } else {
                    $expired = 'success';
                }
            }

            // perbaiki nested ternary operator  di sertifikasi=>tanggal_habis

            $array = [
                $key + 1,
                $data->perusahaan->nama_perusahaan,
                '<a class="text-hover-dark" href="javascript:" onClick="showSertifikasi(\'' . $data->id . '\',\'' . $data->nama_legalitas . '\', \'' . $data->status . '\')">
                    <i class="fa fa-archive text-danger font-size-sm"></i>
                    ' . $data->nama_legalitas . '
                </a>',
                $sertifikasi == null ? '-' : $sertifikasi->nomor_dokumen,
                $sertifikasi ? number_format($sertifikasi->harga, 0, ',', '.') : '-',
                $sertifikasi == null ? '-' : $sertifikasi->instansi,
                '<span class="label label-inline label-' . $label_status . '">' . $data->status . '</span>',
                $sertifikasi == null ? '-' : $this->formatTanggal($sertifikasi->tanggal_terbit),
                $sertifikasi == null ? '-' : ($sertifikasi->tanggal_habis == null ? '-' : '<span class="label label-inline label-outline-' . $expired . '">' . $this->formatTanggal($sertifikasi->tanggal_habis) . '</span>'),
                $sertifikasi == null ? '-' : $sertifikasi->masa_berlaku,
                $sertifikasi == null ? '-' : $sertifikasi->keterangan,
                '
                <a onClick="edit(\'' . $data->id . '\')" title="Edit" href="javascript:" class="fa fa-edit text-hover-dark mr-2"></a>
                <a onClick="deleteItem(\'' . $data->id . '\')" title="Hapus" href="javascript:" class="fa fa-trash text-hover-dark"></a>'

            ];
            $sertifikasi_legalitas[] = $array;
        }
        return response()->json([
            'success' => 1,
            'message' => 'Get data perizinan succeed',
            'data' => $sertifikasi_legalitas
        ]);
    }

    public function getSertifikasi($id)
    {
        $sertifikasi = SertifikasiLegalitas::where('id_legalitas', $id)->where('status', '!=', 'deleted')->orderBy('tanggal_habis', 'desc')->get();
        foreach ($sertifikasi as $s) {
            $s->attachments;
        }
        return response()->json(['success' => 1, 'data' => $sertifikasi], 200);
    }

    public function createSertifikat(Request $request)
    {
        // dd($request->all());
        $sertifikasi = new SertifikasiLegalitas;
        $sertifikasi->id_legalitas = $request->legalitas_id;
        $sertifikasi->nomor_dokumen = $request->no_dokumen;
        $sertifikasi->harga = $request->harga;
        $sertifikasi->instansi = $request->instansi;
        $sertifikasi->tanggal_terbit = $request->tanggal_sertifikasi;
        $sertifikasi->tanggal_habis = $request->tanggal_expired;
        $sertifikasi->masa_berlaku = $request->masa_berlaku;
        $sertifikasi->keterangan = $request->remarks;
        $sertifikasi->status = 'Created';
        $sertifikasi->transaction_id = $request->create_sertifikat_transaction_id;
        $sertifikasi->save();

        // Change attachment draft to no
        $attachment = LocalAttachment::where('transaction_id', $request->create_sertifikat_transaction_id)
            ->update(['is_draft' => 'N']);


        return response()->json([
            'success' => 1,
            'message' => 'Sertifikat created successfully'
        ]);
    }

    public function editSertifikat(Request $request)
    {
        // dd($request->all());
        $sertifikasi = SertifikasiLegalitas::find($request->id);
        $sertifikasi->id_legalitas = $request->legalitas_id;
        $sertifikasi->nomor_dokumen = $request->no_dokumen;
        $sertifikasi->harga = $request->harga;
        $sertifikasi->instansi = $request->instansi;
        $sertifikasi->tanggal_terbit = $request->tanggal_sertifikasi;
        $sertifikasi->tanggal_habis = $request->tanggal_expired;
        $sertifikasi->masa_berlaku = $request->masa_berlaku;
        $sertifikasi->keterangan = $request->remarks;
        $sertifikasi->status = 'Created';
        $sertifikasi->transaction_id = $request->create_sertifikat_transaction_id;
        $sertifikasi->save();

        // Change attachment draft to no
        $attachment = LocalAttachment::where('transaction_id', $request->create_sertifikat_transaction_id)
            ->update(['is_draft' => 'N']);


        return response()->json([
            'success' => 1,
            'message' => 'Sertifikat update successfully'
        ]);
    }

    public function getAttachments($id)
    {
        $sertifikasi = SertifikasiLegalitas::find($id);
        $attachments = LocalAttachment::where('transaction_id', $sertifikasi->transaction_id)->get();
        return response()->json([
            'success' => 1,
            'message' => 'Get attachments succeed',
            'data' => $attachments
        ]);
    }

    public function get($id)
    {
        $data = Legalitas::find($id);
        return response()->json(['success' => 1, 'message' => 'Get data succeed', 'data' => $data]);
    }

    public function update(Request $request)
    {
        $data = Legalitas::find($request->id);
        $data->id_perusahaan = $request->perusahaan;
        $data->nama_legalitas = $request->nama_perizinan;
        $data->save();
        return response()->json(['success' => 1, 'message' => 'Update data succeed']);
    }

    public function delete($id)
    {
        $data = Legalitas::find($id);
        $data->status = 'deleted';
        $data->save();
        return response()->json(['success' => 1, 'message' => 'Delete data succeed']);
    }

    public function setStatus(Request $request)
    {
        $data = Legalitas::find($request->id);
        $data->status = $request->status;
        $data->save();
        return response()->json(['success' => 1, 'message' => 'Change status succeed']);
    }
}
