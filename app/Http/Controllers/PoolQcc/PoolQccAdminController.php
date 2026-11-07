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
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PoolQCC\TemplateBoothExport;
use App\Exports\PoolQCC\TemplateQuestionExport;
use App\Exports\PoolQCC\TemplateParticipantExport;
use App\Models\PoolQCC\PoolJudge;
use App\Models\PoolQCC\PoolJudgeEvaluation;
use App\Models\PoolQCC\PoolEvaluationCriterion;
use App\Models\PoolQCC\PoolJudgeEvaluationDetail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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
        $totalBooths = PoolBooth::where('is_active', true)->count();
        if ($totalBooths <= 0) {
            $totalBooths = PoolBooth::count();
        }
        $totalBooths = max(1, $totalBooths);

        return view('pool_qcc.admin.participants', compact('totalBooths'));
    }

    public function getParticipantsData(Request $request)
    {
        $totalBooths = PoolBooth::where('is_active', true)->count();
        if ($totalBooths <= 0) {
            $totalBooths = PoolBooth::count();
        }
        $maxBooths = max(1, $totalBooths);

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
                $query->where('booths_voted_count', '>=', $maxBooths);
            } elseif ($request->status === 'in_progress') {
                $query->where('booths_voted_count', '<', $maxBooths);
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
            'max_booths' => $maxBooths,
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

    /**
     * Hapus Permanen Data Peserta beserta Riwayat Vote
     */
    public function destroyParticipant(Request $request, $id)
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

            $participant->delete();
        });

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => "Data peserta {$participant->name} berhasil dihapus permanen!",
            ]);
        }

        return redirect()->back()->with('success', "Data peserta {$participant->name} berhasil dihapus!");
    }

    /**
     * Halaman Manajemen Dewan Juri
     */
    public function judges()
    {
        return view('pool_qcc.admin.judges');
    }

    /**
     * API Data Dewan Juri
     */
    public function getJudgesData(Request $request)
    {
        $judges = PoolJudge::withCount('evaluations')
            ->orderBy('is_active', 'desc')
            ->orderBy('name', 'asc')
            ->get()
            ->map(function ($j) {
                return [
                    'id' => $j->id,
                    'rfid' => $j->rfid,
                    'nik' => $j->nik,
                    'name' => $j->name,
                    'department' => $j->department,
                    'title' => $j->title ?: 'Juri',
                    'vote_weight' => (int)($j->vote_weight > 0 ? $j->vote_weight : 10),
                    'photo' => $j->photo,
                    'is_active' => (bool)$j->is_active,
                    'evaluations_count' => (int)$j->evaluations_count,
                    'created_at_formatted' => $j->created_at ? $j->created_at->format('d/m/Y H:i') : '-',
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $judges,
            'total' => $judges->count(),
            'active_count' => $judges->where('is_active', true)->count(),
        ]);
    }

    /**
     * Lookup Data Karyawan by RFID atau NIK untuk Registrasi Dewan Juri
     */
    public function lookupJudge(Request $request)
    {
        $rawInput = trim((string)($request->input('query') ?: $request->input('input') ?: $request->input('nik') ?: $request->input('rfid')));

        if (empty($rawInput)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Silakan masukkan NIK atau scan kartu RFID Juri.',
            ], 422);
        }

        $numericInput = is_numeric($rawInput) ? (int)$rawInput : 0;

        // Generate variasi NIK (leading zeroes)
        $candidates = [$rawInput];
        if (is_numeric($rawInput)) {
            $stripped = ltrim($rawInput, '0');
            if ($stripped !== '') {
                $candidates[] = $stripped;
                $candidates[] = '0' . $stripped;
                $candidates[] = str_pad($stripped, 8, '0', STR_PAD_LEFT);
                $candidates[] = str_pad($stripped, 9, '0', STR_PAD_LEFT);
            }
        }
        $candidates = array_values(array_unique(array_filter($candidates)));

        // 1. Cek apakah sudah terdaftar di pool_judges
        $existingJudge = PoolJudge::where('rfid', $rawInput)
            ->orWhereIn('nik', $candidates)
            ->when($numericInput > 0, function ($q) use ($numericInput) {
                $q->orWhere('rfid', (string)$numericInput);
            })
            ->first();

        if ($existingJudge) {
            return response()->json([
                'status' => 'already_registered',
                'message' => "Juri {$existingJudge->name} ({$existingJudge->nik}) sudah terdaftar sebagai Dewan Juri!",
                'data' => [
                    'id' => $existingJudge->id,
                    'nik' => $existingJudge->nik,
                    'name' => $existingJudge->name,
                    'department' => $existingJudge->department,
                    'rfid' => $existingJudge->rfid,
                    'photo' => $existingJudge->photo,
                    'title' => $existingJudge->title,
                    'is_active' => (bool)$existingJudge->is_active,
                ],
            ]);
        }

        // 2. Lookup ke database karyawan (MSIDCARD)
        $employeeData = null;
        try {
            $matchedCard = DB::connection('192.168.178.44-admin')
                ->table('MSIDCARD')
                ->select('NIK', 'EMPNM', 'DEPTID', 'FOTOBLOB', 'CARDNODEVICE', 'RFID', 'STATUS', 'BARCODE')
                ->where(function ($q) use ($candidates, $numericInput, $rawInput) {
                    if ($numericInput > 0) {
                        $q->where('CARDNODEVICE', $numericInput);
                    }
                    $q->orWhere('RFID', $rawInput)
                        ->orWhereIn('NIK', $candidates)
                        ->orWhereIn('BARCODE', $candidates);
                })
                ->orderByRaw("CASE WHEN STATUS = 'X' THEN 0 ELSE 1 END ASC, CAST(EMPCARDID AS SIGNED) DESC")
                ->first();

            if ($matchedCard) {
                if (!empty($matchedCard->NIK)) {
                    $activeCard = DB::connection('192.168.178.44-admin')
                        ->table('MSIDCARD')
                        ->select('NIK', 'EMPNM', 'DEPTID', 'FOTOBLOB', 'CARDNODEVICE', 'RFID', 'STATUS')
                        ->where('NIK', $matchedCard->NIK)
                        ->where('STATUS', 'X')
                        ->orderByRaw('CAST(EMPCARDID AS SIGNED) DESC')
                        ->first();

                    $employeeData = $activeCard ?: $matchedCard;
                } else {
                    $employeeData = $matchedCard;
                }
            }
        } catch (\Exception $e) {
            Log::warning('[PoolQccAdmin] Gagal query MSIDCARD: ' . $e->getMessage());
        }

        // 3. Fallback ke pkw_karyawan
        if (!$employeeData) {
            try {
                $localKaryawan = DB::table('pkw_karyawan')
                    ->where('rfid', $rawInput)
                    ->orWhereIn('nik', $candidates)
                    ->first();

                if ($localKaryawan) {
                    $employeeData = (object) [
                        'NIK' => $localKaryawan->nik ?? $localKaryawan->no_ktp ?? $rawInput,
                        'EMPNM' => $localKaryawan->nama_lengkap ?? 'Karyawan ' . $rawInput,
                        'DEPTID' => $localKaryawan->departemen ?? 'General',
                        'FOTOBLOB' => null,
                        'CARDNODEVICE' => null,
                        'RFID' => $rawInput,
                    ];
                }
            } catch (\Exception $e) {
                // ignore
            }
        }

        // 4. Fallback ke PoolParticipant jika pernah tap di booth
        if (!$employeeData) {
            $participant = PoolParticipant::where('rfid', $rawInput)
                ->orWhereIn('nik', $candidates)
                ->first();
            if ($participant) {
                $employeeData = (object) [
                    'NIK' => $participant->nik,
                    'EMPNM' => $participant->name,
                    'DEPTID' => $participant->department,
                    'FOTOBLOB' => null,
                    'CARDNODEVICE' => null,
                    'RFID' => $participant->rfid,
                    'photo_url' => $participant->photo,
                ];
            }
        }

        if (!$employeeData) {
            return response()->json([
                'status' => 'error',
                'message' => "Data karyawan dengan RFID/NIK '{$rawInput}' tidak ditemukan di database. Pastikan NIK atau kartu benar.",
            ], 404);
        }

        $photoBase64 = !empty($employeeData->FOTOBLOB)
            ? 'data:image/jpeg;base64,' . base64_encode($employeeData->FOTOBLOB)
            : ($employeeData->photo_url ?? null);

        $rfidToSave = !empty($employeeData->CARDNODEVICE)
            ? (string)$employeeData->CARDNODEVICE
            : (!empty($employeeData->RFID) ? $employeeData->RFID : $rawInput);

        return response()->json([
            'status' => 'success',
            'data' => [
                'nik' => $employeeData->NIK ?? $rawInput,
                'name' => $employeeData->EMPNM ?? 'Juri',
                'department' => $employeeData->DEPTID ?? 'Umum',
                'rfid' => $rfidToSave,
                'photo' => $photoBase64,
                'title' => 'Juri',
                'vote_weight' => 10,
            ],
        ]);
    }

    /**
     * Konfirmasi & Simpan Juri / Manajemen Baru
     */
    public function storeJudge(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|max:50',
            'name' => 'required|string|max:150',
            'department' => 'nullable|string|max:100',
            'title' => 'required|string|max:100',
            'vote_weight' => 'nullable|integer|min:1|max:100',
            'rfid' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $judge = PoolJudge::updateOrCreate(
            ['nik' => trim($request->nik)],
            [
                'rfid' => $request->rfid ? trim($request->rfid) : null,
                'name' => trim($request->name),
                'department' => $request->department ? trim($request->department) : 'Umum',
                'title' => $request->title ? trim($request->title) : 'Juri',
                'vote_weight' => $request->vote_weight ? (int)$request->vote_weight : 10,
                'photo' => $request->photo ?: null,
                'is_active' => true,
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => "{$judge->title} {$judge->name} berhasil didaftarkan (Bobot Vote: +{$judge->vote_weight} Poin)!",
            'data' => $judge,
        ]);
    }

    /**
     * Edit / Perbarui Data Juri / Manajemen
     */
    public function updateJudge(Request $request, $id)
    {
        $judge = PoolJudge::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:100',
            'vote_weight' => 'required|integer|min:1|max:100',
            'department' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
        ], [
            'title.required' => 'Gelar / Peranan wajib dipilih.',
            'vote_weight.required' => 'Bobot poin vote wajib diisi.',
            'vote_weight.integer' => 'Bobot poin harus berupa angka bulat.',
            'vote_weight.min' => 'Bobot poin vote minimal 1.',
            'vote_weight.max' => 'Bobot poin vote maksimal 100.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $judge->title = trim($request->title);
        $judge->vote_weight = (int)$request->vote_weight;
        if ($request->has('department') && !empty($request->department)) {
            $judge->department = trim($request->department);
        }
        if ($request->has('is_active')) {
            $judge->is_active = (bool)$request->is_active;
        }
        $judge->save();

        return response()->json([
            'status' => 'success',
            'message' => "Data {$judge->name} berhasil diperbarui (Gelar: {$judge->title}, Bobot: +{$judge->vote_weight} Poin)!",
            'data' => $judge,
        ]);
    }

    /**
     * Toggle Aktif/Nonaktif Juri
     */
    public function toggleJudgeStatus($id)
    {
        $judge = PoolJudge::findOrFail($id);
        $judge->is_active = !$judge->is_active;
        $judge->save();

        $statusStr = $judge->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return response()->json([
            'status' => 'success',
            'message' => "Dewan Juri {$judge->name} berhasil {$statusStr}.",
            'is_active' => (bool)$judge->is_active,
        ]);
    }

    /**
     * Hapus Dewan Juri
     */
    public function destroyJudge($id)
    {
        $judge = PoolJudge::findOrFail($id);
        $name = $judge->name;
        $judge->delete();

        return response()->json([
            'status' => 'success',
            'message' => "Dewan Juri {$name} berhasil dihapus dari sistem.",
        ]);
    }

    /**
     * Halaman Manajemen Kriteria Penilaian Booth Dewan Juri
     */
    public function criteria(Request $request)
    {
        return view('pool_qcc.admin.criteria');
    }

    /**
     * API Data Kriteria Penilaian Booth
     */
    public function getCriteriaData(Request $request)
    {
        $criteria = PoolEvaluationCriterion::orderBy('order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $totalWeight = $criteria->where('is_active', true)->sum('weight');

        return response()->json([
            'status' => 'success',
            'data' => $criteria,
            'meta' => [
                'total_criteria' => $criteria->count(),
                'active_criteria' => $criteria->where('is_active', true)->count(),
                'total_active_weight' => round($totalWeight, 2),
            ]
        ]);
    }

    /**
     * Simpan Kriteria Penilaian Baru
     */
    public function storeCriterion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'weight' => 'required|numeric|min:0|max:100',
            'max_score' => 'nullable|numeric|min:1|max:1000',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ], [
            'name.required' => 'Nama kriteria wajib diisi.',
            'weight.required' => 'Bobot kriteria wajib diisi.',
            'weight.numeric' => 'Bobot kriteria harus berupa angka.',
            'weight.min' => 'Bobot kriteria minimal 0%.',
            'weight.max' => 'Bobot kriteria maksimal 100%.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $criterion = PoolEvaluationCriterion::create([
            'name' => trim($request->name),
            'description' => $request->description ? trim($request->description) : null,
            'weight' => (float)$request->weight,
            'max_score' => $request->max_score ? (float)$request->max_score : 100.00,
            'order' => $request->has('order') && $request->order !== null ? (int)$request->order : ((int)PoolEvaluationCriterion::max('order') + 1),
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : true,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => "Kriteria '{$criterion->name}' (Bobot: {$criterion->weight}%) berhasil ditambahkan!",
            'data' => $criterion,
        ]);
    }

    /**
     * Update Kriteria Penilaian
     */
    public function updateCriterion(Request $request, $id)
    {
        $criterion = PoolEvaluationCriterion::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'weight' => 'required|numeric|min:0|max:100',
            'max_score' => 'nullable|numeric|min:1|max:1000',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ], [
            'name.required' => 'Nama kriteria wajib diisi.',
            'weight.required' => 'Bobot kriteria wajib diisi.',
            'weight.numeric' => 'Bobot kriteria harus berupa angka.',
            'weight.min' => 'Bobot kriteria minimal 0%.',
            'weight.max' => 'Bobot kriteria maksimal 100%.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $criterion->update([
            'name' => trim($request->name),
            'description' => $request->description ? trim($request->description) : null,
            'weight' => (float)$request->weight,
            'max_score' => $request->max_score ? (float)$request->max_score : 100.00,
            'order' => $request->has('order') && $request->order !== null ? (int)$request->order : $criterion->order,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : $criterion->is_active,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => "Kriteria '{$criterion->name}' berhasil diperbarui!",
            'data' => $criterion,
        ]);
    }

    /**
     * Toggle Status Aktif Kriteria
     */
    public function toggleCriterionStatus($id)
    {
        $criterion = PoolEvaluationCriterion::findOrFail($id);
        $criterion->is_active = !$criterion->is_active;
        $criterion->save();

        $statusStr = $criterion->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return response()->json([
            'status' => 'success',
            'message' => "Kriteria '{$criterion->name}' berhasil {$statusStr}.",
            'is_active' => (bool)$criterion->is_active,
        ]);
    }

    /**
     * Hapus Kriteria Penilaian
     */
    public function destroyCriterion($id)
    {
        $criterion = PoolEvaluationCriterion::findOrFail($id);
        $name = $criterion->name;
        $criterion->delete();

        return response()->json([
            'status' => 'success',
            'message' => "Kriteria '{$name}' berhasil dihapus dari sistem.",
        ]);
    }

    /**
     * Download Template Excel untuk Import Booth
     */
    public function downloadBoothTemplate()
    {
        return Excel::download(new TemplateBoothExport, 'template_import_booth_qcc.xlsx');
    }

    /**
     * Import Data Booth dari File Excel / CSV
     */
    public function importBooths(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'excel_file' => 'required|file|mimes:xlsx,xls,csv,txt|max:5120',
        ], [
            'excel_file.required' => 'File Excel wajib dipilih.',
            'excel_file.mimes' => 'Format file harus berupa .xlsx, .xls, atau .csv.',
            'excel_file.max' => 'Ukuran file maksimal adalah 5MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $sheets = Excel::toArray([], $request->file('excel_file'));
            if (empty($sheets) || empty($sheets[0])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'File Excel kosong atau tidak terbaca.',
                ], 422);
            }

            $rows = $sheets[0];
            // Baris 0 adalah Header, data dimulai dari baris index 1
            $createdCount = 0;
            $updatedCount = 0;
            $errors = [];

            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                if (empty($row)) continue;

                $code = isset($row[0]) ? trim((string)$row[0]) : '';
                $name = isset($row[1]) ? trim((string)$row[1]) : '';
                $category = isset($row[2]) ? trim((string)$row[2]) : '';
                $picName = isset($row[3]) ? trim((string)$row[3]) : '';
                $picContact = isset($row[4]) ? trim((string)$row[4]) : '';
                $location = isset($row[5]) ? trim((string)$row[5]) : '';
                $description = isset($row[6]) ? trim((string)$row[6]) : '';

                // Lewati baris kosong
                if (empty($name) && empty($code)) {
                    continue;
                }

                $rowNumber = $i + 1;

                if (empty($name)) {
                    $errors[] = "Baris {$rowNumber}: Nama booth / judul inovasi wajib diisi.";
                    continue;
                }

                if (empty($category)) {
                    $category = 'Produksi';
                }

                // Cek atau buat booth
                if (!empty($code)) {
                    $existing = PoolBooth::where('code', $code)->first();
                    if ($existing) {
                        $existing->update([
                            'name' => $name,
                            'category' => $category,
                            'pic_name' => $picName ?: $existing->pic_name,
                            'pic_contact' => $picContact ?: $existing->pic_contact,
                            'location' => $location ?: $existing->location,
                            'description' => $description ?: $existing->description,
                            'is_active' => true,
                        ]);
                        $updatedCount++;
                    } else {
                        PoolBooth::create([
                            'code' => strtoupper($code),
                            'name' => $name,
                            'category' => $category,
                            'pic_name' => $picName ?: null,
                            'pic_contact' => $picContact ?: null,
                            'location' => $location ?: null,
                            'description' => $description ?: null,
                            'is_active' => true,
                        ]);
                        $createdCount++;
                    }
                } else {
                    $autoCode = $this->generateBoothCode();
                    PoolBooth::create([
                        'code' => $autoCode,
                        'name' => $name,
                        'category' => $category,
                        'pic_name' => $picName ?: null,
                        'pic_contact' => $picContact ?: null,
                        'location' => $location ?: null,
                        'description' => $description ?: null,
                        'is_active' => true,
                    ]);
                    $createdCount++;
                }
            }

            $totalProcessed = $createdCount + $updatedCount;
            if ($totalProcessed === 0 && !empty($errors)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal memproses data: ' . implode(' ', $errors),
                    'errors' => $errors,
                ], 422);
            }

            $msg = "Import berhasil! {$totalProcessed} booth diproses ({$createdCount} booth baru, {$updatedCount} diperbarui).";
            if (!empty($errors)) {
                $msg .= " Catatan: " . count($errors) . " baris dilewati karena format tidak sesuai.";
            }

            return response()->json([
                'status' => 'success',
                'message' => $msg,
                'data' => [
                    'created' => $createdCount,
                    'updated' => $updatedCount,
                    'total' => $totalProcessed,
                    'errors' => $errors,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error import booths excel: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memproses file Excel: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download Template Excel untuk Import Pertanyaan Kuis
     */
    public function downloadQuestionTemplate()
    {
        return Excel::download(new TemplateQuestionExport, 'template_import_soal_qcc.xlsx');
    }

    /**
     * Import Data Pertanyaan Kuis dari File Excel / CSV
     */
    public function importQuestions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'excel_file' => 'required|file|mimes:xlsx,xls,csv,txt|max:5120',
        ], [
            'excel_file.required' => 'File Excel wajib dipilih.',
            'excel_file.mimes' => 'Format file harus berupa .xlsx, .xls, atau .csv.',
            'excel_file.max' => 'Ukuran file maksimal adalah 5MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $sheets = Excel::toArray([], $request->file('excel_file'));
            if (empty($sheets) || empty($sheets[0])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'File Excel kosong atau tidak terbaca.',
                ], 422);
            }

            $rows = $sheets[0];
            $createdCount = 0;
            $errors = [];

            // Pre-load booths by code and by name for high-performance lookup
            $boothsByCode = PoolBooth::all()->keyBy(function ($b) {
                return strtoupper(trim($b->code));
            });
            $boothsByName = PoolBooth::all()->keyBy(function ($b) {
                return strtolower(trim($b->name));
            });

            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                if (empty($row)) continue;

                $boothRef = isset($row[0]) ? trim((string)$row[0]) : '';
                $questionText = isset($row[1]) ? trim((string)$row[1]) : '';
                $optA = isset($row[2]) ? trim((string)$row[2]) : '';
                $optB = isset($row[3]) ? trim((string)$row[3]) : '';
                $optC = isset($row[4]) ? trim((string)$row[4]) : '';
                $optD = isset($row[5]) ? trim((string)$row[5]) : '';
                $optE = isset($row[6]) ? trim((string)$row[6]) : '';
                $correctAnswer = isset($row[7]) ? strtoupper(trim((string)$row[7])) : '';
                $points = isset($row[8]) && is_numeric($row[8]) ? (int)$row[8] : 1;
                $explanation = isset($row[9]) ? trim((string)$row[9]) : '';

                if (empty($boothRef) && empty($questionText)) {
                    continue;
                }

                $rowNumber = $i + 1;

                if (empty($boothRef)) {
                    $errors[] = "Baris {$rowNumber}: Kode booth wajib diisi.";
                    continue;
                }

                if (empty($questionText)) {
                    $errors[] = "Baris {$rowNumber}: Teks pertanyaan wajib diisi.";
                    continue;
                }

                if (empty($optA) || empty($optB) || empty($optC) || empty($optD)) {
                    $errors[] = "Baris {$rowNumber}: Pilihan jawaban A, B, C, dan D wajib diisi lengkap.";
                    continue;
                }

                if (!in_array($correctAnswer, ['A', 'B', 'C', 'D', 'E'])) {
                    $errors[] = "Baris {$rowNumber}: Kunci jawaban '{$correctAnswer}' tidak valid. Harus A, B, C, D, atau E.";
                    continue;
                }

                // Cari target booth
                $booth = $boothsByCode->get(strtoupper($boothRef));
                if (!$booth) {
                    $booth = $boothsByName->get(strtolower($boothRef));
                }

                if (!$booth) {
                    $errors[] = "Baris {$rowNumber}: Booth '{$boothRef}' tidak ditemukan di sistem.";
                    continue;
                }

                // Simpan pertanyaan
                PoolQuestion::create([
                    'booth_id' => $booth->id,
                    'question_text' => $questionText,
                    'option_a' => $optA,
                    'option_b' => $optB,
                    'option_c' => $optC,
                    'option_d' => $optD,
                    'option_e' => !empty($optE) ? $optE : null,
                    'correct_answer' => $correctAnswer,
                    'points' => $points > 0 ? $points : 1,
                    'explanation' => !empty($explanation) ? $explanation : null,
                    'is_active' => true,
                ]);

                $createdCount++;
            }

            if ($createdCount === 0 && !empty($errors)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal memproses soal: ' . implode(' ', array_slice($errors, 0, 3)),
                    'errors' => $errors,
                ], 422);
            }

            $msg = "Import berhasil! {$createdCount} pertanyaan kuis berhasil ditambahkan.";
            if (!empty($errors)) {
                $msg .= " Catatan: " . count($errors) . " baris dilewati karena ada data yang tidak valid.";
            }

            return response()->json([
                'status' => 'success',
                'message' => $msg,
                'data' => [
                    'created' => $createdCount,
                    'errors' => $errors,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error import questions excel: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memproses file Excel: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download Template Excel untuk Import Peserta
     */
    public function downloadParticipantTemplate()
    {
        return Excel::download(new TemplateParticipantExport, 'template_import_peserta_qcc.xlsx');
    }

    /**
     * Import Data Peserta dari File Excel / CSV
     */
    public function importParticipants(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'excel_file' => 'required|file|mimes:xlsx,xls,csv,txt|max:5120',
        ], [
            'excel_file.required' => 'File Excel wajib dipilih.',
            'excel_file.mimes' => 'Format file harus berupa .xlsx, .xls, atau .csv.',
            'excel_file.max' => 'Ukuran file maksimal adalah 5MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $sheets = Excel::toArray([], $request->file('excel_file'));
            if (empty($sheets) || empty($sheets[0])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'File Excel kosong atau tidak terbaca.',
                ], 422);
            }

            $rows = $sheets[0];
            $createdCount = 0;
            $updatedCount = 0;
            $errors = [];

            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                if (empty($row)) continue;

                $nik = isset($row[0]) ? trim((string)$row[0]) : '';
                $name = isset($row[1]) ? trim((string)$row[1]) : '';
                $dept = isset($row[2]) ? trim((string)$row[2]) : '';
                $rfid = isset($row[3]) ? trim((string)$row[3]) : '';

                // Lewati baris kosong
                if (empty($nik) && empty($name)) {
                    continue;
                }

                $rowNumber = $i + 1;

                if (empty($nik)) {
                    $errors[] = "Baris {$rowNumber}: NIK karyawan wajib diisi.";
                    continue;
                }

                if (empty($name)) {
                    $errors[] = "Baris {$rowNumber}: Nama karyawan wajib diisi.";
                    continue;
                }

                $department = !empty($dept) ? $dept : 'Umum';

                // Cari apakah peserta sudah ada berdasarkan NIK atau RFID
                $participant = PoolParticipant::where('nik', $nik)->first();
                if (!$participant && !empty($rfid)) {
                    $participant = PoolParticipant::where('rfid', $rfid)->first();
                }

                if ($participant) {
                    $updateData = [
                        'name' => $name,
                        'department' => $department,
                    ];
                    if (!empty($rfid)) {
                        $updateData['rfid'] = $rfid;
                    }
                    $participant->update($updateData);
                    $updatedCount++;
                } else {
                    PoolParticipant::create([
                        'nik' => $nik,
                        'name' => $name,
                        'department' => $department,
                        'rfid' => !empty($rfid) ? $rfid : null,
                        'photo' => null,
                        'total_score' => 0,
                        'booths_voted_count' => 0,
                        'last_activity_at' => null,
                    ]);
                    $createdCount++;
                }
            }

            $totalProcessed = $createdCount + $updatedCount;
            if ($totalProcessed === 0 && !empty($errors)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal memproses peserta: ' . implode(' ', array_slice($errors, 0, 3)),
                    'errors' => $errors,
                ], 422);
            }

            $msg = "Import berhasil! {$totalProcessed} data diproses ({$createdCount} peserta baru, {$updatedCount} diperbarui).";
            if (!empty($errors)) {
                $msg .= " Catatan: " . count($errors) . " baris dilewati karena ada data yang tidak valid.";
            }

            return response()->json([
                'status' => 'success',
                'message' => $msg,
                'data' => [
                    'created' => $createdCount,
                    'updated' => $updatedCount,
                    'total' => $totalProcessed,
                    'errors' => $errors,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error import participants excel: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memproses file Excel: ' . $e->getMessage(),
            ], 500);
        }
    }
}
