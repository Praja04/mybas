<?php

namespace App\Http\Controllers\InternalMemo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use DB;
use App\User;
use App\Models\InternalMemoModel;
use Alert;
use Session;
use Path\To\DOMDocument;
use Image;
use PDF;

class InternalMemoController extends Controller
{
    public function index()
    {
        return view('internal_memo.index');
    }
    
    public function get_email()
    {
        $dept = Auth::user()->dept_id;

        $dept_me = DB::table('departments')->where('id', $dept)->first();
        
        return view('internal_memo.add_email', compact('dept_me'));
    }
    
    public function post_email(Request $request, $id)
    {
        $data = DB::table('users')->where('id', $id)->update([
            'email' => $request->email
        ]);

        Session::flash('success', 'E-mail Berhasil Dibuat');
        return redirect('/internal_memo/menu/index');
    }

    public function buat_dokumen()
    {
        $nik = Auth::user()->username;
        
        $dept = DB::table('departments')
        ->orderBy('name', 'ASC')
        ->get();

        $user = DB::table('users')
        ->orderBy('name', 'ASC')
        ->groupBy('name')
        ->get();

          function getRomawi($bln){
        	switch ($bln){
                    case 1: 
                        return "I";
                        break;
                    case 2:
                        return "II";
                        break;
                    case 3:
                        return "III";
                        break;
                    case 4:
                        return "IV";
                        break;
                    case 5:
                        return "V";
                        break;
                    case 6:
                        return "VI";
                        break;
                    case 7:
                        return "VII";
                        break;
                    case 8:
                        return "VIII";
                        break;
                    case 9:
                        return "IX";
                        break;
                    case 10:
                        return "X";
                        break;
                    case 11:
                        return "XI";
                        break;
                    case 12:
                        return "XII";
                        break;
              }
       }
        $bulan	= date('n');
        $romawi = getRomawi($bulan);
        $tahun = date('Y');

        $departemen_me = DB::table('departments')->where('id', Auth::user()->dept_id)->first();

        $bulan_terakhir = DB::table('internal_memo')
        ->orderBy('tgl_pengisian', 'DESC')
        ->whereMonth('tgl_pengisian', '=', date('m'))
        ->where('dept_pembuat', $departemen_me->id)
        ->first();
        
        $hitung_data = DB::table('internal_memo')
        ->orderBy('tgl_pengisian', 'DESC')
        ->whereMonth('tgl_pengisian', '=', date('m'))
        ->where('dept_pembuat', $departemen_me->id)
        ->get();
        

        $cek_dept = DB::table('internal_memo')
        ->where('dept_pembuat', $departemen_me->id)
        ->whereMonth('tgl_pengisian', '=', date('m'))
        ->first();

        if($cek_dept)
        {
            if($bulan_terakhir)
            {
                if(count($hitung_data) < 10)
                {
                    $angka_dinamis = count($hitung_data) + 1;
                    $no_dokumen = "00$angka_dinamis/009/$departemen_me->name/IM/$romawi/$tahun";
                }
                else if(count($hitung_data) > 10)
                {
                    $angka_dinamis = count($hitung_data) + 1;
                    $no_dokumen = "0$angka_dinamis/009/$departemen_me->name/IM/$romawi/$tahun";
                }
                else
                {
                    $angka_dinamis = count($hitung_data) + 1;
                    $no_dokumen = "$angka_dinamis/009/$departemen_me->name/IM/$romawi/$tahun";
                }
            }
            else
            {
                $no_dokumen = "001/009/$departemen_me->name/IM/$romawi/$tahun";
            }
        }
        else
        {
                $no_dokumen = "001/009/$departemen_me->name/IM/$romawi/$tahun";
        }
        $nik = Auth::user()->username;
        
        $karakter_nik = strlen($nik);
        if($karakter_nik < 6)
        {
            $cek_approver   = DB::table('internal_memo_approver_utama')->where('nik', 'like', '%'.$nik.'%')->first();

                $approver_utama = DB::table('internal_memo_approver_utama')
                ->where('pos_id', $cek_approver->report_to)
                ->first();

            $nama_approver = DB::connection('192.168.154.44')
            ->table('MSIDCARD')
            ->select('NIK', 'EMPNM', 'DEPTID')
            ->where(['nik' => $approver_utama->nik])
            ->first();
        }
        else{
            $cek_approver = DB::table('internal_memo_approver_utama')
                            ->where('nik', $nik)
                            ->first();
            
            $approver_utama = DB::table('internal_memo_approver_utama')
            ->where('internal_memo_approver_utama.pos_id', $cek_approver->report_to)
            ->first();
            
            $nama_approver = DB::connection('192.168.154.44')
            ->table('MSIDCARD')
            ->select('NIK', 'EMPNM', 'DEPTID')
            ->where(['nik' => $approver_utama->nik])
            ->first();

        }
        
        return view('internal_memo.buat_dokumen', compact('user', 'dept', 'user', 'no_dokumen', 'approver_utama', 'nama_approver'));
    }

