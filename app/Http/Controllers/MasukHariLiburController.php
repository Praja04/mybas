<?php

namespace App\Http\Controllers;

use App\AuthGroup;
use Illuminate\Http\Request;
use Session;
use Illuminate\Support\Facades\DB;
use App\UploadMasukHariLiburModel;
use App\Imports\UploadMasukHariLiburImport;
use Excel;
use Validator;
use App\Models\HrMasukHariLiburApproval;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use App\Department;
use App\User;
use App\Mail\MasukHariLiburApprovalMail;
use Dotenv\Result\Success;
use Illuminate\Support\Facades\Log;

class MasukHariLiburController extends Controller
{
    // match data auth:user dengan admin
    public function index()
    {
        $users = DB::table('hr_masuk_hari_libur_approval')->where('nik_admin', '=', Auth::user()->username)->get();
        return view('hr.masukharilibur.upload-data-karyawan.index', compact('users'));
    }

    public function scanPage()
    {
        return view('hr.masukharilibur.proses_scan.scan-page-mhl');
    }

    public function doScan(Request $request)
    {
        $rfid = (int)$request->rfid;

        $nik = DB::connection('192.168.178.44-admin')
            ->table('MSIDCARD')
            ->select('NIK', 'EMPNM')
            ->where(['CARDNODEVICE' => $rfid])
            ->orderByRaw('CAST(SUBSTR(NIK, 8) AS SIGNED) desc')
            ->first();

        // Ini kalo data nya ga ada di secure accesss
        if ($nik == null || $rfid == 0) {
            return response(['success' => 0, 'message' => 'Data tidak ditemukan. Silahkan Mengubungi HRD', 'data' => null]);
        }

        // Ambil data yang lebih lengkap
        $user = DB::connection('192.168.178.44-admin')
            ->table('MSIDCARD')
            ->select('NIK', 'CARDNODEVICE', 'EMPNM', 'DEPTID', 'FOTOBLOB')
            ->where(['NIK' => $nik->NIK, 'CARDNODEVICE' => (int)$request->rfid])
            ->first();

        $data_user = [
            'nik' => $user->NIK,
            'name' => $user->EMPNM,
            'department' => $user->DEPTID,
            'image' => base64_encode($user->FOTOBLOB)
        ];
        // ini untuk memvalidasi secure accesss dengan list kerja di database local
        $date = date('Y-m-d');

        $data = DB::table('hr_masuk_hari_libur')
            ->select([
                'hr_masuk_hari_libur_karyawan.id',
                'hr_masuk_hari_libur.status'
            ])
            // perbaiki validasi data scan
            ->join('hr_masuk_hari_libur_karyawan', 'hr_masuk_hari_libur.id_mhl', '=', 'hr_masuk_hari_libur_karyawan.id_mhl')
            ->where('hr_masuk_hari_libur.tanggal', $date)
            ->where('hr_masuk_hari_libur_karyawan.nik', $data_user['nik'])
            ->first();
        if ($data == null) {
            return response([
                'success' => 0, 'message' => 'Data Belum Ada di List Kerja. Silahkan Menghubungi HRD', 'data' => $data_user,
                'status' => 0
            ]);
        }
        if ($data != null) {
            if ($data->status == 1) {
                return response([
                    'success' => 0, 'message' => 'Data Belum Diapprove. Silahkan Menghubungi HRD', 'data' => $data_user,
                    'status' => 1
                ]);
            }
        }
        if ($data != null) {
            if ($data->status == 2) {
                return response([
                    'success' => 0, 'message' => 'Data Direject. Silahkan Menghubungi HRD', 'data' => $data_user,
                    'status' => 0
                ]);
            }
        }
        //Ini untuk menampilkan waktu scan dan status is_scan 'Y'
        DB::table('hr_masuk_hari_libur_karyawan')->where('id', $data->id)
            ->update([
                'waktu_scan' => now(),
                'is_scan' => 'Y'
            ]);

        return response(['success' => 1, 'message' => 'Silahkan Masuk. Selamat Bekerja', 'data' => $data_user]);
    }

