<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\EDoc\EDocMail;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Image;
use Yajra\DataTables\Datatables;

class EDocLogController extends Controller
{
    public function index()
    {
        $dept = DB::table('departments')->orderBy('name', 'ASC')->get();
        return view('edoc.index', compact('dept'));
    }

    public function masterpic()
    {
        $pic = DB::table('edoc_pic')->orderBy('nama', 'ASC')->get();
        $dept = DB::table('departments')->orderBy('name', 'ASC')->get();
        $user = DB::table('users')
            ->select(
                'users.username',
                'users.name',
                'dept.name as dept',
            )
            ->join('departments as dept', 'users.dept_id', '=', 'dept.id')
            ->orderBy('users.name', 'ASC')->get();
        return view('edoc.masterpic', compact('pic', 'dept', 'user'));
    }

    public function post_kedatangan(Request $request)
    {
        // Lindungi dari dua kali submit
        $validasi = DB::table('edoc_kedatangan')
            ->where('nama_penerima', $request->nama_penerima)
            ->where('tanggal_kedatangan', $request->tanggal_kedatangan)
            ->where('nama_pt_pengirim', $request->nama_pt_pengirim)
            ->where('jenis', $request->jenis)
            ->where('created_at', date('Y-m-d H:i:s'))
            ->first();

        if ($validasi != null) {
            Session::flash('info', 'Data Berhasil Di Simpan..');
            return back();
        }

        $foto_kartu_identitas_kurir = $request->foto_kartu_identitas_kurir;
        $foto_kartu_identitas_kurir = str_replace('data:image/jpeg;base64,', '', $foto_kartu_identitas_kurir);
        $foto_kartu_identitas_kurir = str_replace(' ', '+', $foto_kartu_identitas_kurir);
        $foto_kartu_identitas_kurir = base64_decode($foto_kartu_identitas_kurir);
        $foto_kartu_identitas_kurir_name = 'foto_kartu_identitas_kurir-' . uniqid() . '.jpeg';
        Image::make($foto_kartu_identitas_kurir)->resize(500, 500, function ($constraint) {
            $constraint->aspectRatio();
        })->save('e-doc/kedatangan/foto_kartu_identitas_kurir/' . $foto_kartu_identitas_kurir_name);

        //insert
        DB::table('edoc_kedatangan')->insert([
            'dept_penerima' => $request->dept_penerima,
            'id_barang' => $request->dept_penerima  . '-' . $request->jenis . '-' . uniqid(),
            'nama_penerima' => $request->nama_penerima,
            'nama_pt_pengirim' => $request->nama_pt_pengirim,
            'jenis' => $request->jenis,
            'keterangan' => $request->keterangan,
            'tanggal_kedatangan' => $request->tanggal_kedatangan,
            'keterangan' => $request->keterangan,
            'foto_kartu_identitas_kurir' => $foto_kartu_identitas_kurir_name,
            'nama_kurir' => $request->nama_kurir,
            'no_identitas_kurir' => $request->no_identitas_kurir,
            'no_hp_kurir' => $request->no_hp_kurir,
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => Auth::user()->name,
        ]);

        // data for body email
        // $detail = [
        //     'petugas' =>  Auth::user()->name,
        //     'tanggal_kedatangan' => $request->tanggal_kedatangan,
        //     'nama_pt_pengirim' => $request->nama_pt_pengirim,
        //     'nama_penerima' => $request->nama_penerima,
        //     'jenis' => $request->jenis,
        //     'keterangan' => $request->keterangan,
        // ];
        // $pic = DB::table('edoc_pic')->where('dept', $request->dept_penerima)->get();

        // $to = '';
        // $cc = [];


        // foreach ($pic as $key => $list) {
        //     $user =  DB::table('users')->where('username', $list->nik)->first();
        //     // jika users dari edoc_pic belum terdaftar di users beri null dan lanjutkan looping
        //     if ($user == null) {
        //         continue;
        //     }

        //     if ($user->email != null) {
        //         if ($to == '') {
        //             $to = $user->email;
        //         } else {
        //             $cc[] = $user->email;
        //         }
        //     }
        // }

        // if ($to != '') {
        //     Mail::mailer(setEmail($to))
        //         ->to($to)
        //         ->cc($cc)
        //         ->send(new EDocMail(
        //             'Yth. ' . $request->nama_penerima . ', MyBAS mencatat adanya dokumen masuk (E-Document) yang ditujukan kepada Anda. Berikut detail informasinya:',
        //             'E-DOCUMENT NOTIFICATION',
        //             $detail
        //         ));
        // }

        Session::flash('info', 'Berhasil menyimpan kedatangan');
        return back();
    }

