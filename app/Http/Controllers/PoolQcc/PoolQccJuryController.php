<?php

namespace App\Http\Controllers\PoolQcc;

use App\Http\Controllers\Controller;
use App\Models\PoolQCC\PoolBooth;
use App\Models\PoolQCC\PoolJudge;
use App\Models\PoolQCC\PoolJudgeEvaluation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class PoolQccJuryController extends Controller
{
    /**
     * Layar Penilaian Booth oleh Dewan Juri (Shell Blade Client-Side)
     * Alur: Pilih Booth -> Tap / Input NIK Juri -> Verifikasi Data Juri -> Munculkan Penilaian & Catatan
     */
    public function scoringView(Request $request)
    {
        return view('pool_qcc.jury.scoring');
    }

    /**
     * API Daftar Booth untuk Dipilih Juri
     */
    public function getBoothsList(Request $request)
    {
        // Hanya hitung juri resmi (gelar 'Juri') yang bertugas menilai booth (skor 0-100)
        $judges = PoolJudge::where('is_active', true)
            ->where(function ($q) {
                $q->where('title', 'Juri')
                    ->orWhere('title', 'like', '%juri%');
            })
            ->get();
        $totalActiveJudges = $judges->count();

        $booths = PoolBooth::where('is_active', true)
            ->with(['judgeEvaluations.judge'])
            ->orderBy('code', 'asc')
            ->get()
            ->map(function ($b) use ($totalActiveJudges) {
                $evals = $b->judgeEvaluations->filter(function ($e) {
                    return $e->judge && $e->judge->is_active;
                });

                return [
                    'id' => $b->id,
                    'code' => $b->code,
                    'name' => $b->name,
                    'category' => $b->category ?: 'Kaizen Circle',
                    'location' => $b->location,
                    'description' => $b->description,
                    'image' => $b->image,
                    'total_evaluations' => $evals->count(),
                    'total_active_judges' => $totalActiveJudges,
                    'avg_score' => $evals->count() > 0 ? round($evals->avg('score'), 2) : 0,
                    'evaluated_judges' => $evals->map(function ($e) {
                        return [
                            'judge_id' => $e->judge_id,
                            'judge_name' => $e->judge ? $e->judge->name : '-',
                            'score' => (float)$e->score,
                        ];
                    })->values(),
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => [
                'booths' => $booths,
                'total_booths' => $booths->count(),
                'total_active_judges' => $totalActiveJudges,
            ]
        ]);
    }

    /**
     * Verifikasi Tap RFID / NIK Juri untuk Booth Tertentu
     * Jika juri terdaftar, kembalikan profil juri dan nilai/catatan yang pernah diberikan (jika ada)
     */
    public function verifyJudgeAndGetEvaluation(Request $request)
    {
        $boothId = $request->input('booth_id');
        $rawInput = trim((string)($request->input('rfid_or_nik') ?: $request->input('input') ?: $request->input('nik') ?: $request->input('rfid')));

        if (!$boothId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Booth yang akan dinilai wajib dipilih terlebih dahulu.',
            ], 422);
        }

        if (empty($rawInput)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Silakan tap kartu RFID atau ketikkan NIK Dewan Juri.',
            ], 422);
        }

        $booth = PoolBooth::where('is_active', true)->find($boothId);
        if (!$booth) {
            return response()->json([
                'status' => 'error',
                'message' => 'Booth tidak ditemukan atau tidak aktif.',
            ], 404);
        }

        // Generate kandidat NIK (dengan variasi leading zero dsb)
        $numericInput = is_numeric($rawInput) ? (int)$rawInput : 0;
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

        // Cocokkan dengan data judge yang sudah didaftarkan di master pool_judges
        $judge = PoolJudge::where('is_active', true)
            ->where(function ($q) use ($rawInput, $candidates, $numericInput) {
                $q->where('rfid', $rawInput)
                    ->orWhereIn('nik', $candidates)
                    ->when($numericInput > 0, function ($sq) use ($numericInput) {
                        $sq->orWhere('rfid', (string)$numericInput);
                    });
            })
            ->first();

        if (!$judge) {
            return response()->json([
                'status' => 'error',
                'message' => "Kartu / NIK '{$rawInput}' tidak terdaftar di sistem. Pastikan kartu telah didaftarkan di Manajemen Juri & Manajemen.",
            ], 403);
        }

        // Validasi Gelar / Peran: Hanya yang bergelar 'Juri' yang berhak melakukan penilaian booth (skor 0-100)
        $judgeTitleLower = strtolower(trim((string)$judge->title));
        $isOfficialScorer = ($judgeTitleLower === 'juri' || $judgeTitleLower === 'dewan juri' || strpos($judgeTitleLower, 'juri') !== false);

        if (!$isOfficialScorer) {
            return response()->json([
                'status' => 'error',
                'message' => "Akses Ditolak: Hanya Dewan Juri (Gelar 'Juri') yang berhak memberikan penilaian booth. Gelar Anda terdaftar sebagai '{$judge->title}'. Anda tetap dapat memberikan vote kuis di booth.",
            ], 403);
        }

        // Cek apakah sudah pernah memberikan penilaian di booth ini
        $existingEval = PoolJudgeEvaluation::where('judge_id', $judge->id)
            ->where('booth_id', $booth->id)
            ->first();

        return response()->json([
            'status' => 'success',
            'message' => "Dewan Juri terverifikasi: {$judge->name}",
            'data' => [
                'judge' => [
                    'id' => $judge->id,
                    'name' => $judge->name,
                    'nik' => $judge->nik,
                    'department' => $judge->department,
                    'title' => $judge->title ?: 'Dewan Juri',
                    'photo' => $judge->photo,
                ],
                'booth' => [
                    'id' => $booth->id,
                    'code' => $booth->code,
                    'name' => $booth->name,
                    'category' => $booth->category ?: 'Kaizen Circle',
                    'location' => $booth->location,
                    'description' => $booth->description,
                ],
                'has_evaluated' => !is_null($existingEval),
                'existing_evaluation' => $existingEval ? [
                    'id' => $existingEval->id,
                    'score' => (float)$existingEval->score,
                    'notes' => $existingEval->notes,
                    'evaluated_at' => $existingEval->evaluated_at ? $existingEval->evaluated_at->format('H:i d/m/Y') : null,
                ] : null,
            ]
        ]);
    }

    /**
     * Simpan / Perbarui Nilai Booth oleh Juri (Maks 100)
     */
    public function saveScore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'judge_id' => 'required|exists:pool_judges,id',
            'booth_id' => 'required|exists:pool_booths,id',
            'score' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:1000',
        ], [
            'judge_id.required' => 'Identitas Juri wajib disertakan.',
            'booth_id.required' => 'Booth yang dinilai wajib dipilih.',
            'score.required' => 'Nilai skor wajib diisi.',
            'score.numeric' => 'Nilai skor harus berupa angka.',
            'score.min' => 'Nilai minimal adalah 0.',
            'score.max' => 'Nilai maksimal adalah 100.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $judge = PoolJudge::findOrFail($request->judge_id);

        $judgeTitleLower = strtolower(trim((string)$judge->title));
        $isOfficialScorer = ($judgeTitleLower === 'juri' || $judgeTitleLower === 'dewan juri' || strpos($judgeTitleLower, 'juri') !== false);
        if (!$isOfficialScorer) {
            return response()->json([
                'status' => 'error',
                'message' => "Akses Ditolak: Hanya Dewan Juri dengan gelar 'Juri' yang dapat menyimpan penilaian booth.",
            ], 403);
        }

        $booth = PoolBooth::findOrFail($request->booth_id);

        $evaluation = PoolJudgeEvaluation::updateOrCreate(
            [
                'judge_id' => $judge->id,
                'booth_id' => $booth->id,
            ],
            [
                'score' => round((float)$request->score, 2),
                'notes' => $request->notes ? trim($request->notes) : null,
                'evaluated_at' => Carbon::now(),
            ]
        );

        // Hitung ulang progress booth ini
        $boothEvaluationsCount = PoolJudgeEvaluation::where('booth_id', $booth->id)->count();

        return response()->json([
            'status' => 'success',
            'message' => "Nilai {$evaluation->score} untuk Booth {$booth->code} ({$booth->name}) oleh {$judge->name} berhasil disimpan!",
            'data' => [
                'evaluation_id' => $evaluation->id,
                'booth_id' => $booth->id,
                'judge_id' => $judge->id,
                'score' => (float)$evaluation->score,
                'notes' => $evaluation->notes,
                'booth_evaluations_count' => $boothEvaluationsCount,
            ],
        ]);
    }

    /**
     * Halaman Rekapitulasi & Pemenang Penilaian Dewan Juri (Shell Blade Client-Side)
     */
    public function recapView(Request $request)
    {
        return view('pool_qcc.jury.recap');
    }

    /**
     * API Data Rekapitulasi untuk Real-Time Refresh & Client-Side Rendering
     */
    public function getRecapData(Request $request)
    {
        $recap = $this->calculateJuryRecap();
        return response()->json([
            'status' => 'success',
            'data' => $recap,
        ]);
    }

    /**
     * Kalkulasi Peringkat Pemenang Penilaian Dewan Juri
     */
    private function calculateJuryRecap()
    {
        // Hanya hitung dewan juri aktif yang memiliki peran 'Juri'
        $judges = PoolJudge::where('is_active', true)
            ->where(function ($q) {
                $q->where('title', 'Juri')
                    ->orWhere('title', 'like', '%juri%');
            })
            ->get();
        $totalActiveJudges = $judges->count();

        $booths = PoolBooth::where('is_active', true)
            ->with(['judgeEvaluations.judge'])
            ->get();

        $rankedBooths = $booths->map(function ($booth) use ($totalActiveJudges) {
            $evaluations = $booth->judgeEvaluations->filter(function ($eval) {
                return $eval->judge && $eval->judge->is_active;
            });

            $evalCount = $evaluations->count();
            $avgScore = $evalCount > 0 ? round($evaluations->avg('score'), 2) : 0.00;
            $maxScore = $evalCount > 0 ? (float)$evaluations->max('score') : 0.00;
            $minScore = $evalCount > 0 ? (float)$evaluations->min('score') : 0.00;

            $evaluationsList = $evaluations->map(function ($eval) {
                return [
                    'judge_id' => $eval->judge_id,
                    'judge_name' => $eval->judge ? $eval->judge->name : '-',
                    'judge_dept' => $eval->judge ? $eval->judge->department : '-',
                    'score' => (float)$eval->score,
                    'notes' => $eval->notes,
                    'evaluated_at' => $eval->evaluated_at ? $eval->evaluated_at->format('H:i d/m/Y') : '-',
                ];
            })->values();

            return [
                'id' => $booth->id,
                'code' => $booth->code,
                'name' => $booth->name,
                'category' => $booth->category,
                'location' => $booth->location,
                'image' => $booth->image,
                'avg_score' => $avgScore,
                'max_score' => $maxScore,
                'min_score' => $minScore,
                'judges_evaluated_count' => $evalCount,
                'total_active_judges' => $totalActiveJudges,
                'completion_pct' => $totalActiveJudges > 0 ? round(($evalCount / $totalActiveJudges) * 100) : 0,
                'evaluations' => $evaluationsList,
            ];
        })
        ->sort(function ($a, $b) {
            // Urutkan berdasarkan rata-rata skor tertinggi (DESC)
            if ($b['avg_score'] != $a['avg_score']) {
                return $b['avg_score'] <=> $a['avg_score'];
            }
            // Jika skor sama, urutkan berdasarkan jumlah juri terbanyak (DESC)
            if ($b['judges_evaluated_count'] != $a['judges_evaluated_count']) {
                return $b['judges_evaluated_count'] <=> $a['judges_evaluated_count'];
            }
            // Fallback kode booth (ASC)
            return strcmp($a['code'], $b['code']);
        })
        ->values();

        // Tandai Pemenang:
        // Rank 1: The Best Booth
        // Rank 2: Runner Up
        $theBestBooth = $rankedBooths->count() > 0 && $rankedBooths[0]['avg_score'] > 0 ? $rankedBooths[0] : null;
        $runnerUp = $rankedBooths->count() > 1 && $rankedBooths[1]['avg_score'] > 0 ? $rankedBooths[1] : null;

        return [
            'booths' => $rankedBooths,
            'the_best_booth' => $theBestBooth,
            'runner_up' => $runnerUp,
            'total_active_judges' => $totalActiveJudges,
            'totalActiveJudges' => $totalActiveJudges,
            'judges' => $judges->map(function ($j) {
                return [
                    'id' => $j->id,
                    'name' => $j->name,
                    'department' => $j->department,
                    'title' => $j->title,
                ];
            }),
            'last_updated' => Carbon::now()->format('H:i:s d/m/Y'),
        ];
    }
}
