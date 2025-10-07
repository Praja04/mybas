<?php

namespace App\Http\Controllers\Sigra;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sigra\Perusahaan;
use App\Models\Sigra\MDMiInstan;
use App\Models\Sigra\SertifikatMDMiInstan;
use App\Models\Attachment;

class MDMiInstanController extends Controller
{
    public function index()
    {
        $perisahaan = Perusahaan::all();
        return view('sigra.md-mi-instan', [
            'perusahaan' => $perisahaan
        ]);
    }

    public function store(Request $request)
    {
        $data = new MDMiInstan;
        $data->id_perusahaan = $request->perusahaan;
        $data->varian = $request->nama_varian;
        $data->no_md = $request->no_md;
        $data->save();
        return response()->json(['success' => 1, 'message' => 'Berhasil membuat data']);
    }

    public function getAll()
    {
        $data = [];
        $sni = MDMiInstan::where('status', '!=', 'deleted')->get();

        foreach ($sni as $key => $d)
        {
            if($d->status == 'inactive') {
                $label_status = 'secondary';
            }else{
                $label_status = 'success';
            }

            $sertifikat = SertifikatMDMiInstan::where('id_varian', $d->id)->where('status', '!=', 'deleted')->orderBy('tanggal_expired', 'desc')->first();

            if($sertifikat != null) {
                $overdue = $this->expired($sertifikat->tanggal_expired);
                if($overdue <= 30) {
                    // Jika 30 hari lagi ke expired
                    $expired = 'warning';
                }elseif($overdue < 0) {
                    $expired = 'danger';
                }else{
                    $expired = 'success';
                }
            }

            $array = [
                $key+1,
                $d->perusahaan->nama_perusahaan,
                '<a class="text-hover-dark" href="javascript:" onClick="showSertifikat(\''.$d->id.'\',\''.$d->varian.'\', \''.$d->status.'\')">
                    <i class="fa fa-archive text-danger font-size-sm"></i>
                    '.$d->varian.'
                </a>',
                $d->no_md,
                '<span class="label label-inline label-'.$label_status.'">'.$d->status.'</span>',
                $sertifikat == null ? '-' : $this->formatTanggal($sertifikat->tanggal_terbit),
                $sertifikat == null ? '-' : $sertifikat->tanggal_expired == null ? '-' : '<span class="label label-inline label-outline-'.$expired.'">'.$this->formatTanggal($sertifikat->tanggal_expired).'</span>',
                $sertifikat == null ? '-' : $sertifikat->masa_berlaku,
                $sertifikat == null ? '-' : $sertifikat->keterangan,
                '
                <a onClick="edit(\''.$d->id.'\')" title="Edit" href="javascript:" class="fa fa-edit text-hover-dark mr-2"></a>
                <a onClick="deleteItem(\''.$d->id.'\')" title="Hapus" href="javascript:" class="fa fa-trash text-hover-dark"></a>'

            ];
            $data[] = $array;
        }
        return response()->json([
            'success' => 1,
            'message' => 'Get data succeed',
            'data' => $data
        ]);
    }

    public function getSertifikat($id)
    {
        $sertifikat = SertifikatMDMiInstan::where('id_varian', $id)->where('status', '!=', 'deleted')->orderBy('tanggal_expired', 'desc')->get();
        foreach($sertifikat as $s) {
            $s->attachments;
        }
        return response()->json(['success' => 1, 'data' => $sertifikat], 200);
    }

    public function get($id)
    {
        $data = MDMiInstan::find($id);
        return response()->json(['success' => 1, 'message' => 'Get data succeed', 'data' => $data]);
    }

    public function update(Request $request)
    {
        $data = MDMiInstan::find($request->id);
        $data->id_perusahaan = $request->perusahaan;
        $data->varian = $request->nama_varian;
        $data->no_md = $request->no_md;
        $data->save();
        return response()->json(['success' => 1, 'message' => 'Update data succeed']);
    }

    public function delete($id)
    {
        $data = MDMiInstan::find($id);
        $data->status = 'deleted';
        $data->save();
        return response()->json(['success' => 1, 'message' => 'Delete data succeed']);
    }

    public function createSertifikat(Request $request)
    {
        // dd($request->all());
        $sertifikasi = new SertifikatMDMiInstan;
        $sertifikasi->id_varian = $request->varian_id;
        $sertifikasi->tanggal_terbit = $request->tanggal_sertifikasi;
        $sertifikasi->tanggal_expired = $request->tanggal_expired;
        $sertifikasi->masa_berlaku = $request->masa_berlaku;
        $sertifikasi->keterangan = $request->remarks;
        $sertifikasi->status = 'Created';
        $sertifikasi->transaction_id = $request->create_sertifikat_transaction_id;
        $sertifikasi->save();

        // Change attachment draft to no
        $attachment = Attachment::where('transaction_id', $request->create_sertifikat_transaction_id)
        ->update(['is_draft' => 'N']);
        

        return response()->json([
            'success' => 1,
            'message' => 'Sertifikat created successfully'
        ]);
    }

    public function editSertifikat(Request $request)
    {
        $sertifikasi = SertifikatMDMiInstan::find($request->id);
        $sertifikasi->id_varian = $request->varian_id;
        $sertifikasi->tanggal_terbit = $request->tanggal_sertifikasi;
        $sertifikasi->tanggal_expired = $request->tanggal_expired;
        $sertifikasi->masa_berlaku = $request->masa_berlaku;
        $sertifikasi->keterangan = $request->remarks;
        $sertifikasi->status = 'Updated';
        $sertifikasi->transaction_id = $request->create_sertifikat_transaction_id;
        $sertifikasi->save();

        // Change attachment draft to no
        $attachment = Attachment::where('transaction_id', $request->create_sertifikat_transaction_id)
        ->update(['is_draft' => 'N']);
        

        return response()->json([
            'success' => 1,
            'message' => 'Sertifikat updated successfully'
        ]);
    }

    public function getAttachments($id)
    {
        $sertifikasi = SertifikatMDMiInstan::find($id);
        $attachments = Attachment::where('transaction_id', $sertifikasi->transaction_id)->get();
        return response()->json([
            'success' => 1,
            'message' => 'Get attachments succeed',
            'data' => $attachments
        ]);
    }

    public function setStatus(Request $request)
    {
        $data = MDMiInstan::find($request->id);
        $data->status = $request->status;
        $data->save();
        return response()->json(['success' => 1, 'message' => 'Change status succeed']);
    }
}