    public function get_approver($nik, $jumlah)
    {
        $nik = Auth::user()->username;
        
        $karakter_nik = strlen($nik);
        if($karakter_nik < 6)
        {
            $cek_approver   = DB::table('internal_memo_approver_utama')->where('nik', 'like', '%'.$nik.'%')->first();

            $appprover = [];
            for($i=0; $i < $jumlah; $i++)
            {
                $approver = DB::table('internal_memo_approver_utama')
                ->where('pos_id', $cek_approver->report_to)
                ->get();
            }

            $nama_approver = DB::connection('192.168.154.44')
            ->table('MSIDCARD')
            ->select('NIK', 'EMPNM', 'DEPTID')
            ->where(['nik' => $approver_utama->nik])
            ->get();
        }
        else{
            $cek_approver = DB::table('internal_memo_approver_utama')
                            ->where('nik', $nik)
                            ->first();
            
            $approver_utama = DB::table('internal_memo_approver_utama')
            ->where('internal_memo_approver_utama.pos_id', $cek_approver->report_to)
            ->first();
            
            $nama_approver = DB::connection('192.168.154.44')
            ->table('MSIDCARD')
            ->select('NIK', 'EMPNM', 'DEPTID')
            ->where(['nik' => $approver_utama->nik])
            ->first();

        }
    }