    public function store(Request $request)
    {
        DB::table('hr_masuk_hari_libur')
            ->insert([
                'id_mhl' => $id_mhl,
                'tanggal' => $request->tanggal,
            ]);
        Session::flash('info', 'Data Upload Anda Berhasil Disimpan');
        return back();
    }

    // perbaiki logic disini
    public function import_excel(Request $request)
    {
        if (!$request->hasFile('excel')) {
            Session::flash('error', 'Tidak ada file yang di-upload.');
            return back();
        }

        $excel = $request->file('excel');
        $existingData = DB::table('hr_masuk_hari_libur')
            ->where('created_by', Auth::user()->name)
            ->where('tanggal', $request->tanggal)
            ->first();

        if ($existingData) {
            Session::flash('error', 'Data sudah diupload untuk tanggal ini silahkan ke menu reporting dan upload batch ulang.');
            return back();
        }
        
        $id_mhl = $this->generateUniqName();
        $inserted = DB::table('hr_masuk_hari_libur')->insert([
            'tanggal' => $request->tanggal,
            'id_mhl' => $id_mhl,
            'created_by' => Auth::user()->name,
            'created_at' => now(),
            'nik_pembuat' => Auth::user()->username,
            'nik_approver' => $request->nik_approver,
        ]);

        // Impor data dari Excel
        try {
            Excel::import(new UploadMasukHariLiburImport($request->tanggal, $id_mhl), $excel);
            $count = UploadMasukHariLiburModel::where('id_mhl', $id_mhl)->count();
            
            if ($count == 0) {
                DB::table('hr_masuk_hari_libur')->where('id_mhl', $id_mhl)->delete();
            }
        } catch (\Exception $e) {
            DB::table('hr_masuk_hari_libur')->where('id_mhl', $id_mhl)->delete();
            Session::flash('error', 'Kesalahan impor: ' . $e->getMessage());
            return back();
        }

        $email = DB::table('users')->where('username', $request->nik_approver)->first();

        if ($email && $email->email) {
            $data = ['created_by' => Auth::user()->name];
            $emailContent = 'Mengirim data pengajuan karyawan masuk hari libur';
            try {
                Mail::to($email->email)->send(new MasukHariLiburApprovalMail($emailContent, $data));
                Session::flash('info', 'Email notifikasi terkirim.');
            } catch (\Swift_TransportException $e) {
                Log::error('Email sending failed: ' . $e->getMessage());
                Session::flash('warning', 'Email notifikasi gagal dikirim.');
            }
        } else {
            Session::flash('info', 'Approver belum memiliki email. Data karyawan berhasil disimpan tanpa notifikasi email.');
        }
        Session::flash('success', 'Data karyawan berhasil disimpan.');
        return back();
    }

    public function ReportMasukHariLibur()
    {
        $where = [];
        if (isset($_GET['tanggal'])) {
            $where[] = ['tanggal', $_GET['tanggal']];
        }
        $permission = AuthGroup::find(Auth::user()->auth_group_id)
            ->permissions()
            ->where('codename', 'hr_mhl_reporting_hrd')
            ->first();
        if ($permission == null) {
            $where[] = ['nik_pembuat', Auth::user()->username];
        }

        $master = DB::table('hr_masuk_hari_libur')
            ->leftJoin('hr_masuk_hari_libur_approval', 'hr_masuk_hari_libur.created_by', '=', 'hr_masuk_hari_libur_approval.nama_admin')
            ->where($where)
            ->select('hr_masuk_hari_libur.*', 'hr_masuk_hari_libur_approval.nama_approval')
            ->get();

        // memastikan bahwa variabel $currentDate dengan benar berdasarkan tanggal yang dipilih dari $_GET
        $currentDate = isset($_GET['tanggal']) ? $_GET['tanggal'] : '';

        if (!empty($currentDate)) {
            $dateParts = explode('-', $currentDate);
            if (count($dateParts) !== 3 || !checkdate($dateParts[1], $dateParts[2], $dateParts[0])) {
                session()->flash('error', 'Maaf, silakan pilih tanggal yang valid dengan format YYYY-MM-DD.');
                return redirect()->back();
            }

            $currentDate = Carbon::createFromFormat('Y-m-d', $currentDate)->format('Y-m-d');
        }

        $summary = DB::table('hr_masuk_hari_libur_karyawan')
            ->select('status_karyawan', 'shift', DB::raw('COUNT(*) AS jumlah_karyawan'))
            ->whereDate('tanggal', $currentDate)
            ->groupBy('status_karyawan', 'shift')
            ->get();

        $master = $master->map(function ($item) {
            $karyawan = DB::table('hr_masuk_hari_libur_karyawan')
                ->where('id_mhl', $item->id_mhl)
                ->get();
            $item->jumlah_karyawan = $karyawan->count();
            $item->jumlah_scan = $karyawan->where('is_scan', 'Y')->count();
            $item->tidak_scan = $karyawan->where('is_scan', 'N')->count();

            return $item;
        });

        return view('hr.masukharilibur.reporting.index', compact('summary', 'master'));
    }



