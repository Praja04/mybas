<?php

namespace App\Http\Controllers\PoolQcc;

use App\Http\Controllers\Controller;
use App\Models\PoolQCC\PoolBooth;
use App\Models\PoolQCC\PoolQuestion;
use App\Models\PoolQCC\PoolParticipant;
use App\Models\PoolQCC\PoolVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $totalBooths = PoolBooth::count();
        $totalQuestions = PoolQuestion::count();
        $totalParticipants = PoolParticipant::count();
        $totalVotes = PoolVote::count();
        $correctVotes = PoolVote::where('is_correct', true)->count();
        $accuracyRate = $totalVotes > 0 ? round(($correctVotes / $totalVotes) * 100, 1) : 0;

        // Leaderboard Booth berdasarkan total points
        $topBooths = PoolBooth::orderBy('total_points', 'desc')
            ->orderBy('total_voters', 'desc')
            ->take(5)
            ->get();

        // Leaderboard Peserta berdasarkan score tertinggi
        $topParticipants = PoolParticipant::orderBy('total_score', 'desc')
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        // 10 Aktivitas Vote Terbaru
        $recentVotes = PoolVote::with(['participant', 'booth', 'question'])
            ->latest()
            ->take(10)
            ->get();

        // Booth yang belum memenuhi minimal 5 soal
        $incompleteBooths = PoolBooth::withCount(['activeQuestions' => function ($q) {
            $q->where('is_active', true);
        }])
            ->having('active_questions_count', '<', 5)
            ->get();

        return view('pool_qcc.admin.dashboard', compact(
            'totalBooths',
            'totalQuestions',
            'totalParticipants',
            'totalVotes',
            'correctVotes',
            'accuracyRate',
            'topBooths',
            'topParticipants',
            'recentVotes',
            'incompleteBooths'
        ));
    }

    /**
     * Manajemen Booth
     */
    public function booths()
    {
        $booths = PoolBooth::withCount(['questions', 'activeQuestions'])
            ->orderBy('code', 'asc')
            ->get();

        $departments = self::$departments;
        $nextCode = $this->generateBoothCode();

        return view('pool_qcc.admin.booths', compact('booths', 'departments', 'nextCode'));
    }

    public function storeBooth(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|in:' . implode(',', self::$departments),
            'pic_name' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Auto generate kode booth dari backend
        $autoCode = $this->generateBoothCode();

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'booth_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/pool_qcc/booths'), $filename);
            $imagePath = 'uploads/pool_qcc/booths/' . $filename;
        }

        PoolBooth::create([
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

        return redirect()->back()->with('success', "Booth {$autoCode} berhasil ditambahkan! Silakan lengkapi minimal 5 pertanyaan untuk booth ini.");
    }

    public function updateBooth(Request $request, $id)
    {
        $booth = PoolBooth::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|in:' . implode(',', self::$departments),
            'pic_name' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

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

        return redirect()->back()->with('success', "Data Booth {$booth->code} berhasil diperbarui!");
    }

    public function destroyBooth($id)
    {
        $booth = PoolBooth::findOrFail($id);
        $booth->delete();

        return redirect()->back()->with('success', 'Booth beserta pertanyaan dan datanya berhasil dihapus.');
    }

    /**
     * Manajemen Pertanyaan
     */
    public function questions(Request $request)
    {
        $booths = PoolBooth::orderBy('name', 'asc')->get();
        $selectedBoothId = $request->get('booth_id');

        $query = PoolQuestion::with('booth')->latest();
        if ($selectedBoothId) {
            $query->where('booth_id', $selectedBoothId);
        }

        $questions = $query->paginate(15);

        return view('pool_qcc.admin.questions', compact('questions', 'booths', 'selectedBoothId'));
    }

    public function storeQuestion(Request $request)
    {
        $request->validate([
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
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'question_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/pool_qcc/questions'), $filename);
            $imagePath = 'uploads/pool_qcc/questions/' . $filename;
        }

        PoolQuestion::create([
            'booth_id' => $request->booth_id,
            'question_text' => $request->question_text,
            'image' => $imagePath,
            'option_a' => $request->option_a,
            'option_b' => $request->option_b,
            'option_c' => $request->option_c,
            'option_d' => $request->option_d,
            'option_e' => $request->option_e,
            'correct_answer' => strtoupper($request->correct_answer),
            'points' => $request->points ?? 1, // Default 1 Poin
            'explanation' => $request->explanation,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : true,
        ]);

        $booth = PoolBooth::find($request->booth_id);
        $totalActive = $booth->activeQuestions()->count();

        return redirect()->back()->with('success', "Pertanyaan berhasil ditambahkan! Booth '{$booth->name}' sekarang memiliki {$totalActive} pertanyaan aktif.");
    }

    public function updateQuestion(Request $request, $id)
    {
        $question = PoolQuestion::findOrFail($id);

        $request->validate([
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
        ]);

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

        return redirect()->back()->with('success', 'Pertanyaan berhasil diperbarui!');
    }

    public function destroyQuestion($id)
    {
        $question = PoolQuestion::findOrFail($id);
        $question->delete();

        return redirect()->back()->with('success', 'Pertanyaan berhasil dihapus.');
    }

    /**
     * Monitoring Peserta & Log Vote
     */
    public function participants()
    {
        $participants = PoolParticipant::with(['votes.booth'])
            ->latest('last_activity_at')
            ->paginate(20);

        return view('pool_qcc.admin.participants', compact('participants'));
    }

    public function votes(Request $request)
    {
        $query = PoolVote::with(['participant', 'booth', 'question'])->latest();

        if ($request->filled('booth_id')) {
            $query->where('booth_id', $request->booth_id);
        }
        if ($request->filled('status')) {
            $query->where('is_correct', $request->status == 'correct');
        }

        $votes = $query->paginate(25);
        $booths = PoolBooth::orderBy('name', 'asc')->get();

        return view('pool_qcc.admin.votes', compact('votes', 'booths'));
    }

    public function resetParticipantVotes($id)
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

        return redirect()->back()->with('success', "Riwayat vote peserta {$participant->name} berhasil di-reset!");
    }
}