    public function post_dokumen(Request $request)
    {
        $konten = $request->konten;        
        $no_dokumen = $request->no_dokumen;
        $nik_pembuat =  $request->nik_pembuat;
        $nik_approver =  $request->nik_approver;
        $nik_mengetahui =  $request->nik_mengetahui;
        $nik_penerima =  $request->nik_penerima;
        $dept_pembuat =  $request->dept_pembuat;

        $storage = "public/dokumen_im";
        $dept = $request->dept;

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHtml($konten, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);    
        $images = $dom->getElementsByTagName('img');
        
            foreach($images as $img)
            {
                $data = $img->getAttribute('src');
                list($type, $data) = explode(';', $data);
                list($type, $data) = explode(',', $data);
                $data = base64_decode($data);
                $image_name= "dok" . "-". date('Y-m-d')."-".$dept. "-".'.jpg';
                $storage = "dokumen_im/";
                $path = public_path($storage) . $image_name;
                file_put_contents($path, $data);
                $img->removeAttribute('src');
                $img->setAttribute('src', $image_name);
            }
                
                if($request->nik_pembuat != NULL )
                {
                for($i = 0; $i < count($nik_pembuat); $i++)
                {
                DB::table('internal_memo')->insert([
                    'tgl_pengisian' => date('Y-m-d'),
                        'no_dokumen' => $request->no_dokumen,
                        'nik_pembuat' => $nik_pembuat[$i],
                        'dept_pembuat' => $dept_pembuat[$i],
                        'perihal' => $request->perihal,
                        'rev' => '00',
                        'jam_pengisian' => date('H:i'),
                        'tgl_edit' => date('Y-m-d'),
                        'jam_edit' => date('H:i'),
                        'konten' => $description = $dom->saveHTML(),
                        'nama_pengisi' => Auth::user()->name,
                        'nik_pengisi' => Auth::user()->username,
                        ]);
                }
            
            if($nik_approver != NULL )
            {
                for($i = 0; $i < count($nik_approver); $i++)
                {
                    $kategori_approver = $request->kategori_approver;
                    $sub_kategori_approver = $request->sub_kategori_approver;
                    $dept_approver = $request->dept_approver;

                $id_im = DB::table('internal_memo')->orderBy('id', 'DESC')->first(); 

                DB::table('internal_memo_ttd')->insert([
                    'id_im' => $id_im->id,
                    'no_dokumen' => $id_im->no_dokumen,
                    'nik_tujuan' => $nik_approver[$i],
                    'dept_tujuan' => $dept_approver[$i],
                    'kategori' => $kategori_approver[$i],
                    'sub_kategori' => $sub_kategori_approver[$i],
                    ]);
                }
            }

            if($nik_mengetahui != NULL )
            {
                for($i = 0; $i < count($nik_mengetahui); $i++)
                {
                    $kategori_mengetahui     = $request->kategori_mengetahui;
                    $sub_kategori_mengetahui = $request->sub_kategori_mengetahui;
                    $dept_mengetahui = $request->dept_mengetahui;

                $id_im = DB::table('internal_memo')->orderBy('id', 'DESC')->first(); 
                DB::table('internal_memo_ttd')->insert([
                    'id_im' => $id_im->id,
                    'no_dokumen' => $id_im->no_dokumen,
                    'nik_tujuan' => $nik_mengetahui[$i],
                    'dept_tujuan' => $dept_mengetahui[$i],
                    'kategori' => $kategori_mengetahui[$i],
                    'sub_kategori' => $sub_kategori_mengetahui[$i],
                    ]);
                }
            }
            
            if($nik_penerima != NULL )
            {
                for($i = 0; $i < count($nik_penerima); $i++)
                {
                    $kategori_penerima     = $request->kategori_penerima;
                    $sub_kategori_penerima = $request->sub_kategori_penerima;
                    $dept_penerima = $request->dept_penerima;

                $id_im = DB::table('internal_memo')->orderBy('id', 'DESC')->first(); 
                DB::table('internal_memo_ttd')->insert([
                    'id_im' => $id_im->id,
                    'no_dokumen' => $id_im->no_dokumen,
                    'nik_tujuan' => $nik_penerima[$i],
                    'dept_tujuan' => $dept_penerima[$i],
                    'kategori' => $kategori_penerima[$i],
                    'sub_kategori' => $sub_kategori_penerima[$i],
                    ]);
                }
            }
            
            $id_im = DB::table('internal_memo')->orderBy('id', 'DESC')->first(); 
        }
        Session::flash('success', 'Surat Berhasil Dibuat');

        return redirect('/internal_memo/menu/detail_dokumen/'. Crypt::encrypt(Auth::user()->username) . '/'. $id_im->id  );
    }
  
    public function history_dokumen($nik)
    {
        $nik = Crypt::decrypt($nik);

        $detail = DB::table('internal_memo')
        ->where('nik_pengisi', $nik)
        ->get();

         $list_selesai = DB::table('internal_memo')
                ->select(
                    'internal_memo.no_dokumen',
                    'internal_memo.tgl_pengisian',
                    'internal_memo.jam_pengisian',
                    'internal_memo.perihal',
                    'internal_memo.nik_pembuat',
                    'internal_memo.id',
                    'internal_memo_ttd.nik_tujuan',
                    'internal_memo_ttd.id_im',
                    'internal_memo_ttd.sub_kategori',
                    'internal_memo_ttd.status',
                )->join('internal_memo_ttd', 'internal_memo_ttd.id_im', '=', 'internal_memo.id')
                ->where('internal_memo_ttd.nik_tujuan', $nik)
                ->get();

        return view('internal_memo.history_dokumen', compact('detail', 'list_selesai'));
    }
    
