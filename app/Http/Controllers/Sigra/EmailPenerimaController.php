<?php

namespace App\Http\Controllers\Sigra;

use App\Http\Controllers\Controller;
use App\Models\Sigra\EmailPenerima;
use App\Models\Sigra\Perusahaan;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class EmailPenerimaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('sigra.email-penerima');
    }

    public function getAll()
    {
        $query = EmailPenerima::query()
            ->orderBy('jenis', 'asc')
            ->orderByRaw("CASE WHEN active = 'Y' THEN 0 ELSE 1 END");

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('active', function ($item) {
                $value = $item->active ?? null;

                if ($value !== 'Y' && $value !== 'N') {
                    return '';
                }
                return $value == 'Y'
                    ? '<span class="badge badge-success">Aktif</span>'
                    : '<span class="badge badge-danger">Tidak Aktif</span>';
            })
            ->editColumn('jenis', function ($item) {
                if (empty($item->jenis)) return '-';

                return ucwords(str_replace('_', ' ', $item->jenis));
            })
            ->addColumn('action', function ($item) {
                $btnLabel = $item->active === 'Y' ? 'Nonaktifkan' : 'Aktifkan';
                $btnClass = $item->active === 'Y' ? 'btn-danger' : 'btn-success';
                $icon     = $item->active === 'Y' ? 'fa-times' : 'fa-check';

                return '
                    <div class="d-flex">
                        <button class="btn btn-sm btn-primary mr-2" onclick="editData(' . $item->id . ')">
                            <i class="fa fa-edit"></i>
                        </button>

                        <button class="btn btn-sm btn-danger mr-2" onclick="deleteData(' . $item->id . ')">
                            <i class="fa fa-trash"></i>
                        </button>

                        <button class="btn btn-sm ' . $btnClass . '" onclick="toggleStatus(' . $item->id . ')">
                            <i class="fa ' . $icon . '"></i> ' . $btnLabel . '
                        </button>
                    </div>                
                ';
            })
            ->rawColumns(['active', 'action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'email_penerima' => 'required|email',
            'keterangan' => 'required|string',
            'jenis' => 'required',
        ]);

        $exists = EmailPenerima::where('email_penerima', $request->email_penerima)
            ->where('jenis', $request->jenis)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Email tersebut sudah terdaftar untuk jenis yang sama.',
            ], 422);
        }

        try {
            EmailPenerima::create([
                'email_penerima' => $request->email_penerima,
                'keterangan'      => $request->keterangan,
                'jenis'          => $request->jenis,
                'active'         => 'Y',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Email penerima berhasil ditambahkan.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan, data tidak dapat disimpan.'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $email = EmailPenerima::find($id);

        if (!$email) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $email
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'email_penerima' => 'required|email',
            'keterangan'     => 'required',
            'jenis'          => 'required',
        ]);

        try {
            $email = EmailPenerima::find($id);

            if (!$email) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data email tidak ditemukan.'
                ], 404);
            }

            $exists = EmailPenerima::where('email_penerima', $request->email_penerima)
                ->where('jenis', $request->jenis)
                ->where('id', '!=', $request->id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email tersebut sudah terdaftar untuk jenis yang sama.'
                ], 422);
            }

            $email->update([
                'email_penerima' => $request->email_penerima,
                'keterangan'     => $request->keterangan,
                'jenis'          => $request->jenis,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Email penerima berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan, data tidak dapat diperbarui.'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $email = EmailPenerima::find($id);

            if (!$email) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan.'
                ], 404);
            }

            $email->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus permanen.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function toggle($id)
    {
        try {
            $email = EmailPenerima::find($id);

            if (!$email) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan.'
                ], 404);
            }

            // Toggle status (Y <-> N)
            $email->active = $email->active === 'Y' ? 'N' : 'Y';
            $email->save();

            return response()->json([
                'success' => true,
                'message' => $email->active === 'Y'
                    ? 'Notifikasi berhasil diaktifkan kembali.'
                    : 'Notifikasi berhasil dinonaktifkan.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