    public function post_pic(Request $request)
    {
        if ($request->has('pic')) {
            for ($i = 0; $i < count($request->pic); $i++) {
                $validasi = DB::table('edoc_pic')->where('nik', $request->pic[$i])->first();
                $users = DB::table('users')->where('username', $request->pic[$i])->first();
                $dept  = DB::table('departments')->where('id', $users->dept_id)->first();
                if ($validasi != null) {
                    Session::flash('error', 'PIC DOUBLE', 'PIC a/n ' . $users->name . ' Sudah Ada Di sistem..');
                    return back();
                }
                if (!$validasi) {
                    DB::table('edoc_pic')->insert([
                        'nama' => $users->name,
                        'nik' => $users->username,
                        'dept' => $dept->name,
                    ]);
                    Session::flash('info', 'PIC Berhasil Di Simpan..');
                    return back();
                }
            }
        } else {
            Session::flash('error', 'Tidak Ada Data Yang Di Simpan...');
            return back();
        }
    }

    public function deletepic($id)
    {
        DB::table('edoc_pic')->where('id', $id)->delete();
        Session::flash('info', 'PIC Berhasil Di Hapus..');
        return back();
    }

    public function ScanPengambilan($rfid)
    {
        $rfid = (int)$rfid;
        $user = DB::connection('192.168.178.44-admin')
            ->table('MSIDCARD')
            ->select('NIK')
            ->where(['CARDNODEVICE' => $rfid])
            ->value('NIK');

        // $user  = $nik;

        $pic         = DB::table('edoc_pic')->where('nik', $user)->first();
        $list_barang = DB::table('edoc_kedatangan')->select('jenis')->where('dept_penerima', $pic->dept)->get();
        $groupBy     = $list_barang->groupBy('jenis');
        return response()->json([
            'data' => [
                'pic' => $pic,
                'groupBy' => $groupBy,
            ],
            'status' => 'success'
        ]);
    }

    public function GetListBarang($dept, $jenis)
    {
        $data = DB::table('edoc_kedatangan')->where('status', 1)->where('jenis', $jenis)->where('dept_penerima', $dept)->get();

        return response()->json([
            'data' => $data,
            'status' => 'success'
        ]);
    }

    public function post_pengambilan(Request $request)
    {
        // dd($request->all());
        $foto = $request->foto;
        $foto = str_replace('data:image/jpeg;base64,', '', $foto);
        $foto = str_replace(' ', '+', $foto);
        $foto = base64_decode($foto);
        $foto_name = 'pengambilan-' . uniqid() . '.jpeg';

        $banyakIdBarang = explode(',', $request->id_barang);
        if (count($banyakIdBarang) > 0) {
            foreach ($banyakIdBarang as $idBarang) {
                Image::make($foto)->resize(500, 500, function ($constraint) {
                    $constraint->aspectRatio();
                })->save('e-doc/pengambilan/' . $foto_name);

                $data = DB::table('edoc_kedatangan')->where('id_barang', $idBarang)->value('jenis');
                $pic  = DB::table('users')->where('username', $request->nik)->first();

                DB::table('edoc_kedatangan')->where('id_barang', $idBarang)->update([
                    'status'      => 0,
                    'updated_at'  => date('Y-m-d H:i:s'),
                    'updated_by'  => $pic->name,
                    'updated_nik' => $pic->username,
                    'foto'        => $foto_name,
                ]);
            }
        } else {
            Image::make($foto)->resize(500, 500, function ($constraint) {
                $constraint->aspectRatio();
            })->save('e-doc/pengambilan/' . $foto_name);

            $data = DB::table('edoc_kedatangan')->where('id_barang', $request->id_barang)->value('jenis');
            $pic  = DB::table('users')->where('username', $request->nik)->first();

            DB::table('edoc_kedatangan')->where('id_barang', $request->id_barang)->update([
                'status'      => 0,
                'updated_at'  => date('Y-m-d H:i:s'),
                'updated_by'  => $pic->name,
                'updated_nik' => $pic->username,
                'foto'        => $foto_name,
            ]);
        }

        return response()->json([
            'data' => $data,
            'status' => 'success'
        ]);
    }

    public function pengiriman()
    {
        $dept = DB::table('users')->join('departments', 'users.dept_id', '=', 'departments.id')->where('departments.id', Auth::user()->dept_id)->value('departments.name');

        return view('edoc.pengiriman', compact('dept'));
    }

    public function post_pengiriman(Request $request)
    {
        //insert to table edoc_pengiriman
        $data = [
            'id_barang' => $request->dept_pengirim  . '-' . $request->jenis . '-' . uniqid(),
            'dept_pengirim'      => $request->dept_pengirim,
            'nama_penerima'      => $request->nama_penerima,
            'nama_pt_penerima'   => $request->nama_pt_penerima,
            'tanggal_pengiriman' => $request->tanggal_pengiriman,
            'keterangan'         => $request->keterangan,
            'jenis'              => $request->jenis,
            'kurir'              => $request->kurir == 'Lainnya' ? $request->kurir_lain : $request->kurir,
            'created_at'         => date('Y-m-d H:i:s'),
            'created_by'         => Auth::user()->username,
        ];
        DB::table('edoc_pengiriman')->insert($data);

        // dd($data);

        Session::Flash('info', 'Data Berhasil Di Simpan, Silahkan Konfirmasi Pengiriman Di Pos Security');
        return back();
    }

