<?php

namespace App\Http\Controllers\Sigra;

use App\AuthGroup;
use App\Department;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sigra\Perusahaan;
use App\Models\Sigra\SIO;
use App\Exports\SIOExport;
use App\Models\Sigra\SIOSertifikasi;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\LocalAttachment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SioController extends Controller
{
    public function index()
    {
        $departments = Department::where('status', '1')->get();

        $perusahaan = Perusahaan::all();

        $flags = $this->sioAccessFlags();

        return view('sigra.sio', [
            'perusahaan' => $perusahaan,
            'departments' => $departments,
            'sioFlags'    => $flags,
        ]);
    }

    public function tambahPerizinan(Request $request)
    {
        $this->abortIfReadonly();

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
        $flags = $this->sioAccessFlags();

        $sertifikasi_sio = [];
        $sioList = SIO::with(['department', 'perusahaan', 'sertifikasi'])->where('status', '!=', 'deleted')->get();


        foreach ($sioList as $key => $sio) {
            $label_status = $sio->status == 'inactive' ? 'secondary' : 'success';

            $sertifikasi = $sio->sertifikasi->sortByDesc('tanggal_habis')->first();

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

            $harga = '-';
            if ($sertifikasi && !$flags['hide_price']) {
                $harga = number_format($sertifikasi->harga, 0, ',', '.');
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
                $sio->department->name ?? '-',
                $ikatan_dinas ?? '-',

                $sertifikasi ? $sertifikasi->nomor_izin : '-',
                '<span class="label label-inline label-' . $label_status . '">' . $sio->status . '</span>',
                $sertifikasi ? $this->formatTanggal($sertifikasi->tanggal_terbit) : '-',
                $sertifikasi ? '<span class="label label-inline label-outline-' . $expired . '">' . $this->formatTanggal($sertifikasi->tanggal_habis) . '</span>' : '-',
                $sertifikasi ? $harga : '-',
                $sertifikasi ? $sertifikasi->keterangan : '-',
                $this->actionButtons($sio),
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
        $this->abortIfReadonly();

        $request->validate([
            'sio_id' => 'required|integer|exists:sigra_sio,id',
            'nomor_izin' => 'required|string|max:50',
            'harga' => 'required|integer|min:0',
            'tanggal_sertifikasi' => 'required|date',
            'tanggal_expired' => 'nullable|date|after_or_equal:tanggal_terbit',
            'remarks' => 'nullable|string',
        ]);

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
        $flags = $this->sioAccessFlags();

        $sertifikasi = SIOSertifikasi::with('attachments')
            ->where('id_sio', $id)
            ->where('status', '!=', 'deleted')
            ->orderByDesc('tanggal_habis')
            ->get();

        return response()->json([
            'success' => 1,
            'data' => $sertifikasi,
            'permissions' => [
                'readonly' => $flags['readonly'],
                'hide_price' => $flags['hide_price'],
            ],
        ]);
    }

    public function ubahSertifikat(Request $request)
    {
        $this->abortIfReadonly();

        $request->validate([
            'sio_id' => 'required|integer|exists:sigra_sio,id',
            'nomor_izin' => 'required|string|max:50',
            'harga' => 'required|integer|min:0',
            'tanggal_sertifikasi' => 'required|date',
            'tanggal_expired' => 'nullable|date|after_or_equal:tanggal_terbit',
            'remarks' => 'nullable|string',
        ]);

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
        $flags = $this->sioAccessFlags();

        $sertifikasi = SIOSertifikasi::find($id);
        $attachments = LocalAttachment::where('transaction_id', $sertifikasi->transaction_id)->get();
        return response()->json([
            'success' => 1,
            'message' => 'Get attachments succeed',
            'data' => $attachments,
            'permissions' => [
                'readonly' => $flags['readonly'],
            ],

        ]);
    }

    public function getPerizinan($id)
    {
        $data = SIO::find($id);
        return response()->json(['success' => 1, 'message' => 'Get data succeed', 'data' => $data]);
    }


    public function update(Request $request)
    {
        $this->abortIfReadonly();

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
        $this->abortIfReadonly();

        $data = SIO::find($id);
        $data->status = 'deleted';
        $data->save();
        return response()->json(['success' => 1, 'message' => 'Delete data succeed']);
    }

    public function deleteSertifikasi($id)
    {
        $this->abortIfReadonly();

        // soft delete
        $data = SIOSertifikasi::find($id);
        if (!$data) {
            return response()->json([
                'success' => 0,
                'message' => 'Sertifikasi tidak ditemukan'
            ], 404);
        }
        try {
            $data->status = 'deleted';
            $data->save();
            return response()->json([
                'success' => 1,
                'message' => 'Delete data succeed'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => 0,
                'message' => 'Gagal menghapus sertifikasi: ' . $e->getMessage()
            ], 500);
        }

        // $sertifikasi = SIOSertifikasi::find($id);

        // if (!$sertifikasi) {
        //     return response()->json([
        //         'success' => 0,
        //         'message' => 'Sertifikasi tidak ditemukan'
        //     ], 404);
        // }

        // try {
        //     $attachments = LocalAttachment::where('transaction_id', $sertifikasi->transaction_id)->get();

        //     foreach ($attachments as $attachment) {
        //         $filePath = $attachment->transaction_type . '/' . $attachment->encode_file_name;

        //         if (Storage::disk('public')->exists($filePath)) {
        //             Storage::disk('public')->delete($filePath);
        //         }

        //         $attachment->delete();
        //     }

        //     $sertifikasi->status = 'deleted';
        //     $sertifikasi->save();

        //     return response()->json([
        //         'success' => 1,
        //         'message' => 'Sertifikasi dan semua attachment berhasil dihapus'
        //     ]);
        // } catch (\Exception $e) {
        //     return response()->json([
        //         'success' => 0,
        //         'message' => 'Gagal menghapus sertifikasi: ' . $e->getMessage()
        //     ], 500);
        // }
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
        $this->abortIfReadonly();

        $data = SIO::find($request->id);
        $data->status = $request->status;
        $data->save();
        return response()->json(['success' => 1, 'message' => 'Change status succeed']);
    }

    public function exportSio()
    {
        return Excel::download(new SIOExport, 'SIO.xls');
    }

    // Helper check permission
    private function hasPermission($permission)
    {
        $permissions = AuthGroup::find(Auth::user()->auth_group_id)
            ->permissions()
            ->pluck('codename')
            ->toArray();

        return in_array($permission, $permissions);
    }

    // Helper flag readonly & hide price
    private function sioAccessFlags()
    {
        $isReadonly = $this->hasPermission('sigra_sio_readonly');
        $hasHidePrice = $this->hasPermission('sigra_sio_readonly_hide_price');

        if ($hasHidePrice && !$isReadonly) {
            $isReadonly = true;
        }
        return [
            'readonly'   => $isReadonly,
            'hide_price' => $isReadonly && $hasHidePrice,
        ];
    }

    // Action button (edit & delete)
    private function actionButtons($sio)
    {
        if ($this->sioAccessFlags()['readonly']) {
            return '';
        }

        return '
            <a onClick="edit(\'' . $sio->id . '\')" class="fa fa-edit mr-2"></a>
            <a onClick="deleteItem(\'' . $sio->id . '\')" class="fa fa-trash"></a>
        ';
    }

    private function abortIfReadonly(): void
    {
        if ($this->sioAccessFlags()['readonly']) {
            abort(403, 'Readonly access');
        }
    }

    public function downloadAllAttachments()
    {
        $sioList = SIO::with(['sertifikasi'])->where('status', '!=', 'deleted')->get();

        $zip = new \ZipArchive;
        $fileName = 'sio_attachments_' . time() . '.zip';
        $tempDir = storage_path('app/public/temp');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }
        $zipPath = $tempDir . '/' . $fileName;

        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            $hasFiles = false;
            $addedPaths = [];

            foreach ($sioList as $sio) {
                // Folder name: [Nama Karyawan] - [NIK]
                $karyawanName = trim($sio->nama_karyawan ?? 'Karyawan_Tanpa_Nama');
                $nik = trim($sio->nik_karyawan ?? '');
                
                $folderName = $karyawanName;
                if ($nik !== '') {
                    $folderName .= ' - ' . $nik;
                }
                
                // Clean folder name from invalid characters
                $folderName = preg_replace('/[\/\\\\\:\*\?\"\<\>\|]/', '_', $folderName);

                // Get all sertifikasi for this SIO
                foreach ($sio->sertifikasi as $sertifikasi) {
                    // Get attachments
                    $attachments = LocalAttachment::where('transaction_id', $sertifikasi->transaction_id)->get();

                    foreach ($attachments as $attachment) {
                        $filePath = storage_path('app/public/' . $attachment->transaction_type . '/' . $attachment->encode_file_name);
                        
                        $originalName = $attachment->original_file_name ?: $attachment->encode_file_name;
                        // Clean filename
                        $originalName = preg_replace('/[\/\\\\\:\*\?\"\<\>\|]/', '_', $originalName);

                        $zipFilePath = $folderName . '/' . $originalName;

                        // Handle duplicates within the same employee's folder
                        $counter = 1;
                        $pathInfo = pathinfo($originalName);
                        $baseName = $pathInfo['filename'] ?? 'file';
                        $extension = isset($pathInfo['extension']) ? '.' . $pathInfo['extension'] : '';

                        while (in_array($zipFilePath, $addedPaths)) {
                            $zipFilePath = $folderName . '/' . $baseName . '_' . $counter . $extension;
                            $counter++;
                        }

                        $addedPaths[] = $zipFilePath;

                        if (file_exists($filePath)) {
                            // File exists locally
                            $zip->addFile($filePath, $zipFilePath);
                            $hasFiles = true;
                        } else {
                            // Fallback to 172.21.5.105
                            // $fallbackUrl = 'http://172.21.5.105/attachment/download/' . $attachment->id;
                            // try {
                            //     $response = \Illuminate\Support\Facades\Http::timeout(10)->get($fallbackUrl);
                            //     if ($response->successful()) {
                            //         $fileContents = $response->body();
                            //         $zip->addFromString($zipFilePath, $fileContents);
                            //         $hasFiles = true;
                            //     }
                            // } catch (\Throwable $e) {
                            //     \Illuminate\Support\Facades\Log::error("Failed to download fallback file for SIO attachment ID {$attachment->id}: " . $e->getMessage());
                            // }
                        }
                    }
                }
            }

            $zip->close();

            if (!$hasFiles) {
                if (file_exists($zipPath)) {
                    unlink($zipPath);
                }
                return back()->with('error', 'Tidak ada file attachment untuk diunduh.');
            }

            return response()->download($zipPath, 'sio_attachments_all.zip')->deleteFileAfterSend(true);
        } else {
            return back()->with('error', 'Gagal membuat file ZIP.');
        }
    }
}