    public function Approver()
    {
        $where = [];
        if (isset($_GET['tanggal'])) {
            $where[] = ['tanggal', $_GET['tanggal']];
        }
        $where[] = ['status', 1];
        $master = DB::table('hr_masuk_hari_libur')
            ->where($where)
            ->where('nik_approver', Auth::user()->username)
            ->get();

        $master = $master->map(function ($item) {
            $karyawan = DB::table('hr_masuk_hari_libur_karyawan')
                ->where('id_mhl', $item->id_mhl)
                ->get();
            $item->jumlah_karyawan = $karyawan->count();
            $item->jumlah_scan = $karyawan->where('is_scan', 'Y')->count();
            $item->tidak_scan = $karyawan->where('is_scan', 'N')->count();

            // Ambil informasi created_by dari hr_masuk_hari_libur
            $item->created_by = $item->created_by;

            return $item;
        });

        return view('hr.masukharilibur.approver.index', compact('master'));
    }

    public function reportingDetail($id_mhl)
    {
        $data = DB::table('hr_masuk_hari_libur')->where('id_mhl', $id_mhl)->first();
        $scan = DB::table('hr_masuk_hari_libur_karyawan')->where('id_mhl', $id_mhl)->get();

        return view('hr.masukharilibur.reporting.detail', compact('data', 'scan'));
    }

    public function ApproverDetail($id_mhl)
    {
        $data = DB::table('hr_masuk_hari_libur')->where('id_mhl', $id_mhl)->first();
        $scan = DB::table('hr_masuk_hari_libur_karyawan')->where('id_mhl', $id_mhl)->get();

        return view('hr.masukharilibur.approver.detail', compact('data', 'scan'));
    }

    public function approve($id_mhl)
    {
        $mhl = DB::table('hr_masuk_hari_libur')
            ->where('id_mhl', $id_mhl)->first();
        $mhl_tambahan_karyawan = DB::table('hr_masuk_hari_libur_karyawan')->where('id_mhl', $id_mhl)->get();

        // Validasi nik tidak kosong
        if ($mhl_tambahan_karyawan->isEmpty() || empty($mhl_tambahan_karyawan->first()->nik)) {
            // Tangani ketika nik kosong menggunakan flash message
            Session::flash('error', 'NIK tidak boleh kosong!');
            return redirect()->back();
        }

        // Validasi approve row berikutnya dengan nik dan tanggal yang sama
        $existingApprovals = DB::table('hr_masuk_hari_libur_karyawan')
            ->where('nik', $mhl_tambahan_karyawan->first()->nik)
            ->where('tanggal', $mhl->tanggal)
            ->where('status', 0) // Hanya periksa yang sudah diapprove
            ->count();

        if ($existingApprovals > 0) {
            // Row berikutnya dengan nik dan tanggal yang sama sudah diapprove
            Session::flash('error', 'Data dengan NIK dan Tanggal yang sama sudah diapprove sebelumnya!');
            return redirect()->back();
        }

        DB::table('hr_masuk_hari_libur')->where('id_mhl', $id_mhl)->update([
            'status' => 0
        ]);
        DB::table('hr_masuk_hari_libur_karyawan')->where('id_mhl', $id_mhl)->update([
            'status' => 0
        ]);
        $mhl_all_karyawan = DB::table('hr_masuk_hari_libur_karyawan')
            ->where('tanggal', $mhl->tanggal)->get();

        $data_all_shift1 = $mhl_all_karyawan->where('shift', '1')->where('status', '0')->count();
        $data_all_shift2 = $mhl_all_karyawan->where('shift', '2')->where('status', '0')->count();
        $data_all_shift3 = $mhl_all_karyawan->where('shift', '3')->where('status', '0')->count();
        $data_tambahan_shift1 = $mhl_tambahan_karyawan->where('shift', '1')->count();
        $data_tambahan_shift2 = $mhl_tambahan_karyawan->where('shift', '2')->count();
        $data_tambahan_shift3 = $mhl_tambahan_karyawan->where('shift', '3')->count();
        $data_count = [
            'data_all_shift_1' => $data_all_shift1,
            'data_all_shift_2' => $data_all_shift2,
            'data_all_shift_3' => $data_all_shift3,
            'data_tambahan_shift_1' => $data_tambahan_shift1,
            'data_tambahan_shift_2' => $data_tambahan_shift2,
            'data_tambahan_shift_3' => $data_tambahan_shift3
        ];

        $email = DB::table('hr_masuk_hari_libur_pic_ga')
            ->where('is_active', 'Y')
            ->get()
            ->pluck('email')
            ->toArray();

        \Mail::to($email)->send(new \App\Mail\MasukHariLiburMail($data_count, $mhl->tanggal));

        Session::flash('info', 'Data Berhasil Diapprove!');
        return redirect()->back();
    }

