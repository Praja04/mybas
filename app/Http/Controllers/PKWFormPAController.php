<?php

namespace App\Http\Controllers;

use App\PKWAspekPenilaian;
use App\PKWFormPA;
use App\PKW;
use App\PKWApproval;
use App\PKWFormPAAspekPenilaian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\PKWNotification;

class PKWFormPAController extends Controller
{
    public function index()
    {
        $aspek_penilaian = PKWAspekPenilaian::all();
        return view('pkw.form-pa.form-list', ['aspek_penilaian' => $aspek_penilaian]);
    }

    public function approvePage()
    {
        $aspek_penilaian = PKWAspekPenilaian::all();
        return view('pkw.form-pa.approve-page', ['aspek_penilaian' => $aspek_penilaian]);
    }

    public function formatTanggal($date)
    {
        $year = explode('-', $date)[0];
        $month = explode('-', $date)[1];
        $date = explode('-', $date)[2];
        return $date . '/' . $month . '/' . $year;
    }

    public function approve(Request $request)
    {
        //        dd($request->all());
        $list_email = [];
        foreach ($request->id as $id) {
            $form = PKWFormPA::find($id);
            if ($form->status == 'created') {
                return response()->json(['success' => 0, 'message' => 'Ada form yang belum di isi']);
            }
            if ($request->approve_value == 'approve1') {
                $form->nama_supervisor = Auth::user()->name;
                $form->supervisor_approve = 'Y';
                $form->supervisor_approve_time = date('Y-m-d H:i:s');
                $form->status = $request->approve_value;
                $form->save();
                // Letika spv approve. Kirim email notifikasi ke manager.

                $approve2_email = $form->approval->approval_2->email;
                if ($approve2_email != null) {
                    if (!in_array($approve2_email, $list_email)) {
                        $list_email[] = $approve2_email;
                    }
                }
            } elseif ($request->approve_value == 'approve2') {
                $form->nama_manager = Auth::user()->name;
                $form->manager_approve = 'Y';
                $form->manager_approve_time = date('Y-m-d H:i:s');
                $form->status = 'done';
                $form->save();

                if ((int)$form->pkw->kontrak_ke > 1) {
                    // Jika approval 2 dan approval 3 masih orang yang sama, maka langsung approve saja
                    if ($form->approval->approval2 == $form->approval->approval3) {
                        $form->nama_fm = Auth::user()->id;
                        $form->fm_approve = 'Y';
                        $form->fm_approve_time = date('Y-m-d H:i:s');
                        $form->status = 'approve3';
                        $form->save();
                    } else {
                        $approve3_email = $form->approval->approval_3->email;
                        if ($approve3_email != null) {
                            if (!in_array($approve3_email, $list_email)) {
                                $list_email[] = $approve3_email;
                            }
                        }
                    }
                }
            } elseif ($request->approve_value == 'approve3') {
                $form->nama_fm = Auth::user()->id;
                $form->fm_approve = 'Y';
                $form->fm_approve_time = date('Y-m-d H:i:s');
                $form->status = 'approve3';
                $form->save();
            } else {
                return response()->json(['success' => 0, 'message' => 'Hmmmm Error']);
            }
        }

        foreach ($list_email as $email) {
            // Kirim email
            Mail::to($email)->send(new PKWNotification('PKWTT PAS', 'APPROVE FORM PA', 'Ada form PA baru harus di approve nih'));
        }

        return response()->json(['success' => 1, 'message' => 'Approve form succeed']);
    }