    public function tracking_dokumen($id_im)
    {
        $id_im = Crypt::decrypt($id_im);

        $detail = InternalMemoModel::where('id', $id_im)->first();

        $data = DB::table('internal_memo_ttd')
        ->select(
            'internal_memo_ttd.nik_tujuan',
            'internal_memo_ttd.status',
            'internal_memo_ttd.kategori',
            'internal_memo_ttd.alasan_tolak',
            'internal_memo_ttd.tgl_ttd',
            'internal_memo_ttd.jam_ttd',
            'internal_memo_ttd.id',
            'departments.name',
            'users.dept_id',
            'users.name')
        ->join('users', 'users.username', '=', 'internal_memo_ttd.nik_tujuan')
        ->join('departments', 'departments.id', '=', 'users.dept_id')
        ->where('internal_memo_ttd.id_im', $id_im)
        ->get();

        return view('internal_memo.tracking_dokumen', compact('data', 'detail'));
    }
    
    public function detail_dokumen($nik, $id)
    {
        $nik = Crypt::decrypt($nik);
        
        $detail = DB::table('internal_memo')->join('departments', 'departments.id', '=', 'internal_memo.dept_pembuat')->where('internal_memo.nik_pembuat', $nik)->where('internal_memo.id', $id)->first();

        $ttd = DB::table('internal_memo')
        ->select(
            'internal_memo_ttd.nik_tujuan',
            'internal_memo_ttd.dept_tujuan',
            'internal_memo_ttd.status',
            'internal_memo_ttd.kategori',
            'internal_memo_ttd.sub_kategori',
            'users.name',
        )
        ->join('internal_memo_ttd', 'internal_memo_ttd.id_im', '=', 'internal_memo.id')
        ->join('users', 'users.username', '=', 'internal_memo_ttd.nik_tujuan')
        ->join('departments', 'users.dept_id', '=', 'departments.id')
        ->where('internal_memo_ttd.id_im', $id)
        ->get();

        $dept_ttd = DB::table('internal_memo')
        ->select(
            'internal_memo_ttd.nik_tujuan',
            'internal_memo_ttd.dept_tujuan',
            'internal_memo_ttd.status',
            'internal_memo_ttd.kategori',
            'internal_memo_ttd.sub_kategori',
            'departments.name',
        )
        ->join('internal_memo_ttd', 'internal_memo_ttd.id_im', '=', 'internal_memo.id')
        ->join('users', 'users.username', '=', 'internal_memo_ttd.nik_tujuan')
        ->join('departments', 'users.dept_id', '=', 'departments.id')
        ->where('internal_memo_ttd.id_im', $id)
        ->get();
        
        $kepada = DB::table('internal_memo_ttd')
        ->join('users', 'users.username', '=', 'internal_memo_ttd.nik_tujuan')
        ->where('internal_memo_ttd.id_im', $id)
        ->where('internal_memo_ttd.kategori', 'penerima')->get();
        
        $cc = DB::table('internal_memo_ttd')
        ->select('users.name')
        ->join('users', 'users.username', '=', 'internal_memo_ttd.nik_tujuan')
        ->where('internal_memo_ttd.id_im', $id)
        ->where('internal_memo_ttd.kategori', '!=', 'Penerima')->get();
        

        $dept = DB::table('internal_memo')
        ->select('departments.name')
        ->join('users', 'users.username', '=', 'internal_memo.nik_pembuat')
        ->join('departments', 'users.dept_id', '=', 'departments.id')
        ->where('users.username', $nik)->first();

        return view('internal_memo.detail_dokumen', compact('detail','ttd', 'kepada', 'cc', 'dept', 'dept_ttd'));
    }
    