    public function Reject($id_mhl)
    {
        $data = DB::table('hr_masuk_hari_libur')->where('id_mhl', $id_mhl)->update([
            'status' => 2
        ]);

        Session::flash('info', 'Data Berhasil Direject !');
        return back();
    }

    public function Edit($id)
    {
        $detail =  DB::table('hr_masuk_hari_libur_karyawan')->where('id', $id)->first();
        return view('hr.masukharilibur.reporting.edit', compact('detail'));
    }

    public function Update(Request $request)
    {
        $nik = $request->nik;
        $nama = $request->nama;
        $department = $request->department;
        $shift = $request->shift;
        $status_karyawan = $request->status_karyawan;
        // $alasan_update = $request->alasan_update;

        // $data_sekarang = DB::table('hr_masuk_hari_libur_karyawan')->where('tanggal', $tanggal)->first();

        DB::table('hr_masuk_hari_libur_karyawan')->where('id', $request->id)->update([
            'nik' => $nik,
            'nama' => $nama,
            'department' => $department,
            'Shift' => $shift,
            'status_karyawan' => $status_karyawan,
        ]);

        Session::flash('info', 'Data Edit Anda Berhasil Disimpan');
        return back();
    }

    // yang sedang dikerjakan

    // get data master
    // update pencarian master approval
    public function masterApproval(Request $request)
    {
        $query = DB::table('hr_masuk_hari_libur_approval');
        $katakunci = $request->input('katakunci');

        if ($katakunci) {
            $query->where(function ($query) use ($katakunci) {
                $query->where('nik_admin', 'LIKE', "%$katakunci%")
                    ->orWhere('nama_admin', 'LIKE', "%$katakunci%")
                    ->orWhere('nik_approval', 'LIKE', "%$katakunci%")
                    ->orWhere('nama_approval', 'LIKE', "%$katakunci%")
                    ->orWhere('status', 'LIKE', "%$katakunci%");
            });
        }

        $datas = $query->paginate(6);

        return view('hr.masukharilibur.master_approval.index', compact('datas'));
    }


    // tampilkan view page tambah data master
    public function tambahDataMaster()
    {
        return view('hr.masukharilibur.master_approval.add');
    }

    // add data master
    public function storeDataMaster(Request $request)
    {
        $data = $request->validate([
            // 'dept' => 'required',
            'nama_admin' => 'required',
            'nik_admin' => 'required',
            'nama_approval' => 'required',
            'nik_approval' => 'required',
            'status' => 'required',
        ]);

        // $deptId = $request->input('dept');
        $namaAdmin = $request->input('nama_admin');
        $nikAdmin = $request->input('nik_admin');
        $namaApproval = $request->input('nama_approval');
        $nikApproval = $request->input('nik_approval');

        // $data['dept'] = $deptId;
        $data['nama_admin'] = $namaAdmin;
        $data['nik_admin'] = $nikAdmin;
        $data['nama_approval'] = $namaApproval;
        $data['nik_approval'] = $nikApproval;

        $inserted = DB::table('hr_masuk_hari_libur_approval')->insert($data);

        if ($inserted) {
            Session::flash('success', 'Data Master Approval berhasil dibuat!');
        } else {
            Session::flash('error', 'Gagal membuat Data Master Approval. Silakan coba lagi.');
        }

        return redirect()->back();
    }


