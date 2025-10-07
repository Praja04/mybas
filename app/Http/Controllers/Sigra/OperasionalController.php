<?php

namespace App\Http\Controllers\Sigra;

use App\Http\Controllers\Controller;
use App\Models\Sigra\Operasional;
use App\Models\Sigra\Perusahaan;
use App\Models\Sigra\SertifikasiOperasional;
use App\Models\LocalAttachment;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Sigra\OperasionalExport;

class OperasionalController extends Controller
{
    public function store(Request $request)
    {
        $operasional = new Operasional;
        $operasional->id_perusahaan = $request->perusahaan;
        $operasional->nama_perizinan = $request->nama_perizinan;
        $operasional->nomor_perizinan = $request->nomor_perizinan;
        $operasional->save();
        return response()->json(['success' => 1, 'message' => 'Berhasil membuat perizinan operasional']);
    }

    public function index()
    {
        $perisahaan = Perusahaan::all();
        return view('sigra.operasional', [
            'perusahaan' => $perisahaan
        ]);
    }

    public function export()
    {
        return Excel::download(new OperasionalExport, 'sigra-operasional-export-' . time() . '.xls');
    }

    public function getAll()
    {
        $sertifikasi_operasional = [];
        $operasional = Operasional::where('status', '!=', 'deleted')->get();

        foreach ($operasional as $key => $data) {
            if ($data->status == 'inactive') {
                $label_status = 'secondary';
            } else {
                $label_status = 'success';
            }

            $sertifikasi = SertifikasiOperasional::where('id_operasional', $data->id)->where('status', '!=', 'deleted')->orderBy('tahun', 'desc')->first();

            if ($sertifikasi != null) {
                $overdue = $this->expired($sertifikasi->tanggal_expired);
                if ($overdue <= 30) {
                    // Jika 30 hari lagi ke expired
                    $expired = 'warning';
                } elseif ($overdue < 0) {
                    $expired = 'danger';
                } else {
                    $expired = 'success';
                }
            }

            $array = [
                'no' => $key + 1,
                'perusahaan' => $data->perusahaan->nama_perusahaan,
                'nama_perizinan' => '<a class="text-hover-dark" href="javascript:" onClick="showSertifikasi(\'' . $data->id . '\',\'' . $data->nama_perizinan . ' - ' . $data->nomor_perizinan . '\', \'' . $data->status . '\')">
                    <i class="fa fa-archive text-danger font-size-sm"></i>
                    ' . $data->nama_perizinan . '
                </a>',
                'nomor_perizinan' => $data->nomor_perizinan,
                'harga' => $sertifikasi ? number_format($sertifikasi->harga, 0, ',', '.') : '-',
                'status' => '<span class="label label-inline label-' . $label_status . '">' . $data->status . '</span>',
                'terbit' => $sertifikasi == null ? '-' : $this->formatTanggal($sertifikasi->tanggal_sertifikasi),
                'expired' => $sertifikasi == null ? '-' : $this->formatTanggal($sertifikasi->tanggal_expired),
                'remarks' => $sertifikasi == null ? '-' : $sertifikasi->remarks,
                'tahun' => $sertifikasi == null ? '-' : $sertifikasi->tahun,
                'aksi' => '
                <a onClick="edit(\'' . $data->id . '\')" title="Edit" href="javascript:" class="fa fa-edit text-hover-dark mr-2"></a>
                <a onClick="deleteItem(\'' . $data->id . '\')" title="Hapus" href="javascript:" class="fa fa-trash text-hover-dark"></a>'

            ];
            $sertifikasi_operasional[] = $array;
        }
        return response()->json([
            'success' => 1,
            'message' => 'Get data perizinan succeed',
            'data' => $sertifikasi_operasional
        ]);
    }

    public function getSertifikasi($id)
    {
        $sertifikasi = SertifikasiOperasional::where('id_operasional', $id)->where('status', '!=', 'deleted')->orderBy('tahun', 'desc')->get();
        foreach ($sertifikasi as $s) {
            $s->attachments;
        }
        return response()->json(['success' => 1, 'data' => $sertifikasi], 200);
    }

    public function createSertifikat(Request $request)
    {
        // dd($request->all());
        $sertifikasi = new SertifikasiOperasional;
        $sertifikasi->id_operasional = $request->operasional_id;
        $sertifikasi->perizinan = $request->perizinan;
        $sertifikasi->harga = $request->harga;
        $sertifikasi->tanggal_sertifikasi = $request->tanggal_sertifikasi;
        $sertifikasi->tanggal_expired = $request->tanggal_expired;
        $sertifikasi->status = 'Created';
        $sertifikasi->dokumen_asli = $request->dokumen_asli;
        $sertifikasi->scan = 'attachment';
        $sertifikasi->remarks = $request->remarks;
        $sertifikasi->tahun = $request->tahun;
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
        $sertifikasi = SertifikasiOperasional::find($request->id);
        $sertifikasi->id_operasional = $request->operasional_id;
        $sertifikasi->perizinan = $request->perizinan;
        $sertifikasi->harga = $request->harga;
        $sertifikasi->tanggal_sertifikasi = $request->tanggal_sertifikasi;
        $sertifikasi->tanggal_expired = $request->tanggal_expired;
        $sertifikasi->status = 'Created';
        $sertifikasi->dokumen_asli = $request->dokumen_asli;
        $sertifikasi->scan = 'attachment';
        $sertifikasi->remarks = $request->remarks;
        $sertifikasi->tahun = $request->tahun;
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
        $sertifikasi = SertifikasiOperasional::find($id);
        $attachments = LocalAttachment::where('transaction_id', $sertifikasi->transaction_id)->get();
        return response()->json([
            'success' => 1,
            'message' => 'Get attachments succeed',
            'data' => $attachments
        ]);
    }

    public function get($id)
    {
        $data = Operasional::find($id);
        return response()->json(['success' => 1, 'message' => 'Get data succeed', 'data' => $data]);
    }

    public function update(Request $request)
    {
        $data = Operasional::find($request->id);
        $data->id_perusahaan = $request->perusahaan;
        $data->nama_perizinan = $request->nama_perizinan;
        $data->nomor_perizinan = $request->nomor_perizinan;
        $data->save();
        return response()->json(['success' => 1, 'message' => 'Update data succeed']);
    }

    public function delete($id)
    {
        $data = Operasional::find($id);
        $data->status = 'deleted';
        $data->save();
        return response()->json(['success' => 1, 'message' => 'Delete data succeed']);
    }

    public function setStatus(Request $request)
    {
        $data = Operasional::find($request->id);
        $data->status = $request->status;
        $data->save();
        return response()->json(['success' => 1, 'message' => 'Change status succeed']);
    }
}
