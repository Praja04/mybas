<?php

namespace App\Http\Controllers\Sigra;

use App\Department;
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
        $departments = Department::where('status', '1')->get();

        $perusahaan = Perusahaan::all();
        return view('sigra.sio', [
            'perusahaan' => $perusahaan,
            'departments' => $departments
        ]);
    }

    public function tambahPerizinan(Request $request)
    {

        $request->validate([
            'perusahaan' => 'required|integer|exists:sigra_perusahaan,id',
            'nama_perizinan' => 'required|string|max:150',
            'nama_karyawan' => 'required|string|max:150',
            'nik_karyawan' => 'required|string|max:150',

            'dept_id' => 'required|integer|exists:departments,id',
            'tanggal_mulai_ikatan_dinas' => 'nullable|date|required_with:tanggal_selesai_ikatan_dinas',
            'tanggal_selesai_ikatan_dinas' => 'nullable|date|required_with:tanggal_mulai_ikatan_dinas|after_or_equal:tanggal_mulai_ikatan_dinas',
        ]);

        $SIO = new SIO;
        $SIO->id_perusahaan = $request->perusahaan;
        $SIO->nama_perizinan = $request->nama_perizinan;
        $SIO->nama_karyawan = $request->nama_karyawan;
        $SIO->nik_karyawan = $request->nik_karyawan;
        $SIO->dept_id = $request->dept_id;
        $SIO->tanggal_mulai_ikatan_dinas = $request->tanggal_mulai_ikatan_dinas ?? null;
        $SIO->tanggal_selesai_ikatan_dinas = $request->tanggal_selesai_ikatan_dinas ?? null;

        $SIO->save();
        return response()->json(['success' => 1, 'message' => 'Berhasil membuat perizinan operasional']);
    }

    public function getAll()
    {
        $sertifikasi_sio = [];
        $sioList = SIO::with('department')->where('status', '!=', 'deleted')->get();


        foreach ($sioList as $key => $sio) {
            $label_status = $sio->status == 'inactive' ? 'secondary' : 'success';

            $sertifikasi = SIOSertifikasi::where('id_sio', $sio->id)
                ->where('status', '!=', 'deleted')
                ->orderBy('tanggal_habis', 'desc')
                ->first();

            $expired = '-';
            if ($sertifikasi) {
                $overdue = $this->expired($sertifikasi->tanggal_habis);

                if (!is_numeric($overdue)) {
                    $expired = 'secondary';
                } elseif ($overdue > 45) {
                    $expired = 'success'; // masih aman
                } elseif ($overdue > 0 && $overdue <= 45) {
                    $expired = 'warning'; // akan expired dalam 45 hari
                } elseif ($overdue <= 0) {
                    $expired = 'danger'; // sudah expired
                }
            }

            $mulai = $sio->tanggal_mulai_ikatan_dinas ? date('Y', strtotime($sio->tanggal_mulai_ikatan_dinas)) : null;
            $selesai = $sio->tanggal_selesai_ikatan_dinas ? date('Y', strtotime($sio->tanggal_selesai_ikatan_dinas)) : null;
            $ikatan_dinas = $mulai && $selesai ? "$mulai - $selesai" : '-';

            $array = [
                $key + 1,
                $sio->perusahaan->nama_perusahaan,
                '<a class="text-hover-dark" href="javascript:" onClick="showSertifikasi(\'' . $sio->id . '\',\'' . $sio->nama_perizinan . '\', \'' . $sio->status . '\')">
                    <i class="fa fa-archive text-danger font-size-sm"></i>
                    ' . $sio->nama_perizinan . '
                </a>',
                $sio->nama_karyawan ?? '-',
                $sio->nik_karyawan ?? '-',
                // "<a class='text-hover-dark' href='javascript:' onClick=\"showDepartmentModal('$sio->id')\">
                //     <i class='text-danger font-size-sm'></i>
                //     " . ($sio->dept_id ?? 'Belum ditambahkan') . "
                // </a>",

                $sio->department->name ?? '-',
                $ikatan_dinas ?? '-',

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
            'data' => $sertifikasi_sio,
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

        $request->validate([
            'id' => 'required|exists:sigra_sio,id',
            'perusahaan' => 'required|integer|exists:sigra_perusahaan,id',
            'nama_perizinan' => 'required|string|max:150',
            'nama_karyawan' => 'required|string|max:150',
            'nik_karyawan' => 'required|string|max:150',
            'dept_id' => 'required|integer|exists:departments,id',

            'tanggal_mulai_ikatan_dinas' => 'nullable|date|required_with:tanggal_selesai_ikatan_dinas',
            'tanggal_selesai_ikatan_dinas' => 'nullable|date|required_with:tanggal_mulai_ikatan_dinas|after_or_equal:tanggal_mulai_ikatan_dinas',
        ]);

        $data->id_perusahaan = $request->perusahaan;
        $data->nama_perizinan = $request->nama_perizinan;
        $data->nama_karyawan = $request->nama_karyawan;
        $data->nik_karyawan = $request->nik_karyawan;
        $data->dept_id = $request->dept_id;
        $data->tanggal_mulai_ikatan_dinas = $request->tanggal_mulai_ikatan_dinas;
        $data->tanggal_selesai_ikatan_dinas = $request->tanggal_selesai_ikatan_dinas;

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