    public function getOne($id_form)
    {
        $f = PKWFormPA::find($id_form);
        $k = $f->pkw->karyawan;
        $data = [
            'id' => $f->id,
            'nik' => $k->nik,
            'nama' => $k->nama,
            'alamat_rumah' => $k->alamat_rumah,
            'alamat_rumah_luar_kota' => $k->alamat_rumah_luar_kota,
            'jenis_kelamin' => $k->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan',
            'tempat_lahir' => $k->tempat_lahir,
            'tanggal_lahir' => $this->formatTanggal($k->tanggal_lahir),
            'divisi' => $k->divisi->nama_divisi ?? '-',
            'bagian' => $k->bagian->nama_bagian ?? '-',
            'jabatan' => $k->jabatan->nama_jabatan ?? '-',
            'group' => $k->group->nama_group ?? '-',
            'agama' => $k->agama,
            'tanggal_masuk' => $k->tanggal_masuk != '' ? $this->formatTanggal($k->tanggal_masuk) : '',
            'status_perdata' => $k->status_perdata,
            'nama_pasangan' => $k->nama_pasangan,
            'tempat_pernikahan' => $k->tempat_pernikahan,
            'tanggal_pernikahan' => $k->tanggal_pernikahan != '' ? $this->formatTanggal($k->tanggal_pernikahan) : '',
            'tempat_lahir_pasangan' => $k->tempat_lahir_pasangan,
            'tanggal_lahir_pasangan' => $k->tanggal_lahir_pasangan,
            'pekerjaan_pasangan' => $k->pekerjaan_pasangan,
            'tempat_pasangan_bekarja' => $k->tempat_pasangan_bekarja,
            'nama_ayah' => $k->nama_ayah,
            'tempat_lahir_ayah' => $k->tempat_lahir_ayah,
            'tanggal_lahir_ayah' => $k->tanggal_lahir_ayah,
            'nama_ibu' => $k->nama_ibu,
            'tempat_lahir_ibu' => $k->tempat_lahir_ibu,
            'tanggal_lahir_ibu' => $k->tanggal_lahir_ibu,
            'nama_ayah_mertua' => $k->nama_ayah_mertua,
            'tempat_lahir_ayah_mertua' => $k->tempat_lahir_ayah_mertua,
            'tanggal_lahir_ayah_mertua' => $k->tanggal_lahir_ayah_mertua,
            'nama_ibu_mertua' => $k->nama_ibu_mertua,
            'tempat_lahir_ibu_mertua' => $k->tempat_lahir_ibu_mertua,
            'tanggal_lahir_ibu_mertua' => $k->tanggal_lahir_ibu_mertua,
            'nama_kontak_darurat' => $k->nama_kontak_darurat,
            'hubungan_kontak_darurat' => $k->hubungan_kontak_darurat,
            'no_tlepon_kontak_darurat' => $k->no_telepon_kontak_darurat,
            'nomor_rekening_bank' => $k->nomor_rekening_bank,
            'nomor_kartu_bpjs_ketenagakerjaan' => $k->nomor_kartu_bpjs_ketenagakerjaan,
            'keterangan_kartu_bpjs_ketenagakerjaan' => $k->keterangan_kartu_bpjs_ketenagakerjaan,
            'nomor_kartu_bpjs_kesehatan' => $k->nomor_kartu_bpjs_kesehatan,
            'keterangan_kartu_bpjs_kesehatan' => $k->keterangan_kartu_bpjs_kesehatan,
            'kontrak_ke' => $f->pkw->jenis == 'PKWT' ? 'Kontrak ke ' . $f->pkw->kontrak_ke : 'Probation ke ' . $f->pkw->kontrak_ke,
            'jenis_kontrak' => $f->pkw->jenis,
            'mulai_kontrak' => $this->formatTanggal($f->pkw->tanggal_mulai),
            'selesai_kontrak' => $this->formatTanggal($f->pkw->tanggal_selesai),
            'fungsi_penilaian' => $f->fungsi_penilaian,
            'kesimpulan' => $f->kesimpulan,
            'aspek_penilaian' => $f->aspek_penilaian,
            'evaluasi' => $f->evaluasi_keseluruhan,
            'status' => $f->status,
            'nama_supervisor' => $f->nama_supervisor != null ? $f->nama_supervisor : Auth::user()->name
        ];
        return response()->json(['success' => 1, 'data' => $data]);
    }