    public function ScanPengiriman($rfid)
    {
        $rfid = (int)$rfid;
        $user = DB::connection('192.168.178.44-admin')
            ->table('MSIDCARD')
            ->select('NIK')
            ->where(['CARDNODEVICE' => $rfid])
            ->value('NIK');

        $nik  = $user;

        return response()->json([
            'data' => [
                'nik' => $nik,
            ],
            'status' => 'success'
        ]);
    }

    public function ShowListPengiriman($nik)
    {
        $pic = DB::table('edoc_pic')->where('nik', $nik)->first();
        $data =  DB::table('edoc_pengiriman')
            ->where('dept_pengirim', $pic->dept)
            ->where('status', 1)->get();
        return Datatables::of($data)->addIndexColumn()->make(true);
    }

    public function KonfirmasiPengiriman(Request $request)
    {
        if ($request->has('id_barang')) {
            $foto = $request->foto;
            $foto = str_replace('data:image/jpeg;base64,', '', $foto);
            $foto = str_replace(' ', '+', $foto);
            $foto = base64_decode($foto);
            $foto_name = 'Konfirmasi-Pengiriman-' . uniqid() . '.jpeg';
            Image::make($foto)->resize(500, 500, function ($constraint) {
                $constraint->aspectRatio();
            })->save('e-doc/konfirmasi-pengiriman/' . $foto_name);

            $id_barang = explode(',', $request->id_barang);

            foreach ($id_barang as $id) {
                DB::table('edoc_pengiriman')->where('id_barang', $id)->update([
                    'status' => 2,
                    'confirm_at' => date('Y-m-d H:i:s'),
                    'confirm_by' => $request->nik,
                    'foto' => $foto_name,
                ]);
            }

            Session::flash('info', 'Barang Berhasil Di Konfirmasi..');
            return back();
        } else {
            Session::flash('error', 'Barang Belum Di Pilih');
            return back();
        }
    }

    public function ListSerahTerimaKurir()
    {
        $data = DB::table('edoc_pengiriman')->where('status', 2)->get();
        $nama = [];
        foreach ($data as $d) {
            $nama[] = DB::table('users')->select('name')->where('username', $d->created_by)->value('name');
        }
        return response()->json([
            'data' => [
                'data' => $data,
                'nama' => $nama,
            ],
            'status' => 'success'
        ]);
    }

    public function PostSerahTerima(Request $request)
    {
        $foto = $request->foto;
        $foto = str_replace('data:image/jpeg;base64,', '', $foto);
        $foto = str_replace(' ', '+', $foto);
        $foto = base64_decode($foto);
        $foto_name = 'Serah-Terima-' . uniqid() . '.jpeg';
        Image::make($foto)->resize(500, 500, function ($constraint) {
            $constraint->aspectRatio();
        })->save('e-doc/serah-terima/' . $foto_name);

        $id_barang = explode(',', $request->id_barang);

        foreach ($id_barang as $id) {
            DB::table('edoc_pengiriman')->where('id_barang', $id)->update([
                'status' => 0,
                'transfer_at' => date('Y-m-d H:i:s'),
                'transfer_by' => Auth::user()->username,
                'foto_serah_terima' => $foto_name,
            ]);
        }

        Session::flash('info', 'Data Berhasil Di Update..');
        return back();
    }

    public function history()
    {
        $dept = auth()->user()->department->name;

        // Periksa apakah pengguna memiliki 'auth_group_id' sama dengan '63'
        $permissions = auth()->user()->group->permissions;

        $permissions = $permissions->pluck('codename')->toArray();

        if (in_array('security', $permissions)) {
            // cek data security pada variable permissions
            $pengiriman = DB::table('edoc_pengiriman')->get();
        } else {
            // Jika tidak, ambil catatan 'edoc_pengiriman' khusus untuk departemen pengguna
            $pengiriman = DB::table('edoc_pengiriman')
                ->where('created_by', Auth::user()->username)
                ->get();
        }

        $list_dept = DB::table('departments')->orderBy('name', 'ASC')->get();

        return view('edoc.history', compact('pengiriman', 'dept', 'list_dept'));
    }