    // delete data master
    public function deleteData($id)
    {
        DB::table('hr_masuk_hari_libur_approval')->where('id', $id)->delete();

        return redirect('/masukharilibur/master_approval')->with('success', 'Data berhasil dihapus.');
    }

    // cek id data master
    public function editData($id)
    {
        $data = DB::table('hr_masuk_hari_libur_approval')->where('id', $id)->first();
        return view('hr.masukharilibur.master_approval.edit', compact('data'));
    }

    // edit id data master
    public function updateData(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric', // Assuming id is available in the form data
            'nik_admin' => 'required|numeric',
            'nama_admin' => 'required',
            'nik_approval' => 'required|numeric',
            'nama_approval' => 'required',
            'status' => 'required|in:aktif,tidak aktif',
        ]);

        $id = $request->input('id');

        // Update the data in the database
        DB::table('hr_masuk_hari_libur_approval')
            ->where('id', $id)
            ->update([
                'nik_admin' => $request->input('nik_admin'),
                'nama_admin' => $request->input('nama_admin'),
                'nik_approval' => $request->input('nik_approval'),
                'nama_approval' => $request->input('nama_approval'),
                'status' => $request->input('status'),
            ]);

        return redirect('/masukharilibur/master_approval')->with('success', 'Data berhasil diperbarui.');
    }

    // import batch ulang
    // point final deadline
    public function import_batch(Request $request)
    {
        $id_mhl = $request->id_mhl;

        try {
            // Hapus data lama di tabel hr_masuk_hari_libur_karyawan berdasarkan id_mhl
            DB::table('hr_masuk_hari_libur_karyawan')
                ->where('id_mhl', $id_mhl)
                ->delete();

            // Impor data baru ke tabel hr_masuk_hari_libur_karyawan menggunakan id_mhl yang diberikan
            Excel::import(new UploadMasukHariLiburImport($request->tanggal, $id_mhl), $request->file('excel'));

            DB::table('hr_masuk_hari_libur')
                ->where('id_mhl', $id_mhl)
                ->update([
                    'status' => 1
                ]);


            Session::flash('info', 'Data Anda Berhasil Disimpan!');
        } catch (\Exception $e) {
            // dd($e->getMessage());
            Session::flash('error', 'Terjadi kesalahan saat mengimpor data. Pastikan format file Excel sesuai dengan yang diharapkan.');
        }

        return back();
    }


    public function departemenApproval()
    {
        $departments = Department::where('status', '1')->get();
        $users = User::all();

        return view('hr.masukharilibur.master_approval.add', compact('departments', 'users'));
    }

    // master approval new fiture update password
    public function  userEditPassword()
    {
        $users = User::all();
        return view('hr.masukharilibur.master_approval.reset', compact('users'));
    }

    public function userUpdatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'new_password' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', 'Silahkan isi password terlebih dahulu.')->withInput();
        }

        $user = User::find($request->user_id);

        if (!$user) {
            return redirect()->back()->with('error', 'User tidak ditemukan.');
        }

        $user->password = bcrypt($request->new_password);
        $user->save();

        return redirect()->back()->with('success', 'Password berhasil diupdate.');
    }


    // end get master approval

    // protect excel file

    // public function count_summary(Request $request)
    // {
    //     $summary = DB::table('hr_masuk_hari_libur_karyawan')
    //         ->select('status_karyawan', 'shift', DB::raw('COUNT(*) AS jumlah_karyawan'))
    //         ->groupBy('status_karyawan', 'shift')
    //         ->get();

    //     return view('hr.masukharilibur.reporting.index', compact('summary'));
    // }

    // proses pengerjaan bareng dengan pdf dan excel
    // tampilkan data admin dan approv
}