    public function export_pdf($id)
    {
        $id = Crypt::decrypt($id);

        $detail = InternalMemoModel::where('id', $id)->first();

        $ttd = DB::table('internal_memo')
        ->join('internal_memo_ttd', 'internal_memo_ttd.id_im', '=', 'internal_memo.id')
        ->where('internal_memo_ttd.id_im', $id)
        ->get();

        $kepada = DB::table('internal_memo_ttd')
        ->join('users', 'users.username', '=', 'internal_memo_ttd.nik_tujuan')
        ->where('internal_memo_ttd.id_im', $id)
        ->where('internal_memo_ttd.kategori', 'penerima')->get();
        
        $cc = DB::table('internal_memo_ttd')
        ->select('users.name')
        ->join('users', 'users.username', '=', 'internal_memo_ttd.nik_tujuan')
        ->where('internal_memo_ttd.id_im', $id)->get();


        $dept = DB::table('internal_memo')
        ->select('departments.name')
        ->join('users', 'users.username', '=', 'internal_memo.nik_pembuat')
        ->join('departments', 'users.dept_id', '=', 'departments.id')
        ->where('internal_memo.id', $id)->first();

        $pdf =  PDF::loadview('internal_memo.export_pdf', compact('detail','ttd', 'kepada', 'cc', 'dept'))->setPaper('A4','potrait');
        return $pdf->stream();
        // return $pdf->download('laporan-post.pdf');
    }

    public function form_ttd( $id, $sub_kategori, $nik, $status)
    {
        $nik = Crypt::decrypt($nik);
        $pembuat = InternalMemoModel::find($id);
        $dept_pembuat = DB::table('departments')->where('id', $pembuat->dept_pembuat)->first();

        $detail = DB::table('internal_memo')
        ->select(
            'internal_memo.no_dokumen',
            'internal_memo.tgl_pengisian',
            'internal_memo.nama_pengisi',
            'internal_memo.nik_pengisi',
            'internal_memo.dept_pembuat',
            'internal_memo.nik_pembuat',
            'internal_memo.perihal',
            'internal_memo.konten',
            'internal_memo_ttd.nik_tujuan',
            'internal_memo_ttd.status',
            'internal_memo_ttd.id_im',
            'internal_memo_ttd.kategori',
            'internal_memo_ttd.sub_kategori',
            'internal_memo_ttd.id',
            'departments.name',
            'users.dept_id',
            'users.name',
        )->join('internal_memo_ttd', 'internal_memo_ttd.id_im', '=', 'internal_memo.id')
        ->join('users', 'users.username', '=', 'internal_memo_ttd.nik_tujuan')
        ->join('departments', 'departments.id', '=', 'users.dept_id')
        ->where('internal_memo_ttd.nik_tujuan', $nik)
        ->where('internal_memo_ttd.sub_kategori', $sub_kategori)
        ->where('internal_memo_ttd.status', $status)
        ->first();

        $ttd = DB::table('internal_memo')
        ->select(
            'internal_memo_ttd.nik_tujuan',
            'internal_memo_ttd.dept_tujuan',
            'internal_memo_ttd.status',
            'internal_memo_ttd.kategori',
            'internal_memo_ttd.sub_kategori',
            'users.name',
        )
        ->join('internal_memo_ttd', 'internal_memo_ttd.id_im', '=', 'internal_memo.id')
        ->join('users', 'users.username', '=', 'internal_memo_ttd.nik_tujuan')
        ->join('departments', 'users.dept_id', '=', 'departments.id')
        ->where('internal_memo_ttd.nik_tujuan', $nik)
        ->get();
        $dept_tujuan = "";

        foreach($ttd as $val)
        {
            $dept_tujuan = DB::Table('departments')
            ->where('id', $val->dept_tujuan)
            ->first();
        }

        $kepada = DB::table('internal_memo_ttd')
        ->join('users', 'users.username', '=', 'internal_memo_ttd.nik_tujuan')
        ->where('internal_memo_ttd.kategori', 'penerima')->get();
        
        $cc = DB::table('internal_memo_ttd')
        ->select(
            'users.name',
            'users.dept_id',
            'internal_memo_ttd.status',
            'internal_memo_ttd.kategori',
        )->join('users', 'users.username', '=', 'internal_memo_ttd.nik_tujuan')
        ->where('internal_memo_ttd.kategori', '!=', 'Penerima')
        ->where('internal_memo_ttd.id_im', $id)->get();

        $dept = DB::table('internal_memo')
        ->select('departments.name')
        ->join('users', 'users.username', '=', 'internal_memo.nik_pembuat')
        ->join('departments', 'users.dept_id', '=', 'departments.id')
        ->where('users.username', $pembuat->nik_pembuat)->first();

        return view('internal_memo.form_ttd', compact('detail', 'pembuat', 'kepada', 'cc', 'dept', 'ttd', 'dept_pembuat', 'dept_tujuan'));
    }
    
