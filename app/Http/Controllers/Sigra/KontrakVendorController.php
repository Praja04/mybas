<?php

namespace App\Http\Controllers\Sigra;

use App\Http\Controllers\Controller;
use App\Models\Sigra\KontrakVendor;
use App\Models\Sigra\MasterVendor;
use App\Models\Sigra\Perusahaan;
use App\Imports\UploadSigraKontrakVendorImport;
use App\Exports\KontrakVendorExport;
// use App\Models\Attachment;
use App\Models\LocalAttachment;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;

class KontrakVendorController extends Controller
{
    public function exportKontrak()
    {
        return Excel::download(new KontrakVendorExport, 'kontrak-karyawan.xls');
    }

    public function import_excel(Request $request)
    {
        $validasi_file = Validator::make($request->all(), [
            'excel' => 'required|mimes:xls,xlsx',
        ]);

        if ($validasi_file->fails()) {
            Session::flash('error', 'Format File Tidak Sesuai!');
            return back();
        }

        try {
            Excel::import(new UploadSigraKontrakVendorImport, $request->file('excel'));

            Session::flash('success', 'Data Sigra berhasil di import!');
        } catch (\Exception $e) {
            Session::flash('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }

        return back();
    }

    public function index()
    {
        $perusahaan = Perusahaan::all();
        $kontrak_vendors = KontrakVendor::all();
        return view('sigra.kontrak-vendor', [
            'kontrak_vendors' => $kontrak_vendors,
            'perusahaan' => $perusahaan
        ]);
    }

    public function getAll()
    {
        $kontrak_vendors = [];
        $vendors = MasterVendor::where('status', '!=', 'deleted')->get();

        foreach ($vendors as $key => $data) {
            if ($data->status == 'inactive') {
                $label_status = 'secondary';
            } else {
                $label_status = 'success';
            }

            $kontrak_vendor = KontrakVendor::where('id_vendor', $data->id)->where('status', '!=', 'deleted')->orderBy('tanggal_selesai', 'desc')->first();

            if ($kontrak_vendor != null) {
                $overdue = $this->expired($kontrak_vendor->tanggal_selesai);
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
                $key + 1,
                $data->perusahaan->nama_perusahaan,
                '<a class="text-hover-dark" href="javascript:" onClick="showKontrak(\'' . $data->id . '\',\'' . $data->nama_vendor . '\', \'' . $data->status . '\')">
                    <i class="fa fa-archive text-danger font-size-sm"></i>
                    ' . $data->nama_vendor . '
                </a>',
                $data->jenis_pekerjaan,
                $kontrak_vendor == null ? '-' : $this->formatTanggal($kontrak_vendor->tanggal_mulai),
                $kontrak_vendor == null ? '-' : $this->formatTanggal($kontrak_vendor->tanggal_selesai),
                $kontrak_vendor ? number_format($kontrak_vendor->value, 0, ',', '.') : '-',
                $kontrak_vendor == null ? '-' : $kontrak_vendor->keterangan,
                '<span class="label label-inline label-' . $label_status . '">' . $data->status . '</span>',
                '
                <a onClick="edit(\'' . $data->id . '\')" title="Edit" href="javascript:" class="fa fa-edit text-hover-dark mr-2"></a>
                <a onClick="deleteItem(\'' . $data->id . '\')" title="Hapus" href="javascript:" class="fa fa-trash text-hover-dark"></a>'

            ];
            $kontrak_vendors[] = $array;
        }
        return response()->json([
            'success' => 1,
            'message' => 'Get data perizinan succeed',
            'data' => $kontrak_vendors
        ]);
    }

    public function getKontrak($id)
    {
        $kontrak = KontrakVendor::where('id_vendor', $id)->where('status', '!=', 'deleted')->orderBy('tanggal_selesai', 'desc')->get();
        foreach ($kontrak as $k) {
            $k->attachments;
        }
        return response()->json(['success' => 1, 'data' => $kontrak], 200);
    }

    public function create(Request $request)
    {
        $kontrak = new KontrakVendor;
        $kontrak->id_vendor = $request->vendor_id;
        $kontrak->tanggal_mulai = $request->tanggal_mulai;
        $kontrak->tanggal_selesai = $request->tanggal_selesai;
        $kontrak->value = $request->value;
        $kontrak->status = 'Created';
        $kontrak->keterangan = $request->remarks;
        $kontrak->transaction_id = $request->create_kontrak_transaction_id;
        $kontrak->save();

        // Change attachment draft to no
        $attachment = LocalAttachment::where('transaction_id', $request->create_kontrak_transaction_id)
            ->update(['is_draft' => 'N']);


        return response()->json([
            'success' => 1,
            'message' => 'Contract created successfully'
        ]);
    }

    public function update(Request $request)
    {
        $kontrak = KontrakVendor::find($request->id);
        $kontrak->id_vendor = $request->vendor_id;
        $kontrak->tanggal_mulai = $request->tanggal_mulai;
        $kontrak->tanggal_selesai = $request->tanggal_selesai;
        $kontrak->value = $request->value;
        $kontrak->keterangan = $request->remarks;
        $kontrak->status = 'Created';
        $kontrak->transaction_id = $request->create_kontrak_transaction_id;
        $kontrak->save();
        // Change attachment draft to no
        $attachment = LocalAttachment::where('transaction_id', $request->create_kontrak_transaction_id)
            ->update(['is_draft' => 'N']);


        return response()->json([
            'success' => 1,
            'message' => 'Contract updated successfully'
        ]);
    }

    public function get($id)
    {
        $kontrak = KontrakVendor::find($id);
        return response()->json([
            'success' => 1,
            'data' => $kontrak,
            'message' => 'Get sertifikasi succeed'
        ]);
    }

    public function delete($id)
    {
        $data = KontrakVendor::find($id);
        $data->status = 'deleted';
        $data->save();
        return response()->json(['success' => 1, 'message' => 'Delete data succeed']);
    }

    public function getAttachments($id)
    {
        $kontrak = KontrakVendor::find($id);
        $attachments = LocalAttachment::where('transaction_id', $kontrak->transaction_id)->get();
        return response()->json([
            'success' => 1,
            'message' => 'Get attachments succeed',
            'data' => $attachments
        ]);
    }

    public function ubahStatus(Request $request)
    {
        $data = KontrakVendor::find($request->id);
        $data->status = $request->status;
        $data->save();
        return response()->json(['success' => 1, 'message' => 'Change status succeed']);
    }
}
