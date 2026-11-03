<?php

namespace App\Http\Controllers\PoolQcc;

use App\Http\Controllers\Controller;
use App\Models\PoolQCC\PoolBooth;
use App\Models\PoolQCC\PoolQuestion;
use App\Models\PoolQCC\PoolParticipant;
use App\Models\PoolQCC\PoolVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;

class PoolQccAdminController extends Controller
{
    /**
     * Daftar departemen statis yang valid
     */
    public static $departments = [
        'Engineering',
        'Quality Control',
        'Warehouse',
        'Produksi',
        'Expedisi',
        'HSE',
        'HRGA'
    ];

    /**
     * Generate Kode Booth Otomatis dari backend (Contoh: B01, B02, dst)
     */
    private function generateBoothCode()
    {
        $allCodes = PoolBooth::pluck('code')->toArray();
        $maxNum = 0;

        foreach ($allCodes as $code) {
            // Ambil angka dari kode booth (misal B01 -> 1, BOOTH-5 -> 5)
            if (preg_match('/\d+/', $code, $matches)) {
                $num = (int)$matches[0];
                if ($num > $maxNum) {
                    $maxNum = $num;
                }
            }
        }

        $nextNum = $maxNum + 1;
        return 'B' . str_pad($nextNum, 2, '0', STR_PAD_LEFT);
    }

    /**
     * Dashboard Overview Pool QCC
     */
    public function index()
    {
        return view('pool_qcc.admin.dashboard');
    }

