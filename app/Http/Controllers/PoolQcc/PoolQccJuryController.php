<?php

namespace App\Http\Controllers\PoolQcc;

use App\Http\Controllers\Controller;
use App\Models\PoolQCC\PoolBooth;
use App\Models\PoolQCC\PoolJudge;
use App\Models\PoolQCC\PoolJudgeEvaluation;
use App\Models\PoolQCC\PoolEvaluationCriterion;
use App\Models\PoolQCC\PoolJudgeEvaluationDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class PoolQccJuryController extends Controller
{
    /**
     * Helper untuk mengambil data Dewan Juri yang sedang login (berdasarkan user_id atau NIK/username)
     */
    private function getLoggedInJudge()
    {
        $user = Auth::user();
        if (!$user) {
            return null;
        }

        // 1. Cek relasi langsung user_id
        $judge = PoolJudge::where('is_active', true)->where('user_id', $user->id)->first();
        if ($judge) {
            return $judge;
        }

        // 2. Fallback pencocokan NIK berdasarkan username akun login
        $username = trim((string)$user->username);
        if ($username !== '') {
            $stripped = ltrim($username, '0');
            $candidates = array_values(array_unique(array_filter([
                $username,
                $stripped,
                '0' . $stripped,
                str_pad($stripped, 8, '0', STR_PAD_LEFT),
                str_pad($stripped, 9, '0', STR_PAD_LEFT),
            ])));

            $judge = PoolJudge::where('is_active', true)
                ->whereIn('nik', $candidates)
                ->first();

            // Auto-link user_id jika cocok agar seterusnya tersambung
            if ($judge && !$judge->user_id) {
                $judge->user_id = $user->id;
                $judge->save();
            }
            return $judge;
        }

        return null;
    }

    /**
     * Layar Penilaian Booth oleh Dewan Juri (Shell Blade Client-Side)
     * Alur: Pilih Booth -> Tap / Input NIK Juri -> Verifikasi Data Juri -> Munculkan Penilaian & Catatan
     */
    public function scoringView(Request $request)
    {
        $loggedInJudge = $this->getLoggedInJudge();
        $isJudgeAccount = !is_null($loggedInJudge);

        $evaluatedBoothIds = [];
        $totalBoothsCount = PoolBooth::where('is_active', true)->count();
        if ($loggedInJudge) {
            $evaluatedBoothIds = PoolJudgeEvaluation::where('judge_id', $loggedInJudge->id)
                ->pluck('booth_id')
                ->toArray();
        }

        return view('pool_qcc.jury.scoring', compact('loggedInJudge', 'isJudgeAccount', 'evaluatedBoothIds', 'totalBoothsCount'));
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
        $loggedInJudge = $this->getLoggedInJudge();

        $booths = PoolBooth::where('is_active', true)
            ->with(['judgeEvaluations.judge'])
            ->orderBy('code', 'asc')
            ->get()
            ->map(function ($b) use ($totalActiveJudges, $loggedInJudge) {
                $evals = $b->judgeEvaluations->filter(function ($e) {
                    return $e->judge && $e->judge->is_active;
                });

                $myEval = $loggedInJudge ? $evals->firstWhere('judge_id', $loggedInJudge->id) : null;

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
                    'my_evaluation' => $myEval ? [
                        'score' => (float)$myEval->score,
                        'evaluated_at' => $myEval->evaluated_at ? $myEval->evaluated_at->format('H:i d/m/Y') : null,
                    ] : null,
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => [
                'booths' => $booths,
                'total_booths' => $booths->count(),
                'total_active_judges' => $totalActiveJudges,
                'is_judge_account' => !is_null($loggedInJudge),
                'logged_in_judge' => $loggedInJudge ? [
                    'id' => $loggedInJudge->id,
                    'name' => $loggedInJudge->name,
                    'title' => $loggedInJudge->title ?: 'Dewan Juri',
                ] : null,
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
        $requestedJudgeId = $request->input('judge_id');

        if (!$boothId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Booth yang akan dinilai wajib dipilih terlebih dahulu.',
            ], 422);
        }

        $booth = PoolBooth::where('is_active', true)->find($boothId);
        if (!$booth) {
            return response()->json([
                'status' => 'error',
                'message' => 'Booth tidak ditemukan atau tidak aktif.',
            ], 404);
        }

        $loggedInJudge = $this->getLoggedInJudge();
        $judge = null;

        // 1. Jika juri sedang login akun sendiri, atau judge_id disertakan langsung
        if ($requestedJudgeId) {
            $judge = PoolJudge::where('is_active', true)->find($requestedJudgeId);
        } elseif ($loggedInJudge) {
            $judge = $loggedInJudge;
        } else {
            // 2. Mode Kiosk/Admin Kiosk: Wajib tap RFID atau input NIK
            if (empty($rawInput)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Silakan tap kartu RFID atau ketikkan NIK Dewan Juri.',
                ], 422);
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
        }

        if (!$judge) {
            return response()->json([
                'status' => 'error',
                'message' => $rawInput ? "Kartu / NIK '{$rawInput}' tidak terdaftar di sistem. Pastikan kartu telah didaftarkan di Manajemen Juri & Manajemen." : "Data Dewan Juri tidak ditemukan.",
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
            ->with(['details'])
            ->first();

        // Ambil kriteria penilaian aktif
        $activeCriteria = PoolEvaluationCriterion::where('is_active', true)
            ->orderBy('order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $existingDetails = $existingEval && $existingEval->details ? $existingEval->details->keyBy('criterion_id') : collect([]);
        $existingScoresMap = [];
        if ($existingEval && !empty($existingEval->criteria_scores) && is_array($existingEval->criteria_scores)) {
            foreach ($existingEval->criteria_scores as $cs) {
                if (isset($cs['criterion_id'])) {
                    $existingScoresMap[$cs['criterion_id']] = (float)$cs['score'];
                }
            }
        }

        $criteriaData = $activeCriteria->map(function ($c) use ($existingDetails, $existingScoresMap) {
            $savedScore = null;
            if ($existingDetails->has($c->id)) {
                $savedScore = (float)$existingDetails->get($c->id)->score;
            } elseif (isset($existingScoresMap[$c->id])) {
                $savedScore = (float)$existingScoresMap[$c->id];
            }

            return [
                'id' => $c->id,
                'name' => $c->name,
                'description' => $c->description,
                'weight' => (float)$c->weight,
                'max_score' => (float)$c->max_score,
                'order' => (int)$c->order,
                'saved_score' => $savedScore,
            ];
        });

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
                'criteria' => $criteriaData,
                'has_evaluated' => !is_null($existingEval),
                'existing_evaluation' => $existingEval ? [
                    'id' => $existingEval->id,
                    'score' => (float)$existingEval->score,
                    'criteria_scores' => $existingEval->criteria_scores,
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
        $loggedInJudge = $this->getLoggedInJudge();

        $validator = Validator::make($request->all(), [
            'judge_id' => $loggedInJudge ? 'nullable' : 'required|exists:pool_judges,id',
            'booth_id' => 'required|exists:pool_booths,id',
            'score' => 'nullable|numeric|min:1|max:100',
            'criteria_scores' => 'nullable|array',
            'notes' => 'nullable|string|max:1000',
        ], [
            'judge_id.required' => 'Identitas Juri wajib disertakan.',
            'booth_id.required' => 'Booth yang dinilai wajib dipilih.',
            'score.numeric' => 'Nilai skor harus berupa angka.',
            'score.min' => 'Nilai minimal adalah 1.',
            'score.max' => 'Nilai maksimal adalah 100.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $judge = $loggedInJudge ?: PoolJudge::findOrFail($request->judge_id);

        $judgeTitleLower = strtolower(trim((string)$judge->title));
        $isOfficialScorer = ($judgeTitleLower === 'juri' || $judgeTitleLower === 'dewan juri' || strpos($judgeTitleLower, 'juri') !== false);
        if (!$isOfficialScorer) {
            return response()->json([
                'status' => 'error',
                'message' => "Akses Ditolak: Hanya Dewan Juri dengan gelar 'Juri' yang dapat menyimpan penilaian booth.",
            ], 403);
        }

        $booth = PoolBooth::findOrFail($request->booth_id);

        $activeCriteria = PoolEvaluationCriterion::where('is_active', true)->get()->keyBy('id');
        $criteriaInput = $request->input('criteria_scores', []);

        $finalScore = 0;
        $criteriaSnapshot = [];

        if (!empty($criteriaInput) && is_array($criteriaInput)) {
            $totalWeightedScore = 0;
            $totalWeight = 0;

            foreach ($criteriaInput as $critId => $critVal) {
                $criterion = $activeCriteria->get($critId);
                if (!$criterion) continue;

                $val = max(1, min((float)$critVal, (float)$criterion->max_score));
                $weight = (float)$criterion->weight;

                $scorePct = $criterion->max_score > 0 ? ($val / $criterion->max_score) * 100 : 0;
                $weightedContrib = ($scorePct * ($weight / 100));

                $totalWeightedScore += $weightedContrib;
                $totalWeight += $weight;

                $criteriaSnapshot[] = [
                    'criterion_id' => $criterion->id,
                    'name' => $criterion->name,
                    'description' => $criterion->description,
                    'score' => round($val, 2),
                    'max_score' => (float)$criterion->max_score,
                    'weight' => $weight,
                    'weighted_score' => round($weightedContrib, 2),
                ];
            }

            if ($totalWeight > 0 && abs($totalWeight - 100) > 0.01) {
                $finalScore = ($totalWeightedScore / $totalWeight) * 100;
            } else {
                $finalScore = $totalWeightedScore;
            }
            $finalScore = round(max(1, min(100, $finalScore)), 2);
        } else {
            $finalScore = round(max(1, min(100, (float)$request->input('score', 1))), 2);
        }

        $evaluation = PoolJudgeEvaluation::updateOrCreate(
            [
                'judge_id' => $judge->id,
                'booth_id' => $booth->id,
            ],
            [
                'score' => $finalScore,
                'criteria_scores' => !empty($criteriaSnapshot) ? $criteriaSnapshot : null,
                'notes' => $request->notes ? trim($request->notes) : null,
                'evaluated_at' => Carbon::now(),
            ]
        );

        // Simpan / perbarui detail per kriteria
        if (!empty($criteriaSnapshot)) {
            foreach ($criteriaSnapshot as $snap) {
                PoolJudgeEvaluationDetail::updateOrCreate(
                    [
                        'evaluation_id' => $evaluation->id,
                        'criterion_id' => $snap['criterion_id'],
                    ],
                    [
                        'score' => $snap['score'],
                        'weight' => $snap['weight'],
                    ]
                );
            }
        }

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
                'criteria_scores' => $evaluation->criteria_scores,
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
                    'id' => $eval->id,
                    'judge_id' => $eval->judge_id,
                    'judge_name' => $eval->judge ? $eval->judge->name : '-',
                    'judge_dept' => $eval->judge ? $eval->judge->department : '-',
                    'score' => (float)$eval->score,
                    'criteria_scores' => $eval->criteria_scores,
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

    /**
     * Helper validasi hak akses admin (permission qcc_pool_admin)
     */
    private function authorizeAdminPermission()
    {
        $user = Auth::user();
        if (!$user) {
            abort(401, 'Unauthenticated.');
        }

        $permissions = [];
        if ($user->auth_group_id) {
            $group = \App\AuthGroup::find($user->auth_group_id);
            if ($group) {
                $permissions = $group->permissions()->pluck('codename')->toArray();
            }
        }

        if (method_exists($user, 'getAllPermissionCodenames')) {
            $permissions = array_values(array_unique(array_merge(
                $permissions,
                $user->getAllPermissionCodenames()
            )));
        }

        if (!in_array('qcc_pool_admin', $permissions)) {
            abort(403, 'Akses Ditolak: Anda memerlukan permission qcc_pool_admin untuk mereset penilaian juri.');
        }
    }

    /**
     * Reset / Hapus Penilaian Juri Tertentu
     * Permission: qcc_pool_admin
     */
    public function resetEvaluation(Request $request, $id)
    {
        $this->authorizeAdminPermission();

        $evaluation = PoolJudgeEvaluation::with(['judge', 'booth'])->find($id);
        if (!$evaluation) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data penilaian juri tidak ditemukan atau sudah direset sebelumnya.',
            ], 404);
        }

        $judgeName = $evaluation->judge ? $evaluation->judge->name : 'Juri';
        $boothName = $evaluation->booth ? ($evaluation->booth->code . ' - ' . $evaluation->booth->name) : 'Booth';

        DB::transaction(function () use ($evaluation) {
            $evaluation->details()->delete();
            $evaluation->delete();
        });

        return response()->json([
            'status' => 'success',
            'message' => "Nilai dari {$judgeName} untuk {$boothName} berhasil direset.",
        ]);
    }

    /**
     * Reset / Hapus Seluruh Penilaian Juri pada Booth Tertentu
     * Permission: qcc_pool_admin
     */
    public function resetBoothEvaluations(Request $request, $boothId)
    {
        $this->authorizeAdminPermission();

        $booth = PoolBooth::find($boothId);
        if (!$booth) {
            return response()->json([
                'status' => 'error',
                'message' => 'Booth tidak ditemukan.',
            ], 404);
        }

        $evaluations = PoolJudgeEvaluation::where('booth_id', $booth->id)->get();
        $count = $evaluations->count();

        DB::transaction(function () use ($evaluations) {
            foreach ($evaluations as $eval) {
                $eval->details()->delete();
                $eval->delete();
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => "Seluruh penilaian juri ({$count} penilaian) untuk Booth {$booth->code} ({$booth->name}) berhasil direset.",
        ]);
    }

    /**
     * Reset / Hapus Seluruh Penilaian Dewan Juri Secara Keseluruhan (Global Reset)
     * Permission: qcc_pool_admin
     */
    public function resetAllEvaluations(Request $request)
    {
        $this->authorizeAdminPermission();

        $count = PoolJudgeEvaluation::count();

        DB::transaction(function () {
            PoolJudgeEvaluationDetail::query()->delete();
            PoolJudgeEvaluation::query()->delete();
        });

        return response()->json([
            'status' => 'success',
            'message' => "Seluruh data penilaian juri ({$count} penilaian) berhasil direset.",
        ]);
    }
}