    public function read_notif(Request $request, $id)
    {
        $nik = $request->nik;
        $id_im = $request->id_im;

       $data =  DB::table('internal_memo_notifikasi')
        ->where('id', $id)
        ->update([
            'status' => 0 
        ]);
        
        return redirect('/internal_memo/menu/detail_dokumen/'.Crypt::encrypt($nik).'/'.$id_im);
    }
    
    public function read_all_notif($nik)
    {
        $data =  DB::table('internal_memo_notifikasi')
        ->where('notif_to_nik', $nik)
        ->update([
            'status' => 0 
        ]);

        return redirect()->back();
    }
    public function form_revisi($id, $nik)
    {
        $nik = Crypt::decrypt($nik);

        $pembuat = InternalMemoModel::find($id);
        
        $detail = DB::table('internal_memo')
        ->select(
            'internal_memo.no_dokumen',
            'internal_memo.tgl_pengisian',
            'internal_memo.nik_pembuat',
            'internal_memo.perihal',
            'internal_memo.konten',
            'internal_memo_ttd.id_im',
            'internal_memo_ttd.nik_tujuan',
            'internal_memo_ttd.status',
            'internal_memo_ttd.id',
            'departments.name',
            'users.dept_id',
            'users.name',
        )->join('internal_memo_ttd', 'internal_memo_ttd.id_im', '=', 'internal_memo.id')
        ->join('users', 'users.username', '=', 'internal_memo_ttd.nik_tujuan')
        ->join('departments', 'departments.id', '=', 'users.dept_id')
        ->where('internal_memo_ttd.nik_tujuan', $nik)
        ->first();
        
        return view('internal_memo.form_revisi', compact('detail'));
    }
    public function konfirmasi(Request $request, $id)
    {
        $konfirmasi = $request->konfirmasi;
        $sub_kategori = $request->sub_kategori;
        $nik_tujuan = $request->nik_tujuan;
        $nama = $request->nama_tujuan;
        $no_dokumen = $request->no_dokumen;
        $notif_from_nik = $request->nik_tujuan;
        $notif_to_nik = $request->notif_to_nik;
        $id_im = $request->id_im;

        if($konfirmasi == "0")
        {
            $data = DB::table('internal_memo_ttd')->where('id', $id)->update([
                'tgl_ttd' => date('Y-m-d'),
                'jam_ttd' => date('H:i'),
                'status' => 0,
                ]);

            $data = DB::table('internal_memo')->where('no_dokumen', $no_dokumen)->update([
                'status' => 1,
                'progress' => 'Disetujui'. ' '. $nama. ' ' .' | ' . $nik_tujuan . ' ' .  'Sebagai'. ' ' . $sub_kategori,
            ]);

            DB::table('internal_memo_notifikasi')->insert([
                'no_notif' => $no_dokumen. '-'.$id_im. '-'.date('d/m/Y'),
                'id_im' => $id_im,
                'no_dokumen' => $no_dokumen,
                'notif_from_nik' => $notif_from_nik,
                'notif_to_nik' => $notif_to_nik,
                'jenis_notif' => 1,
                'isi' => 'Surat Dengan No. '.  $no_dokumen  .' '. 'Selesai Di Approve Oleh' . ' ' . $nama
            ]);

        Session::flash('success', 'Berhasil Di Konfrimasi..');
        return redirect('/internal_memo/menu/outstanding/'.Crypt::encrypt($nik_tujuan));
        }

        else
        {
            $data = DB::table('internal_memo_ttd')->where('id', $id)->update([
                'status' => 2,
                'alasan_tolak' => $request->alasan_tolak,
                ]);
               
            $data = DB::table('internal_memo')->where('no_dokumen', $no_dokumen)->update([
                'status' => 2,
                'progress' => 'Di Tolak '. ' '. $nama. ' ' .' | ' . $nik_tujuan . ' ' .  'Sebagai'. ' ' . $sub_kategori,
                'alasan_tolak' => $request->alasan_tolak,
            ]);

                DB::table('internal_memo_notifikasi')->insert([
                'no_notif' => $no_dokumen. '-'.$id_im. '-'.date('d/m/Y'),
                'id_im' => $id_im,
                'no_dokumen' => $no_dokumen,
                'notif_from_nik' => $notif_from_nik,
                'notif_to_nik' => $notif_to_nik,
                'jenis_notif' => 2,
                'isi' => 'Surat Dengan No. '.  $no_dokumen  .' '. 'Di Tolak Oleh' . ' ' . $nama. ' '. 'Silahkan Revisi Surat kamu'
            ]);

        Session::flash('success', 'Berhasil Mengirim Revisi..');
        return redirect('/internal_memo/menu/outstanding/'.Crypt::encrypt($nik_tujuan));
        }
    }
     public function update_dokumen(Request $request, $id, $nik_tujuan)
    {
        $nik_tujuan = Crypt::decrypt($nik_tujuan);

        $konten = $request->konten;
        $nik_tujuan = $request->nik_tujuan;
        $nama = $request->nama_tujuan;
        $no_dokumen = $request->no_dokumen;
        $notif_from_nik = $request->notif_from_nik;
        $notif_to_nik = $request->notif_to_nik;
        $id_im = $request->id_im;
        $perihal = $request->perihal;

            $data = DB::table('internal_memo')->where('id', $id)->update([
                'konten' => $konten,
                'perihal' => $perihal,
                'status' => 0,
                'progress' => 'Selesai Revisi Dokumen',
                'alasan_tolak' => "",
                ]);

            $data = DB::table('internal_memo_ttd')
            ->where('id_im', $id)
            ->where('nik_tujuan', $nik_tujuan)
            ->update([
                'status' => 1,
                'alasan_tolak' => "",
                ]);

            DB::table('internal_memo_notifikasi')->insert([
            'no_notif' => $no_dokumen. '-'.$id_im. '-'.date('d/m/Y'),
            'id_im' => $id_im,
            'no_dokumen' => $no_dokumen,
            'notif_from_nik' => $notif_from_nik,
            'notif_to_nik' => $notif_to_nik,
            'jenis_notif' => 3,
            'isi' => 'Surat Dengan No. '.  $no_dokumen  .' '. 'Selesai Di Revisi'
            ]);

        Session::flash('success', 'Berhasil Di Update..');

        return redirect('/internal_memo/menu/index');
    }
    public function outstanding($nik)
    {
        $nik = Crypt::decrypt($nik);

        $list_pending = DB::table('internal_memo')
                ->select(
                    'internal_memo.no_dokumen',
                    'internal_memo.tgl_pengisian',
                    'internal_memo.jam_pengisian',
                    'internal_memo.perihal',
                    'internal_memo.nik_pembuat',
                    'internal_memo.id',
                    'internal_memo_ttd.id_im',
                    'internal_memo_ttd.nik_tujuan',
                    'internal_memo_ttd.sub_kategori',
                    'internal_memo_ttd.status',
                )->join('internal_memo_ttd', 'internal_memo_ttd.id_im', '=', 'internal_memo.id')
                ->where('internal_memo_ttd.nik_tujuan', $nik)
                ->where('internal_memo_ttd.status', 1)
                ->get();

        $list_selesai = DB::table('internal_memo')
                ->select(
                    'internal_memo.no_dokumen',
                    'internal_memo.tgl_pengisian',
                    'internal_memo.jam_pengisian',
                    'internal_memo.perihal',
                    'internal_memo.nik_pembuat',
                    'internal_memo.id',
                    'internal_memo_ttd.nik_tujuan',
                    'internal_memo_ttd.sub_kategori',
                    'internal_memo_ttd.status',
                )->join('internal_memo_ttd', 'internal_memo_ttd.id_im', '=', 'internal_memo.id')
                ->where('internal_memo_ttd.nik_tujuan', $nik)
                ->where('internal_memo_ttd.status', 0)
                ->get();
        
        $status_user = DB::table('users')
                       ->join('internal_memo_ttd', 'internal_memo_ttd.nik_tujuan', '=', 'users.username')
                       ->where('internal_memo_ttd.nik_tujuan', Auth::user()->username)
                       ->where('internal_memo_ttd.status', 1)
                       ->first();
                       
        return view('internal_memo.outstanding', compact('list_pending', 'list_selesai', 'status_user'));
    }