    public function getDashboardData()
    {
        $totalBooths = PoolBooth::count();
        $totalQuestions = PoolQuestion::count();
        $totalParticipants = PoolParticipant::count();
        $totalVotes = PoolVote::count();
        $correctVotes = PoolVote::where('is_correct', true)->count();
        $accuracyRate = $totalVotes > 0 ? round(($correctVotes / $totalVotes) * 100, 1) : 0;

        $topBooths = PoolBooth::orderBy('total_points', 'desc')
            ->orderBy('total_voters', 'desc')
            ->take(5)
            ->get();

        $topParticipants = PoolParticipant::orderBy('total_score', 'desc')
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        $recentVotes = PoolVote::with(['participant', 'booth', 'question'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($v) {
                return [
                    'id' => $v->id,
                    'created_at_formatted' => $v->created_at ? $v->created_at->format('H:i:s d/m/Y') : '-',
                    'participant_name' => $v->participant ? $v->participant->name : '-',
                    'booth_name' => $v->booth ? $v->booth->name : '-',
                    'booth_code' => $v->booth ? $v->booth->code : '',
                    'participant_answer' => $v->participant_answer,
                    'is_correct' => (bool) $v->is_correct,
                    'points_awarded' => (int) $v->points_awarded,
                ];
            });

        $incompleteBooths = PoolBooth::withCount(['activeQuestions' => function ($q) {
            $q->where('is_active', true);
        }])
            ->having('active_questions_count', '<', 5)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'totalBooths' => $totalBooths,
                'totalQuestions' => $totalQuestions,
                'totalParticipants' => $totalParticipants,
                'totalVotes' => $totalVotes,
                'correctVotes' => $correctVotes,
                'accuracyRate' => $accuracyRate,
                'topBooths' => $topBooths,
                'topParticipants' => $topParticipants,
                'recentVotes' => $recentVotes,
                'incompleteBooths' => $incompleteBooths,
            ]
        ]);
    }

    /**
     * Manajemen Booth
     */
    public function booths()
    {
        $departments = self::$departments;
        $nextCode = $this->generateBoothCode();

        return view('pool_qcc.admin.booths', compact('departments', 'nextCode'));
    }

    public function getBoothsData(Request $request)
    {
        $booths = PoolBooth::withCount(['questions', 'activeQuestions'])
            ->orderBy('code', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'total' => $booths->count(),
            'data' => $booths
        ]);
    }

    public function storeBooth(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category' => 'required|string|in:' . implode(',', self::$departments),
            'pic_name' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'name.required' => 'Nama Booth / Judul Inovasi wajib diisi.',
            'category.required' => 'Departemen wajib dipilih.',
            'category.in' => 'Pilihan departemen tidak valid.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
            'image.uploaded' => 'Ukuran file gambar melebihi batas upload server (Maksimal 2MB). Silakan kompres atau pilih file gambar yang lebih kecil.',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi data gagal. Silakan periksa kolom yang ditandai merah.',
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Auto generate kode booth dari backend
        $autoCode = $this->generateBoothCode();

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'booth_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/pool_qcc/booths'), $filename);
            $imagePath = 'uploads/pool_qcc/booths/' . $filename;
        }

        $booth = PoolBooth::create([
            'code' => $autoCode,
            'name' => $request->name,
            'category' => $request->category,
            'location' => null,
            'pic_name' => $request->pic_name,
            'pic_contact' => null,
            'description' => $request->description,
            'image' => $imagePath,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : true,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => "Booth {$autoCode} berhasil ditambahkan!",
                'booth' => $booth,
            ]);
        }

        return redirect()->back()->with('success', "Booth {$autoCode} berhasil ditambahkan! Silakan lengkapi minimal 5 pertanyaan untuk booth ini.");
    }

    public function updateBooth(Request $request, $id)
    {
        $booth = PoolBooth::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category' => 'required|string|in:' . implode(',', self::$departments),
            'pic_name' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'name.required' => 'Nama Booth / Judul Inovasi wajib diisi.',
            'category.required' => 'Departemen wajib dipilih.',
            'category.in' => 'Pilihan departemen tidak valid.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
            'image.uploaded' => 'Ukuran file gambar melebihi batas upload server (Maksimal 2MB). Silakan kompres atau pilih file gambar yang lebih kecil.',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi data gagal. Silakan periksa kolom yang ditandai merah.',
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = [
            'name' => $request->name,
            'category' => $request->category,
            'pic_name' => $request->pic_name,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : true,
        ];

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'booth_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/pool_qcc/booths'), $filename);
            $data['image'] = 'uploads/pool_qcc/booths/' . $filename;
        }

        $booth->update($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => "Data Booth {$booth->code} berhasil diperbarui!",
                'booth' => $booth->fresh(),
            ]);
        }

        return redirect()->back()->with('success', "Data Booth {$booth->code} berhasil diperbarui!");
    }

    public function destroyBooth(Request $request, $id)
    {
        $booth = PoolBooth::findOrFail($id);
        $code = $booth->code;
        $name = $booth->name;
        $booth->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => "Booth {$code} - {$name} beserta seluruh soalnya berhasil dihapus.",
            ]);
        }

        return redirect()->back()->with('success', 'Booth beserta pertanyaan dan datanya berhasil dihapus.');
    }

    /**
     * Manajemen Pertanyaan
     */
    public function questions(Request $request)
    {
        $booths = PoolBooth::orderBy('name', 'asc')->get();
        $selectedBoothId = $request->get('booth_id');

        return view('pool_qcc.admin.questions', compact('booths', 'selectedBoothId'));
    }

    public function getQuestionsData(Request $request)
    {
        $selectedBoothId = $request->get('booth_id');
        $perPage = (int) $request->get('per_page', 10);
        if ($perPage < 1) {
            $perPage = 10;
        }

        $query = PoolQuestion::with('booth')->latest();
        if (!empty($selectedBoothId)) {
            $query->where('booth_id', $selectedBoothId);
        }

        $paginated = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $paginated->items(),
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
                'from' => $paginated->firstItem(),
                'to' => $paginated->lastItem(),
            ]
        ]);
    }

    public function storeQuestion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booth_id' => 'required|exists:pool_booths,id',
            'question_text' => 'required|string',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'required|string',
            'option_d' => 'required|string',
            'option_e' => 'nullable|string',
            'correct_answer' => 'required|in:A,B,C,D,E',
            'points' => 'required|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'booth_id.required' => 'Booth wajib dipilih.',
            'booth_id.exists' => 'Booth yang dipilih tidak valid.',
            'question_text.required' => 'Teks pertanyaan kuis wajib diisi.',
            'option_a.required' => 'Pilihan A wajib diisi.',
            'option_b.required' => 'Pilihan B wajib diisi.',
            'option_c.required' => 'Pilihan C wajib diisi.',
            'option_d.required' => 'Pilihan D wajib diisi.',
            'correct_answer.required' => 'Kunci jawaban yang benar wajib dipilih.',
            'correct_answer.in' => 'Kunci jawaban harus antara A, B, C, D, atau E.',
            'points.required' => 'Bobot poin wajib diisi.',
            'points.integer' => 'Bobot poin harus berupa angka.',
            'points.min' => 'Bobot poin minimal 1.',
            'image.image' => 'File lampiran harus berupa gambar.',
            'image.max' => 'Ukuran gambar lampiran maksimal 2MB.',
            'image.uploaded' => 'Ukuran file gambar lampiran melebihi batas upload server (Maksimal 2MB). Silakan kompres gambar.',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi pertanyaan gagal. Silakan periksa kolom yang ditandai merah.',
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'question_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/pool_qcc/questions'), $filename);
            $imagePath = 'uploads/pool_qcc/questions/' . $filename;
        }

        $question = PoolQuestion::create([
            'booth_id' => $request->booth_id,
            'question_text' => $request->question_text,
            'image' => $imagePath,
            'option_a' => $request->option_a,
            'option_b' => $request->option_b,
            'option_c' => $request->option_c,
            'option_d' => $request->option_d,
            'option_e' => $request->option_e,
            'correct_answer' => strtoupper($request->correct_answer),
            'points' => $request->points ?? 1,
            'explanation' => $request->explanation,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : true,
        ]);

        $booth = PoolBooth::find($request->booth_id);
        $totalActive = $booth ? $booth->activeQuestions()->count() : 0;

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => "Pertanyaan berhasil ditambahkan! Booth '{$booth->name}' sekarang memiliki {$totalActive} soal aktif.",
                'question' => $question,
                'total_active' => $totalActive,
            ]);
        }

        return redirect()->back()->with('success', "Pertanyaan berhasil ditambahkan! Booth '{$booth->name}' sekarang memiliki {$totalActive} pertanyaan aktif.");
    }

    public function updateQuestion(Request $request, $id)
    {
        $question = PoolQuestion::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'booth_id' => 'required|exists:pool_booths,id',
            'question_text' => 'required|string',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'required|string',
            'option_d' => 'required|string',
            'option_e' => 'nullable|string',
            'correct_answer' => 'required|in:A,B,C,D,E',
            'points' => 'required|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'booth_id.required' => 'Booth wajib dipilih.',
            'booth_id.exists' => 'Booth yang dipilih tidak valid.',
            'question_text.required' => 'Teks pertanyaan kuis wajib diisi.',
            'option_a.required' => 'Pilihan A wajib diisi.',
            'option_b.required' => 'Pilihan B wajib diisi.',
            'option_c.required' => 'Pilihan C wajib diisi.',
            'option_d.required' => 'Pilihan D wajib diisi.',
            'correct_answer.required' => 'Kunci jawaban yang benar wajib dipilih.',
            'correct_answer.in' => 'Kunci jawaban harus antara A, B, C, D, atau E.',
            'points.required' => 'Bobot poin wajib diisi.',
            'points.integer' => 'Bobot poin harus berupa angka.',
            'points.min' => 'Bobot poin minimal 1.',
            'image.image' => 'File lampiran harus berupa gambar.',
            'image.max' => 'Ukuran gambar lampiran maksimal 2MB.',
            'image.uploaded' => 'Ukuran file gambar lampiran melebihi batas upload server (Maksimal 2MB). Silakan kompres gambar.',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi pertanyaan gagal. Silakan periksa kolom yang ditandai merah.',
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = [
            'booth_id' => $request->booth_id,
            'question_text' => $request->question_text,
            'option_a' => $request->option_a,
            'option_b' => $request->option_b,
            'option_c' => $request->option_c,
            'option_d' => $request->option_d,
            'option_e' => $request->option_e,
            'correct_answer' => strtoupper($request->correct_answer),
            'points' => $request->points ?? 1,
            'explanation' => $request->explanation,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : true,
        ];

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'question_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/pool_qcc/questions'), $filename);
            $data['image'] = 'uploads/pool_qcc/questions/' . $filename;
        }

        $question->update($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Pertanyaan berhasil diperbarui!',
                'question' => $question->fresh(),
            ]);
        }

        return redirect()->back()->with('success', 'Pertanyaan berhasil diperbarui!');
    }

    public function destroyQuestion(Request $request, $id)
    {
        $question = PoolQuestion::findOrFail($id);
        $question->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Pertanyaan berhasil dihapus.',
            ]);
        }

        return redirect()->back()->with('success', 'Pertanyaan berhasil dihapus.');
    }

    /**
     * Monitoring Peserta & Log Vote
     */
    public function participants()
    {
        return view('pool_qcc.admin.participants');
    }

    public function getParticipantsData(Request $request)
    {
        $query = PoolParticipant::with(['votes.booth'])->latest('last_activity_at');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%")
                    ->orWhere('rfid', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'completed') {
                $query->where('booths_voted_count', '>=', 10);
            } elseif ($request->status === 'in_progress') {
                $query->where('booths_voted_count', '<', 10);
            }
        }

        $perPage = (int) $request->get('per_page', 15);
        if ($perPage < 1) $perPage = 20;

        $paginated = $query->paginate($perPage);

        $items = collect($paginated->items())->map(function ($p) {
            $votes = $p->votes->map(function ($v) {
                return [
                    'id' => $v->id,
                    'booth_code' => $v->booth ? $v->booth->code : 'B',
                    'is_correct' => (bool) $v->is_correct,
                    'points_awarded' => (int) $v->points_awarded,
                ];
            });

            return [
                'id' => $p->id,
                'name' => $p->name,
                'nik' => $p->nik,
                'rfid' => $p->rfid,
                'department' => $p->department,
                'photo' => $p->photo,
                'booths_voted_count' => (int) $p->booths_voted_count,
                'total_score' => (int) $p->total_score,
                'votes' => $votes,
                'initials' => strtoupper(substr($p->name ?? 'P', 0, 2)),
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $items,
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
                'from' => $paginated->firstItem(),
                'to' => $paginated->lastItem(),
            ]
        ]);
    }

    public function votes(Request $request)
    {
        $booths = PoolBooth::orderBy('name', 'asc')->get();

        return view('pool_qcc.admin.votes', compact('booths'));
    }

    public function getVotesData(Request $request)
    {
        $query = PoolVote::with(['participant', 'booth', 'question'])->latest();

        if ($request->filled('booth_id')) {
            $query->where('booth_id', $request->booth_id);
        }
        if ($request->filled('status')) {
            $query->where('is_correct', $request->status == 'correct');
        }

        $perPage = (int) $request->get('per_page', 15);
        if ($perPage < 1) {
            $perPage = 25;
        }

        $paginated = $query->paginate($perPage);

        $items = collect($paginated->items())->map(function ($v) {
            return [
                'id' => $v->id,
                'created_at_time' => $v->created_at ? $v->created_at->format('H:i:s') : '-',
                'created_at_date' => $v->created_at ? $v->created_at->format('d/m/Y') : '',
                'participant_name' => $v->participant ? $v->participant->name : 'N/A',
                'participant_nik' => $v->participant ? $v->participant->nik : '-',
                'booth_code' => $v->booth ? $v->booth->code : 'N/A',
                'booth_name' => $v->booth ? $v->booth->name : '-',
                'question_text' => $v->question ? $v->question->question_text : 'Soal telah dihapus',
                'participant_answer' => $v->participant_answer,
                'correct_answer' => $v->question ? $v->question->correct_answer : '-',
                'is_correct' => (bool) $v->is_correct,
                'points_awarded' => (int) $v->points_awarded,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $items,
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
                'from' => $paginated->firstItem(),
                'to' => $paginated->lastItem(),
            ]
        ]);
    }

    public function resetParticipantVotes(Request $request, $id)
    {
        $participant = PoolParticipant::findOrFail($id);

        DB::transaction(function () use ($participant) {
            $votes = PoolVote::where('participant_id', $participant->id)->get();
            foreach ($votes as $vote) {
                if ($vote->is_correct && $vote->points_awarded > 0) {
                    PoolBooth::where('id', $vote->booth_id)->decrement('total_points', $vote->points_awarded);
                }
                PoolBooth::where('id', $vote->booth_id)->decrement('total_voters', 1);
                $vote->delete();
            }

            $participant->update([
                'total_score' => 0,
                'booths_voted_count' => 0,
            ]);
        });

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => "Riwayat vote peserta {$participant->name} berhasil di-reset!",
            ]);
        }

        return redirect()->back()->with('success', "Riwayat vote peserta {$participant->name} berhasil di-reset!");
    }
}