    public function getAll()
    {
        $data = [];
        // approval status off me
        $approval = PKWApproval::where('approval1', Auth::user()->id)->first();
        // dd($approval);
        // $form = PKWFormPA::where('status', 'created')->orWhere('status', 'filled')->get();
        $form = PKWFormPA::where(function ($query) {
            $query->where('status', 'created')
                ->orWhere('status', 'filled');
        })->where('id_approval', $approval->id)->get();
        foreach ($form as $f) {
            $k = $f->pkw->karyawan;
            $data[] = [
                'id' => $f->id,
                'nik' => $k->nik,
                'nama' => $k->nama,
                'alamat_rumah' => $k->alamat_rumah,
                'jenis_kelamin' => $k->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan',
                'tempat_lahir' => $k->tempat_lahir,
                'tanggal_lahir' => $this->formatTanggal($k->tanggal_lahir),
                'divisi' => $k->divisi->nama_divisi ?? '-',
                'bagian' => $k->bagian->nama_bagian ?? '-',
                'agama' => $k->agama,
                'tanggal_masuk' => $k->tanggal_masuk != '' ? $this->formatTanggal($k->tanggal_masuk) : '',
                'status_perdata' => $k->status_perdata,
                'nama_pasangan' => $k->nama_pasangan,
                'tempat_pernikahan' => $k->tempat_pernikahan,
                'tanggal_pernikahan' => $k->tanggal_pernikahan != '' ? $this->formatTanggal($k->tanggal_pernikahan) : '',
                'tempat_lahir_pasangan' => $k->tempat_lahir_pasangan,
                'tanggal_lahir_pasangan' => $k->tanggal_lahir_pasangan,
                'pekerjaan_pasangan' => $k->pekerjaan_pasangan,
                'tempat_pasangan_bekarja' => $k->tempat_pasangan_bekarja,
                'nama_ayah' => $k->nama_ayah,
                'tempat_lahir_ayah' => $k->tempat_lahir_ayah,
                'tanggal_lahir_ayah' => $k->tanggal_lahir_ayah,
                'nama_ibu' => $k->nama_ibu,
                'tempat_lahir_ibu' => $k->tempat_lahir_ibu,
                'tanggal_lahir_ibu' => $k->tanggal_lahir_ibu,
                'nama_ayah_mertua' => $k->nama_ayah_mertua,
                'tempat_lahir_ayah_mertua' => $k->tempat_lahir_ayah_mertua,
                'tanggal_lahir_ayah_mertua' => $k->tanggal_lahir_ayah_mertua,
                'nama_ibu_mertua' => $k->nama_ibu_mertua,
                'tempat_lahir_ibu_mertua' => $k->tempat_lahir_ibu_mertua,
                'tanggal_lahir_ibu_mertua' => $k->tanggal_lahir_ibu_mertua,
                'nama_kontak_darurat' => $k->nama_kontak_darurat,
                'hubungan_kontak_darurat' => $k->hubungan_kontak_darurat,
                'no_tlepon_kontak_darurat' => $k->no_telepon_kontak_darurat,
                'nomor_rekening_bank' => $k->nomor_rekening_bank,
                'nomor_kartu_bpjs_ketenagakerjaan' => $k->nomor_kartu_bpjs_ketenagakerjaan,
                'keterangan_kartu_bpjs_ketenagakerjaan' => $k->keterangan_kartu_bpjs_ketenagakerjaan,
                'nomor_kartu_bpjs_kesehatan' => $k->nomor_kartu_bpjs_kesehatan,
                'keterangan_kartu_bpjs_kesehatan' => $k->keterangan_kartu_bpjs_kesehatan,
                'kontrak_ke' => $f->pkw->jenis == 'PKWT' ? 'Kontrak ke ' . $f->pkw->kontrak_ke : 'Probation ke ' . $f->pkw->kontrak_ke,
                'jenis_pkw' => $f->pkw->jenis,
                'status' => $f->status
            ];
        }
        $response = [
            'meta' => [
                'page' => 1,
                'pages' => 1,
                'perpage' => 10,
                'total' => count($form),
                'field' => 'id'
            ],
            'data' => $data
        ];
        return response()->json(['success' => 1, 'data' => $response]);
    }

    public function getFilled()
    {
        $data = [];
        $form = PKWFormPA::where('status', '!=', 'created')->where('status', '!=', 'filled')->where('status', '!=', 'done')->whereHas('approval', function ($query) {
            $query->where('approval2', Auth::user()->id)
                ->orWhere('approval3', Auth::user()->id);
        })->get();
        foreach ($form as $f) {
            $k = $f->pkw->karyawan;
            $data[] = [
                'id' => $f->id,
                'nik' => $k->nik,
                'nama' => $k->nama,
                'alamat_rumah' => $k->alamat_rumah,
                'jenis_kelamin' => $k->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan',
                'tempat_lahir' => $k->tempat_lahir,
                'tanggal_lahir' => $this->formatTanggal($k->tanggal_lahir),
                'divisi' => $k->divisi->nama_divisi ?? '-',
                'bagian' => $k->bagian->nama_bagian ?? '-',
                'agama' => $k->agama,
                'tanggal_masuk' => $k->tanggal_masuk != '' ? $this->formatTanggal($k->tanggal_masuk) : '',
                'status_perdata' => $k->status_perdata,
                'nama_pasangan' => $k->nama_pasangan,
                'tempat_pernikahan' => $k->tempat_pernikahan,
                'tanggal_pernikahan' => $k->tanggal_pernikahan != '' ? $this->formatTanggal($k->tanggal_pernikahan) : '',
                'tempat_lahir_pasangan' => $k->tempat_lahir_pasangan,
                'tanggal_lahir_pasangan' => $k->tanggal_lahir_pasangan,
                'pekerjaan_pasangan' => $k->pekerjaan_pasangan,
                'tempat_pasangan_bekarja' => $k->tempat_pasangan_bekarja,
                'nama_ayah' => $k->nama_ayah,
                'tempat_lahir_ayah' => $k->tempat_lahir_ayah,
                'tanggal_lahir_ayah' => $k->tanggal_lahir_ayah,
                'nama_ibu' => $k->nama_ibu,
                'tempat_lahir_ibu' => $k->tempat_lahir_ibu,
                'tanggal_lahir_ibu' => $k->tanggal_lahir_ibu,
                'nama_ayah_mertua' => $k->nama_ayah_mertua,
                'tempat_lahir_ayah_mertua' => $k->tempat_lahir_ayah_mertua,
                'tanggal_lahir_ayah_mertua' => $k->tanggal_lahir_ayah_mertua,
                'nama_ibu_mertua' => $k->nama_ibu_mertua,
                'tempat_lahir_ibu_mertua' => $k->tempat_lahir_ibu_mertua,
                'tanggal_lahir_ibu_mertua' => $k->tanggal_lahir_ibu_mertua,
                'nama_kontak_darurat' => $k->nama_kontak_darurat,
                'hubungan_kontak_darurat' => $k->hubungan_kontak_darurat,
                'no_tlepon_kontak_darurat' => $k->no_telepon_kontak_darurat,
                'nomor_rekening_bank' => $k->nomor_rekening_bank,
                'nomor_kartu_bpjs_ketenagakerjaan' => $k->nomor_kartu_bpjs_ketenagakerjaan,
                'keterangan_kartu_bpjs_ketenagakerjaan' => $k->keterangan_kartu_bpjs_ketenagakerjaan,
                'nomor_kartu_bpjs_kesehatan' => $k->nomor_kartu_bpjs_kesehatan,
                'keterangan_kartu_bpjs_kesehatan' => $k->keterangan_kartu_bpjs_kesehatan,
                'kontrak_ke' => $f->pkw->jenis == 'PKWT' ? 'Kontrak ke ' . $f->pkw->kontrak_ke : 'Probation ke ' . $f->pkw->kontrak_ke,
                'jenis_pkw' => $f->pkw->jenis,
                'status' => $f->status,
                'kesimpulan' => $f->kesimpulan,
            ];
        }
        $response = [
            'meta' => [
                'page' => 1,
                'pages' => 1,
                'perpage' => 10,
                'total' => count($form),
                'field' => 'id'
            ],
            'data' => $data
        ];
        return response()->json(['success' => 1, 'data' => $response]);
    }

