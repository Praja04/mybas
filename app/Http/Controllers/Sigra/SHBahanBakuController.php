<?php

namespace App\Http\Controllers\Sigra;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sigra\Perusahaan;
use App\Models\Sigra\SHBahanBaku;
use App\Models\Sigra\SertifikatSHBahanBaku;
use App\Models\Attachment;

class SHBahanBakuController extends Controller
{
    public function index()
    {
        $perisahaan = Perusahaan::all();
        return view('sigra.sh-bahan-baku', [
            'perusahaan' => $perisahaan
        ]);
    }

    public function store(Request $request)
    {
        $data = new SHBahanBaku;
        $data->id_perusahaan = $request->perusahaan;
        $data->nama_bahan = $request->nama_bahan;
        $data->save();
        return response()->json(['success' => 1, 'message' => 'Berhasil membuat data']);
    }

    public function getAll()
    {
        $data = [];
        $sni = SHBahanBaku::where('status', '!=', 'deleted')->get();

        foreach ($sni as $key => $d)
        {
            if($d->status == 'inactive') {
                $label_status = 'secondary';
            }else{
                $label_status = 'success';
            }

            $sertifikat = SertifikatSHBahanBaku::where('id_bahan', $d->id)->where('status', '!=', 'deleted')->orderBy('tanggal_expired', 'desc')->first();

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
                '<a class="text-hover-dark" href="javascript:" onClick="showSertifikat(\''.$d->id.'\',\''.$d->nama_bahan.'\', \''.$d->status.'\')">
                    <i class="fa fa-archive text-danger font-size-sm"></i>
                    '.$d->nama_bahan.'
                </a>',
                '<span class="label label-inline label-'.$label_status.'">'.$d->status.'</span>',
                $sertifikat == null ? '-' : $sertifikat->no_sh,
                $sertifikat == null ? '-' : $sertifikat->produsen,
                $sertifikat == null ? '-' : $sertifikat->pemasok,
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
        $sertifikat = SertifikatSHBahanBaku::where('id_bahan', $id)->where('status', '!=', 'deleted')->orderBy('tanggal_expired', 'desc')->get();
        foreach($sertifikat as $s) {
            $s->attachments;
        }
        return response()->json(['success' => 1, 'data' => $sertifikat], 200);
    }

    public function get($id)
    {
        $data = SHBahanBaku::find($id);
        return response()->json(['success' => 1, 'message' => 'Get data succeed', 'data' => $data]);
    }

    public function update(Request $request)
    {
        $data = SHBahanBaku::find($request->id);
        $data->id_perusahaan = $request->perusahaan;
        $data->nama_bahan = $request->nama_bahan_baku;
        $data->save();
        return response()->json(['success' => 1, 'message' => 'Update data succeed']);
    }

    public function delete($id)
    {
        $data = SHBahanBaku::find($id);
        $data->status = 'deleted';
        $data->save();
        return response()->json(['success' => 1, 'message' => 'Delete data succeed']);
    }

    public function createSertifikat(Request $request)
    {
        // dd($request->all());
        $sertifikasi = new SertifikatSHBahanBaku;
        $sertifikasi->id_bahan = $request->id_bahan;
        $sertifikasi->produsen = $request->produsen;
        $sertifikasi->pemasok = $request->pemasok;
        $sertifikasi->no_sh = $request->no_sh;
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
        // dd($request->all());
        $sertifikasi = SertifikatSHBahanBaku::find($request->id);
        $sertifikasi->id_bahan = $request->id_bahan;
        $sertifikasi->produsen = $request->produsen;
        $sertifikasi->pemasok = $request->pemasok;
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
        $sertifikasi = SertifikatSHBahanBaku::find($id);
        $attachments = Attachment::where('transaction_id', $sertifikasi->transaction_id)->get();
        return response()->json([
            'success' => 1,
            'message' => 'Get attachments succeed',
            'data' => $attachments
        ]);
    }

    public function setStatus(Request $request)
    {
        $data = SHBahanBaku::find($request->id);
        $data->status = $request->status;
        $data->save();
        return response()->json(['success' => 1, 'message' => 'Change status succeed']);
    }
}