    public function historyDataKedatangan()
    {
        $status = [1, 0];

        if (isset($_GET['filter']) && $_GET['filter'] != 'all') {
            $status = [$_GET['filter']];
        }

        $dept = auth()->user()->department->name;

        $kedatangan = DB::table('edoc_kedatangan')
            ->whereIn('status', $status);

        // cari data pada auth group yang berisikan codename
        $permissions = auth()->user()->group->permissions;

        $permissions = $permissions->pluck('codename')->toArray();

        // Jika pengguna memiliki 'auth_group' sama dengan 'security', sertakan semua departemen dalam hasil kueri
        if (in_array('security', $permissions)) {
            $kedatangan = $kedatangan->where('dept_penerima', '!=', '');
        } else {
            $kedatangan = $kedatangan->where('dept_penerima', $dept);
        }

        // Return datatable server side
        return Datatables::of($kedatangan)
            ->addIndexColumn()
            ->make(true);
    }

    public function detailKedatangan($id)
    {
        $data = DB::table('edoc_kedatangan')->where('id', $id)->first();
        $user = DB::table('users')->where('name', $data->created_by)->first();
        return response()->json([
            'status' => 'ok',
            'data' => [
                'data' => $data,
                'user' => $user,
            ],
        ]);
    }

    public function detailPengiriman($id)
    {
        $data = DB::table('edoc_pengiriman')->where('id', $id)->first();
        $user = DB::table('users')->where('username', $data->created_by)->first();
        $konfirm_by = DB::table('users')->where('username', $data->confirm_by)->first();
        $petugas = DB::table('users')->where('username', $data->transfer_by)->first();
        return response()->json([
            'status' => 'ok',
            'data' => [
                'data' => $data,
                'konfirm_by' => $konfirm_by,
                'petugas' => $petugas,
                'user' => $user,
            ],
        ]);
    }

    public function postChangeDeprtPenerima(Request $request)
    {
        DB::table('edoc_kedatangan')->where('id_barang', $request->id_barang)->update([
            'dept_penerima' => $request->dept_penerima_baru,
        ]);

        $data = DB::table('edoc_kedatangan')->where('id_barang', $request->id_barang)->first();

        $detail = [
            'petugas' =>  Auth::user()->name,
            'tanggal_kedatangan' => $data->tanggal_kedatangan,
            'nama_pt_pengirim' => $data->nama_pt_pengirim,
            'nama_penerima' => $data->nama_penerima,
            'jenis' => $data->jenis,
            'keterangan' => $data->keterangan,
        ];

        $pic = DB::table('edoc_pic')->where('dept', $data->dept_penerima)->get();

        $to = '';
        $cc = [];

        foreach ($pic as $key => $list) {
            $user =  DB::table('users')->where('username', $list->nik)->first();
            if ($user->email != null) {
                if ($key == 0) {
                    $to = $user->email;
                } else {
                    $cc[] = $user->email;
                }
            }
        }

        if ($to != '') {
            Mail::mailer(setEmail($to))
                ->to($to)
                ->cc($cc)
                ->send(new EDocMail(
                    'Yth. ' . $request->nama_penerima . ', MyBAS mencatat adanya dokumen masuk (E-Document) yang ditujukan kepada Anda. Berikut detail informasinya:',
                    'E-DOCUMENT NOTIFICATION',
                    $detail
                ));
        }

        Session::flash('info', 'Data Berhasil Di Simpan..');
        return back();
    }

    public function postReturnBarang(Request $request)
    {
        DB::table('edoc_kedatangan')->where('id_barang', $request->id_barang)->update([
            'dept_penerima' => $request->dept_return,
            'status' => 1
        ]);

        $data = DB::table('edoc_kedatangan')->where('id_barang', $request->id_barang)->first();

        $detail = [
            'petugas' =>  Auth::user()->name,
            'tanggal_kedatangan' => $data->tanggal_kedatangan,
            'nama_pt_pengirim' => $data->nama_pt_pengirim,
            'nama_penerima' => $data->nama_penerima,
            'jenis' => $data->jenis,
            'keterangan' => $data->keterangan,
        ];

        $pic = DB::table('edoc_pic')->where('dept', $data->dept_penerima)->get();

        $to = '';
        $cc = [];

        foreach ($pic as $key => $list) {
            $user =  DB::table('users')->where('username', $list->nik)->first();
            if ($user->email != null) {
                if ($key == 0) {
                    $to = $user->email;
                } else {
                    $cc[] = $user->email;
                }
            }
        }

        if ($to != '') {
            Mail::mailer(setEmail($to))
                ->to($to)
                ->cc($cc)
                ->send(new EDocMail(
                    'Yth. ' . $request->nama_penerima . ', MyBAS mencatat adanya dokumen masuk (E-Document) yang ditujukan kepada Anda. Berikut detail informasinya:',
                    'E-DOCUMENT NOTIFICATION',
                    $detail
                ));
        }

        Session::flash('info', 'Data Berhasil Di Simpan..');
        return back();
    }
}