    public function create(Request $request)
    {
        $list_email = [];
        foreach ($request->id as $id) {
            //
            $pkw = PKW::find($id);
            if ($pkw != null) {
                $karyawan = $pkw->karyawan;
                $approval = PKWApproval::where('id_bagian', $karyawan->id_bagian)->first();
                if ($approval == null) {
                    return response()->json(['success' => 0, 'message' => 'Karyawan ' . $karyawan->nik . ', Approval tidak ditemukan untuk bagian ' . $karyawan->bagian->nama_bagian]);
                }

                // Cek dulu apakah form pa belum ada
                $form_pa = PKWFormPA::where('id_pkw', $pkw->id)->where('id_approval', $approval->id)->first();
                if ($form_pa == null) {
                    // Buat form PA baru
                    $form_pa = new PKWFormPA;
                    $form_pa->id_pkw = $id;
                    $form_pa->id_approval = $approval->id;
                    $form_pa->tanggal_create = date('Y-m-d');
                    $form_pa->status = 'created';
                    $form_pa->nama_supervisor = $approval->approval_1->name;
                    $form_pa->nama_manager = $approval->approval_2->name;
                    $form_pa->nama_fm = $approval->approval_3->name;
                    $form_pa->save();
                }

                // Email dibuat akumulasi
                $email = $approval->approval_1->email;
                if ($email != null) {
                    if (!in_array($email, $list_email)) {
                        $list_email[] = $email;
                    }
                }
            }
        }

        foreach ($list_email as $email) {
            // Kirim email
            Mail::to($email)->send(new PKWNotification('PKWTT PAS', 'ISI FORM PA', 'Ada form PA baru harus di isi nih'));
        }

        return response()->json(['success' => 1, 'message' => 'Create form PA succeed']);
    }

    public function store(Request $request)
    {
        $aspek_penilaian = json_decode($request->aspek_penilaian);
        $form_pa = PKWFormPA::find($request->id_form_pa);
        $form_pa->tanggal_penilaian = date('Y-m-d');
        $form_pa->fungsi_penilaian = $request->fungsi_penilaian;
        $form_pa->evaluasi_keseluruhan = $request->evaluasi;
        $form_pa->kesimpulan = $request->kesimpulan;
        $form_pa->status = 'filled';
        $form_pa->save();
        $id = $form_pa->id;

        // Delete dulu semua aspek penilaian untuk form PA ini.
        $deletePenilaian = PKWFormPAAspekPenilaian::where('id_form_pa', $id)->delete();

        foreach ($aspek_penilaian as $aspek) {
            $fa = new PKWFormPAAspekPenilaian;
            $fa->id_form_pa = $id;
            $fa->id_aspek_penilaian = $aspek;
            $fa->skala = $request['skala_' . $aspek];
            $fa->catatan = $request['catatan_' . $aspek];
            $fa->save();
        }

        return response()->json(['success' => 1, 'message' => 'Fill form pa succeed']);
    }
}
