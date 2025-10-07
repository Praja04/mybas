<?php

namespace App\Http\Controllers\Sigra;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sigra\Perusahaan;
use App\Models\Sigra\SIO;
use App\Exports\SIOExport;
use App\Models\Sigra\SIOSertifikasi;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\LocalAttachment;


class SioController extends Controller
{
    public function index()
    {
        $perisahaan = Perusahaan::all();
        return view('sigra.sio', [
            'perusahaan' => $perisahaan
        ]);
    }

    public function tambahPerizinan(Request $request)
    {
        $SIO = new SIO;
        $SIO->id_perusahaan = $request->perusahaan;
        $SIO->nama_perizinan = $request->nama_perizinan;
        $SIO->nama_karyawan = $request->nama_karyawan;
        $SIO->nik_karyawan = $request->nik_karyawan;

        $SIO->save();
        return response()->json(['success' => 1, 'message' => 'Berhasil membuat perizinan operasional']);
    }

    public function getAll()
    {
        $sertifikasi_sio = [];
        $sioList = SIO::where('status', '!=', 'deleted')->get();

        foreach ($sioList as $key => $sio) {
            $label_status = $sio->status == 'inactive' ? 'secondary' : 'success';

            $sertifikasi = SIOSertifikasi::where('id_sio', $sio->id)
                ->where('status', '!=', 'deleted')
                ->orderBy('tanggal_habis', 'desc')
                ->first();

            $expired = '-';
            if ($sertifikasi) {
                $overdue = $this->expired($sertifikasi->tanggal_habis);
                if ($overdue >= 30) {
                    $expired = 'warning';
                } elseif ($overdue > 0) {
                    $expired = 'danger';
                } else {
                    $expired = 'success';
                }
            }

            $array = [
                $key + 1,
                $sio->perusahaan->nama_perusahaan,
                '<a class="text-hover-dark" href="javascript:" onClick="showSertifikasi(\'' . $sio->id . '\',\'' . $sio->nama_perizinan . '\', \'' . $sio->status . '\')">
                    <i class="fa fa-archive text-danger font-size-sm"></i>
                    ' . $sio->nama_perizinan . '
                </a>',
                $sio->nama_karyawan ?? '-',
                $sio->nik_karyawan ?? '-',
                $sertifikasi ? $sertifikasi->nomor_izin : '-',
                '<span class="label label-inline label-' . $label_status . '">' . $sio->status . '</span>',
                $sertifikasi ? $this->formatTanggal($sertifikasi->tanggal_terbit) : '-',
                $sertifikasi ? '<span class="label label-inline label-outline-' . $expired . '">' . $this->formatTanggal($sertifikasi->tanggal_habis) . '</span>' : '-',
                $sertifikasi ? number_format($sertifikasi->harga, 0, ',', '.') : '-',
                $sertifikasi ? $sertifikasi->keterangan : '-',
                '<a onClick="edit(\'' . $sio->id . '\')" title="Edit" href="javascript:" class="fa fa-edit text-hover-dark mr-2"></a>
                <a onClick="deleteItem(\'' . $sio->id . '\')" title="Hapus" href="javascript:" class="fa fa-trash text-hover-dark"></a>'
            ];
            $sertifikasi_sio[] = $array;
        }

        return response()->json([
            'success' => 1,
            'message' => 'Get data perizinan succeed',
            'data' => $sertifikasi_sio
        ]);
    }


    public function buatSertifikat(Request $request)
    {
        // dd($request->all()); // Debug request data

        $sertifikasi = new SIOSertifikasi;
        $sertifikasi->id_sio = $request->sio_id;
        $sertifikasi->nomor_izin = $request->nomor_izin;
        $sertifikasi->harga = $request->harga;
        $sertifikasi->tanggal_terbit = $request->tanggal_sertifikasi;
        $sertifikasi->tanggal_habis = $request->tanggal_expired;
        $sertifikasi->keterangan = $request->remarks;
        $sertifikasi->transaction_id = $request->create_sertifikat_transaction_id;
        $sertifikasi->status = 'Created';
        $sertifikasi->save();

        // tambah ke transaction id
        $attachment = LocalAttachment::where('transaction_id', $request->create_sertifikat_transaction_id)
            ->update(['is_draft' => 'N']);

        return response()->json([
            'success' => 1,
            'message' => 'Sertifikat created successfully'
        ]);
    }

    public function getSertifikat($id)
    {
        $sertifikasi = SIOSertifikasi::where('id_sio', $id)->where('status', '!=', 'deleted')->orderBy('tanggal_habis', 'desc')->get();
        foreach ($sertifikasi as $s) {
            $s->attachments;
        }
        return response()->json(['success' => 1, 'data' => $sertifikasi], 200);
    }

    public function ubahSertifikat(Request $request)
    {
        // dd($request->all());
        $sertifikasi = SIOSertifikasi::find($request->id);
        $sertifikasi->id_sio = $request->sio_id;
        $sertifikasi->nomor_izin = $request->nomor_izin;
        $sertifikasi->tanggal_terbit = $request->tanggal_sertifikasi;
        $sertifikasi->tanggal_habis = $request->tanggal_expired;
        $sertifikasi->harga = $request->harga;
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
        $sertifikasi = SIOSertifikasi::find($id);
        $attachments = LocalAttachment::where('transaction_id', $sertifikasi->transaction_id)->get();
        return response()->json([
            'success' => 1,
            'message' => 'Get attachments succeed',
            'data' => $attachments
        ]);
    }

    public function getPerizinan($id)
    {
        $data = SIO::find($id);
        return response()->json(['success' => 1, 'message' => 'Get data succeed', 'data' => $data]);
    }


    public function update(Request $request)
    {
        $data = SIO::find($request->id);

        if (!$data) {
            return response()->json(['error' => 'data gak ketemu'], 404);
        }

        $data->id_perusahaan = $request->perusahaan;
        $data->nama_perizinan = $request->nama_perizinan;
        $data->nama_karyawan = $request->nama_karyawan;
        $data->nik_karyawan = $request->nik_karyawan;
        $data->save();

        return response()->json(['success' => 1, 'message' => 'Update data succeed']);
    }

    public function deletePerizinan($id)
    {
        $data = SIO::find($id);
        $data->status = 'deleted';
        $data->save();
        return response()->json(['success' => 1, 'message' => 'Delete data succeed']);
    }

    public function deleteSertifikasi($id)
    {
        $data = SIOSertifikasi::find($id);
        $data->status = 'deleted';
        $data->save();
        return response()->json(['success' => 1, 'message' => 'Delete data succeed']);
    }

    public function getSertifikasi($id)
    {
        $sertifikasi = SIOSertifikasi::find($id);
        return response()->json([
            'success' => 1,
            'data' => $sertifikasi,
            'message' => 'Get sertifikasi succeed'
        ]);
    }

    public function setStatus(Request $request)
    {
        $data = SIO::find($request->id);
        $data->status = $request->status;
        $data->save();
        return response()->json(['success' => 1, 'message' => 'Change status succeed']);
    }

    public function exportSio()
    {
        return Excel::download(new SIOExport, 'SIO.xls');
    }
}