    public function penerima(Request $request)
    {
        $id_im = $request->id_im;
        
        $update = DB::table('internal_memo_ttd')
                  ->where('id_im', $id_im)
                  ->where('nik_tujuan', Auth::user()->username)
                  ->update([
                      'status'  => 0,
                      'tgl_ttd' => date('Y-m-d'),
                      'jam_ttd' => date('H:i')
                  ]);

          DB::table('internal_memo_notifikasi')->insert([
                'no_notif' => $request->no_dokumen. '-'.$request->id_im. '-'.date('d/m/Y'),
                'id_im' => $request->id_im,
                'no_dokumen' => $request->no_dokumen,
                'notif_from_nik' => $request->notif_from_nik,
                'notif_to_nik' => $request->notif_to_nik,
                'jenis_notif' => 1,
                'isi' => 'Surat Dengan No. '.  $request->no_dokumen  .' '. 'Selesai Di Terima Dan Sudah Di Baca'
            ]);

            return redirect('/internal_memo/menu/detail_dokumen/'. Crypt::encrypt($request->notif_to_nik) . '/'. $request->id_im);
    }
    public function list_revisi($status, $nik)
    {
        $nik = Crypt::decrypt($nik);

        $list_revisi = DB::table('internal_memo')
                ->select(
                    'internal_memo.no_dokumen',
                    'internal_memo.tgl_pengisian',
                    'internal_memo.jam_pengisian',
                    'internal_memo.perihal',
                    'internal_memo.nik_pembuat',
                    'internal_memo.status',
                    'internal_memo.id',
                    'internal_memo_ttd.nik_tujuan',
                    'internal_memo_ttd.alasan_tolak',
                    'internal_memo_ttd.sub_kategori',
                    'internal_memo_notifikasi.notif_from_nik',
                    'users.name',
                )
                ->join('internal_memo_ttd', 'internal_memo.id', '=', 'internal_memo_ttd.id_im')
                ->join('users', 'users.username', '=','internal_memo.nik_pembuat')
                ->join('internal_memo_notifikasi', 'internal_memo_notifikasi.id_im', '=','internal_memo.id')
                ->where('internal_memo.nik_pembuat', $nik)
                ->where('internal_memo.status', $status)
                ->groupBy('internal_memo.no_dokumen')
                ->get();

            $nama_tolak ="";
            foreach($list_revisi as $list)
            {
                $nama_tolak = $list->notif_from_nik;
            }
            $nama_penolak = DB::table('users')->where('username', $nama_tolak)->first();

        return view('internal_memo.list_revisi', compact('list_revisi', 'nama_penolak'));
    }
    public function post_nama($isi)
    {
        $data = DB::table('users')
        ->where('username', $isi)
        ->first();
        
        return response()->json([
            'success' => 1,
            'data' => $data
        ]);
    }
   
    public function add_akun($nama, $nik)
    {
        $cek_user = User::where('username', 'LIKE', '%'.$nik.'%')->first();

        if($cek_user == NULL)
        {
            $data = User::create([
                'username' => $nik,
                'name' => $nama,
                'password' => '$2y$10$ckUAw8RgiYSMhxgbfgut9.Z9I7P4FsSdavWSuYixh5MfG9GX31vCe',
                'auth_group_id' => 0,
                'dept_id' => 0,
                'status' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                ]);
                
                return response()->json([
                    'success' => 1,
                    'data' => $data
                    ]);
        }
    }

       public function logout()
    {
        Auth::logout();
        Session::flash('success', 'Berhasil Logout..');
        return redirect('/');
    }
}
