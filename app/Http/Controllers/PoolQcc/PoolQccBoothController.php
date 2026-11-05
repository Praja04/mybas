<?php

namespace App\Http\Controllers\PoolQcc;

use App\Http\Controllers\Controller;
use App\Models\PoolQCC\PoolBooth;
use App\Models\PoolQCC\PoolQuestion;
use App\Models\PoolQCC\PoolParticipant;
use App\Models\PoolQCC\PoolVote;
use App\Models\PoolQCC\PoolJudge;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class PoolQccBoothController extends Controller
{
    /**
     * Halaman Daftar Booth untuk Peserta / Panitia
     * Menampilkan seluruh booth untuk dipilih layar booth mana yang ingin dibuka
     */
    public function index()
    {
        $booths = PoolBooth::where('is_active', true)
            ->withCount(['activeQuestions' => function ($q) {
                $q->where('is_active', true);
            }])
            ->orderBy('code', 'asc')
            ->get();

        return view('pool_qcc.booth.index', compact('booths'));
    }

    /**
     * Halaman Layar Tap Standby khusus untuk Booth Tertentu
     * Setiap booth memiliki layar stand sendiri yang standby menunggu peserta tap
     */
    public function boothStation($boothId)
    {
        $booth = PoolBooth::withCount(['activeQuestions' => function ($q) {
            $q->where('is_active', true);
        }])->findOrFail($boothId);

        return view('pool_qcc.booth.station', compact('booth'));
    }

    /**
     * Endpoint API / AJAX untuk Scan Tapping RFID Card di Booth Tertentu
     */
    public function scanRfidAtBooth(Request $request, $boothId)
    {
        $validator = Validator::make($request->all(), [
            'rfid' => 'nullable|string',
            'nik' => 'nullable|string',
            'name' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $booth = PoolBooth::findOrFail($boothId);

        // 1. Validasi kelengkapan soal booth
        $activeQuestionsCount = $booth->activeQuestions()->count();
        if ($activeQuestionsCount < 5) {
            return response()->json([
                'status' => 'error',
                'message' => "Booth '{$booth->name}' belum memiliki minimal 5 soal aktif (saat ini: {$activeQuestionsCount} soal). Silakan hubungi admin booth.",
            ], 422);
        }

        $rawInput = trim((string)($request->rfid ?: $request->nik));
        $isManual = (bool)($request->is_manual || $request->filled('name'));
        $manualRfid = trim((string)$request->rfid);
        $manualNik = trim((string)($request->nik ?: $request->rfid));
        $manualName = trim((string)$request->name);

        if ($isManual) {
            if (empty($manualNik)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Nomor NIK / Identitas peserta wajib diisi.',
                ], 422);
            }
            if (empty($manualName)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Nama lengkap peserta wajib diisi untuk input manual.',
                ], 422);
            }
        } else {
            if (empty($rawInput)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Silakan tap kartu RFID atau ketik NIK peserta.',
                ], 422);
            }
        }

        $numericInput = is_numeric($rawInput) ? (int)$rawInput : 0;

        // Generate variasi NIK (menangani cetakan ID card tanpa nol depan, misal 18000275 vs 018000275)
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

        // 2. Cari / Identifikasi Peserta yang sudah pernah tercatat di Pool QCC
        $participant = PoolParticipant::where('rfid', $rawInput)
            ->orWhereIn('nik', $candidates)
            ->when($numericInput > 0, function ($q) use ($numericInput) {
                $q->orWhere('rfid', (string)$numericInput);
            })
            ->first();

        if ($isManual) {
            // Mode Input Manual: Simpan HANYA di database lokal PoolParticipant (jangan ke DB pusat 192.168)
            // Dept default 'Peserta QCC'
            $rfidToSave = !empty($manualRfid) ? $manualRfid : $manualNik;

            if (!$participant) {
                $participant = PoolParticipant::where('nik', $manualNik)
                    ->orWhere('rfid', $rfidToSave)
                    ->first();
            }

            if (!$participant) {
                $participant = PoolParticipant::create([
                    'rfid' => $rfidToSave,
                    'nik' => $manualNik,
                    'name' => $manualName,
                    'department' => 'Peserta QCC',
                    'photo' => null,
                    'total_score' => 0,
                    'booths_voted_count' => 0,
                    'last_activity_at' => Carbon::now(),
                ]);
            } else {
                $updateData = [
                    'name' => $manualName ?: $participant->name,
                    'department' => $participant->department ?: 'Peserta QCC',
                    'last_activity_at' => Carbon::now(),
                ];
                if (!empty($manualRfid)) {
                    $updateData['rfid'] = $manualRfid;
                }
                $participant->update($updateData);
            }
        } else {
            // Mode Scanner Normal: Cek ke DB Karyawan jika belum ada di tabel lokal pool_participants
            if (!$participant) {
                $employeeData = null;

                // 2a. Cari di MSIDCARD (192.168.178.44-admin)
                // Query kartu berdasarkan nomor RFID / Device No / NIK / Barcode (termasuk variasi leading-zero)
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
                        // Jika ada NIK, utamakan profil ter-update dari kartu aktif (STATUS = 'X')
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
                    Log::warning('[PoolQcc] Gagal query MSIDCARD 192.168.178.44-admin: ' . $e->getMessage());
                }

                // 2b. Fallback ke tabel karyawan lokal jika ada
                if (!$employeeData) {
                    try {
                        $localKaryawan = DB::table('hr_karyawan')
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

                // Jika tidak ditemukan di database manapun, TOLAK (beri flag not_registered agar frontend bisa confirm input manual)
                if (!$employeeData) {
                    return response()->json([
                        'status' => 'error',
                        'not_registered' => true,
                        'rfid' => $rawInput,
                        'message' => "Nomor RFID / NIK '{$rawInput}' tidak terdaftar di database pusat.",
                    ], 404);
                }

                // 2c. Cek kembali ke pool_participants berdasarkan NIK
                // (Mencegah multi-akun jika karyawan tap dengan variasi NIK atau kartu berbeda)
                if (!empty($employeeData->NIK)) {
                    $participant = PoolParticipant::where('nik', $employeeData->NIK)
                        ->orWhereIn('nik', $candidates)
                        ->first();
                }

                if (!$participant) {
                    $photoBase64 = !empty($employeeData->FOTOBLOB) ? 'data:image/jpeg;base64,' . base64_encode($employeeData->FOTOBLOB) : null;
                    $rfidToSave = !empty($employeeData->CARDNODEVICE) ? (string)$employeeData->CARDNODEVICE : (!empty($employeeData->RFID) ? $employeeData->RFID : $rawInput);

                    $participant = PoolParticipant::create([
                        'rfid' => $rfidToSave,
                        'nik' => $employeeData->NIK ?? $rawInput,
                        'name' => $employeeData->EMPNM ?? ('Peserta ' . substr($rawInput, -4)),
                        'department' => $employeeData->DEPTID ?? 'Umum',
                        'photo' => $photoBase64,
                        'total_score' => 0,
                        'booths_voted_count' => 0,
                        'last_activity_at' => Carbon::now(),
                    ]);
                } else {
                    $participant->update([
                        'last_activity_at' => Carbon::now(),
                    ]);
                }
            } else {
                $participant->update([
                    'last_activity_at' => Carbon::now(),
                ]);
            }
        }

        // 3. Validasi: Apakah sudah pernah vote di booth ini?
        if ($participant->hasVotedBooth($booth->id)) {
            return response()->json([
                'status' => 'error',
                'message' => "Mohon maaf {$participant->name}, Anda sudah pernah melakukan vote/pooling di Booth {$booth->code} ini!",
            ], 422);
        }

        // 4. Validasi: Apakah sudah mencapai maksimal 10 booth?
        if ($participant->booths_voted_count >= 10) {
            return response()->json([
                'status' => 'error',
                'message' => "Mohon maaf {$participant->name}, kuota pooling Anda sudah habis (Maksimal 10 Booth). Terima kasih atas partisipasi Anda!",
            ], 422);
        }

        // Cek apakah peserta terdaftar sebagai Dewan Juri / Manajemen (termasuk variasi leading zero NIK / RFID)
        $strippedNik = ltrim((string)$participant->nik, '0');
        $judgeCandidates = array_values(array_unique(array_filter([
            (string)$participant->nik,
            $strippedNik,
            '0' . $strippedNik,
            str_pad($strippedNik, 8, '0', STR_PAD_LEFT),
            str_pad($strippedNik, 9, '0', STR_PAD_LEFT),
            (string)$participant->rfid,
            ltrim((string)$participant->rfid, '0'),
        ])));

        $matchedJudge = PoolJudge::where('is_active', true)
            ->where(function ($q) use ($judgeCandidates) {
                $q->whereIn('nik', $judgeCandidates)
                    ->orWhereIn('rfid', $judgeCandidates);
            })
            ->first();

        $isJudge = !is_null($matchedJudge);
        $judgeTitle = $isJudge ? ($matchedJudge->title ?: 'Dewan Juri') : null;
        $judgeVoteWeight = $isJudge ? (int)($matchedJudge->vote_weight > 0 ? $matchedJudge->vote_weight : 10) : 1;

        // Simpan session kuis aktif untuk peserta di booth ini
        Session::put('challenge_participant_id', $participant->id);
        Session::put('challenge_booth_id', $booth->id);
        Session::put('challenge_is_judge', $isJudge);
        Session::put('challenge_judge_title', $judgeTitle);
        Session::put('challenge_judge_weight', $judgeVoteWeight);

        $welcomeMsg = $isJudge
            ? "{$judgeTitle} terverifikasi! Selamat datang, {$participant->name} (Vote bernilai +{$judgeVoteWeight} Poin)"
            : "Kartu terverifikasi! Selamat datang, {$participant->name} (Booth ke-" . ($participant->booths_voted_count + 1) . " dari 10)";

        return response()->json([
            'status' => 'success',
            'message' => $welcomeMsg,
            'is_judge' => $isJudge,
            'judge_title' => $judgeTitle,
            'judge_vote_weight' => $judgeVoteWeight,
            'participant' => [
                'name' => $participant->name,
                'nik' => $participant->nik,
                'department' => $participant->department,
                'booths_voted_count' => $participant->booths_voted_count,
            ],
            'redirect_url' => route('pool-qcc.booth-challenge', $booth->id),
        ]);
    }

    /**
     * Halaman Kuis Soal Acak untuk Booth Tertentu
     */
    public function challengeScreen($boothId)
    {
        $booth = PoolBooth::findOrFail($boothId);
        $participantId = Session::get('challenge_participant_id');
        $sessionBoothId = Session::get('challenge_booth_id');

        if (!$participantId || $sessionBoothId != $booth->id) {
            return redirect()->route('pool-qcc.booth-station', $booth->id)
                ->with('warning', 'Silakan tempelkan ID Card Anda terlebih dahulu.');
        }

        $participant = PoolParticipant::findOrFail($participantId);

        // Ambil 1 soal acak aktif dari booth ini
        $question = PoolQuestion::where('booth_id', $booth->id)
            ->where('is_active', true)
            ->inRandomOrder()
            ->first();

        if (!$question) {
            return redirect()->route('pool-qcc.booth-station', $booth->id)
                ->with('error', 'Belum ada pertanyaan aktif di booth ini.');
        }

        // Kumpulkan pilihan jawaban dan acak (shuffle) posisinya
        $rawOptions = [
            'A' => $question->option_a,
            'B' => $question->option_b,
            'C' => $question->option_c,
            'D' => $question->option_d,
        ];
        if (!empty($question->option_e)) {
            $rawOptions['E'] = $question->option_e;
        }

        $optionList = [];
        foreach ($rawOptions as $origKey => $text) {
            if ($text !== null && trim($text) !== '') {
                $optionList[] = [
                    'original_key' => $origKey,
                    'text' => $text,
                ];
            }
        }

        // Acak posisi pilihan jawaban
        shuffle($optionList);

        $labels = ['A', 'B', 'C', 'D', 'E'];
        $shuffledOptions = [];
        $displayToOrigMap = [];
        $origToDisplayMap = [];

        foreach ($optionList as $index => $opt) {
            $displayLabel = $labels[$index];
            $shuffledOptions[] = [
                'label' => $displayLabel,
                'original_key' => $opt['original_key'],
                'text' => $opt['text'],
            ];
            $displayToOrigMap[$displayLabel] = $opt['original_key'];
            $origToDisplayMap[$opt['original_key']] = $displayLabel;
        }

        // Simpan mapping acak ke session peserta untuk verifikasi saat submit jawaban
        Session::put('challenge_question_id', $question->id);
        Session::put('challenge_options_map', $displayToOrigMap);
        Session::put('challenge_orig_to_display_map', $origToDisplayMap);

        $isJudge = (bool) Session::get('challenge_is_judge', false);
        $judgeTitle = Session::get('challenge_judge_title');
        $judgeVoteWeight = (int) Session::get('challenge_judge_weight', 10);

        if (!$isJudge) {
            $strippedNik = ltrim((string)$participant->nik, '0');
            $judgeCandidates = array_values(array_unique(array_filter([
                (string)$participant->nik,
                $strippedNik,
                '0' . $strippedNik,
                str_pad($strippedNik, 8, '0', STR_PAD_LEFT),
                str_pad($strippedNik, 9, '0', STR_PAD_LEFT),
                (string)$participant->rfid,
                ltrim((string)$participant->rfid, '0'),
            ])));

            $matchedJudge = PoolJudge::where('is_active', true)
                ->where(function ($q) use ($judgeCandidates) {
                    $q->whereIn('nik', $judgeCandidates)
                        ->orWhereIn('rfid', $judgeCandidates);
                })
                ->first();

            if ($matchedJudge) {
                $isJudge = true;
                $judgeTitle = $matchedJudge->title ?: 'Dewan Juri';
                $judgeVoteWeight = (int)($matchedJudge->vote_weight > 0 ? $matchedJudge->vote_weight : 10);
                Session::put('challenge_is_judge', true);
                Session::put('challenge_judge_title', $judgeTitle);
                Session::put('challenge_judge_weight', $judgeVoteWeight);
            }
        }

        return view('pool_qcc.booth.challenge', compact('booth', 'participant', 'question', 'shuffledOptions', 'isJudge', 'judgeTitle', 'judgeVoteWeight'));
    }

    /**
     * Submit Jawaban Kuis di Booth
     */
    public function submitAnswerAtBooth(Request $request, $boothId)
    {
        $request->validate([
            'question_id' => 'required|exists:pool_questions,id',
            'answer' => 'required|in:A,B,C,D,E',
        ]);

        $booth = PoolBooth::findOrFail($boothId);
        $participantId = Session::get('challenge_participant_id');

        if (!$participantId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sesi telah berakhir, silakan tap ulang ID Card Anda.',
            ], 401);
        }

        $participant = PoolParticipant::findOrFail($participantId);
        $question = PoolQuestion::findOrFail($request->question_id);

        if ($participant->booths_voted_count >= 10) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda sudah mencapai batas maksimal 10 booth pooling!',
            ], 422);
        }

        if ($participant->hasVotedBooth($booth->id)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda sudah pernah melakukan pooling untuk booth ini!',
            ], 422);
        }

        // Terjemahkan jawaban yang dipilih di layar ke original key master di database
        $chosenDisplayAnswer = strtoupper($request->answer); // Misal peserta mengklik 'C'
        $displayToOrigMap = Session::get('challenge_options_map', []);
        $origToDisplayMap = Session::get('challenge_orig_to_display_map', []);

        $originalKey = $displayToOrigMap[$chosenDisplayAnswer] ?? $chosenDisplayAnswer;
        $correctAnswerOrig = strtoupper($question->correct_answer);

        $isJudge = (bool) Session::get('challenge_is_judge', false);
        $judgeTitle = Session::get('challenge_judge_title', 'Dewan Juri');
        $judgeVoteWeight = (int) Session::get('challenge_judge_weight', 10);

        if (!$isJudge) {
            $strippedNik = ltrim((string)$participant->nik, '0');
            $judgeCandidates = array_values(array_unique(array_filter([
                (string)$participant->nik,
                $strippedNik,
                '0' . $strippedNik,
                str_pad($strippedNik, 8, '0', STR_PAD_LEFT),
                str_pad($strippedNik, 9, '0', STR_PAD_LEFT),
                (string)$participant->rfid,
                ltrim((string)$participant->rfid, '0'),
            ])));

            $matchedJudge = PoolJudge::where('is_active', true)
                ->where(function ($q) use ($judgeCandidates) {
                    $q->whereIn('nik', $judgeCandidates)
                        ->orWhereIn('rfid', $judgeCandidates);
                })
                ->first();

            if ($matchedJudge) {
                $isJudge = true;
                $judgeTitle = $matchedJudge->title ?: 'Dewan Juri';
                $judgeVoteWeight = (int)($matchedJudge->vote_weight > 0 ? $matchedJudge->vote_weight : 10);
            }
        }

        $isCorrect = (strtoupper($originalKey) === $correctAnswerOrig);
        // Bobot vote untuk Booth: Juri / VIP memberikan bobot poin dinamis (misal 10 poin), peserta umum 1 poin
        $boothPoints = $isCorrect ? ($isJudge ? $judgeVoteWeight : ($question->points > 0 ? $question->points : 1)) : 0;
        // Poin untuk Diri Sendiri (Reward Nominee peserta): TETAP 1 poin jika benar agar tidak jomplang nominasi rewardnya
        $participantPoints = $isCorrect ? 1 : 0;

        // Label tampilan kunci jawaban yang benar di layar peserta saat ini
        $correctAnswerDisplay = $origToDisplayMap[$correctAnswerOrig] ?? $correctAnswerOrig;
        $correctAnswerField = 'option_' . strtolower($correctAnswerOrig);
        $correctAnswerText = $question->{$correctAnswerField} ?? '';

        DB::transaction(function () use ($participant, $booth, $question, $originalKey, $isCorrect, $boothPoints, $participantPoints, $isJudge) {
            // 1. Simpan Log Vote (Simpan original key A/B/C/D agar konsisten dengan master bank soal)
            PoolVote::create([
                'participant_id' => $participant->id,
                'booth_id' => $booth->id,
                'question_id' => $question->id,
                'participant_answer' => $originalKey,
                'is_correct' => $isCorrect,
                'points_awarded' => $boothPoints,
                'is_judge_vote' => $isJudge,
                'answered_at' => Carbon::now(),
            ]);

            // 2. Tambah poin peserta jika benar (TETAP 1 poin untuk personal score reward nominee)
            if ($isCorrect) {
                $participant->increment('total_score', $participantPoints);
            }
            $participant->increment('booths_voted_count', 1);
            $participant->update(['last_activity_at' => Carbon::now()]);

            // 3. Tambah poin booth jika benar (Bobot vote khusus juri/VIP)
            if ($isCorrect) {
                $booth->increment('total_points', $boothPoints);
            }
            $booth->increment('total_voters', 1);
        });

        // Selesai kuis, bersihkan session challenge agar booth kembali standby
        Session::forget('challenge_participant_id');
        Session::forget('challenge_booth_id');
        Session::forget('challenge_question_id');
        Session::forget('challenge_options_map');
        Session::forget('challenge_orig_to_display_map');

        $participant->refresh();
        $booth->refresh();

        return response()->json([
            'status' => 'success',
            'is_correct' => $isCorrect,
            'points_earned' => $participantPoints,
            'booth_points_earned' => $boothPoints,
            'is_judge' => $isJudge,
            'judge_title' => $judgeTitle,
            'judge_vote_weight' => $judgeVoteWeight,
            'correct_answer' => $correctAnswerDisplay,
            'correct_answer_display' => $correctAnswerDisplay,
            'correct_answer_text' => $correctAnswerText,
            'explanation' => $question->explanation,
            'participant' => [
                'name' => $participant->name,
                'total_score' => $participant->total_score,
                'booths_voted_count' => $participant->booths_voted_count,
                'remaining_quota' => max(0, 10 - $participant->booths_voted_count),
            ],
            'booth' => [
                'code' => $booth->code,
                'name' => $booth->name,
                'total_points' => $booth->total_points,
            ],
            'return_url' => route('pool-qcc.booth-station', $booth->id),
        ]);
    }

    /**
     * Halaman Live Display / Leaderboard Screen
     * Mendukung reload data real-time via async AJAX maupun render halaman penuh
     */
    public function leaderboard(Request $request)
    {
        $booths = PoolBooth::orderBy('total_points', 'desc')
            ->orderBy('total_voters', 'desc')
            ->get();

        // Ambil daftar NIK & RFID juri aktif agar tidak masuk dalam nominasi reward dan takeover peserta
        $judgeIdentifiers = PoolJudge::where('is_active', true)
            ->get(['nik', 'rfid'])
            ->flatMap(function ($j) {
                $nik = (string)$j->nik;
                $stripped = ltrim($nik, '0');
                return array_filter([
                    $nik,
                    $stripped,
                    '0' . $stripped,
                    str_pad($stripped, 8, '0', STR_PAD_LEFT),
                    str_pad($stripped, 9, '0', STR_PAD_LEFT),
                    (string)$j->rfid,
                ]);
            })
            ->unique()
            ->values()
            ->toArray();

        $participants = PoolParticipant::whereNotIn('nik', $judgeIdentifiers)
            ->where(function ($q) use ($judgeIdentifiers) {
                $q->whereNull('rfid')
                  ->orWhereNotIn('rfid', $judgeIdentifiers);
            })
            ->orderBy('total_score', 'desc')
            ->orderBy('updated_at', 'asc')
            ->get();

        $totalVotes = PoolVote::count();
        $totalParticipants = PoolParticipant::count();

        // Kelompokkan peserta berdasarkan 5 Reward Tiers
        // Format nominasi persis sesuai acuan:
        // 1. Reward 1: > 8 booth (dipilih 1 pemenang)
        // 2. Reward 2: > 6 booth (dipilih 1 pemenang)
        // 3. Reward 3: > 5 booth (dipilih 1 pemenang)
        // 4. Reward 4: > 4 booth (dipilih 1 pemenang)
        // 5. Reward 5: > 2 booth (dipilih 16 pemenang)
        $rewardTiers = [
            'reward_1' => [
                'id' => 'reward_1',
                'name' => 'Reward 1',
                'title' => 'NOMINASI REWARD 1',
                'rule_text' => '> 8 booth',
                'criteria_text' => 'Bisa menjawab benar > 8 booth',
                'quota_winners' => 1,
                'min_points' => 9,
                'color' => 'amber',
                'border_color' => 'border-amber-400 dark:border-amber-500',
                'badge_color' => 'bg-amber-400 text-slate-950',
                'icon' => 'fa-crown',
                'participants' => $participants->filter(fn($p) => $p->total_score > 8)->values(),
            ],
            'reward_2' => [
                'id' => 'reward_2',
                'name' => 'Reward 2',
                'title' => 'NOMINASI REWARD 2',
                'rule_text' => '> 6 booth',
                'criteria_text' => 'Bisa menjawab benar > 6 booth',
                'quota_winners' => 1,
                'min_points' => 7,
                'color' => 'cyan',
                'border_color' => 'border-cyan-400 dark:border-cyan-500',
                'badge_color' => 'bg-cyan-400 text-slate-950',
                'icon' => 'fa-gem',
                'participants' => $participants->filter(fn($p) => $p->total_score > 6 && $p->total_score <= 8)->values(),
            ],
            'reward_3' => [
                'id' => 'reward_3',
                'name' => 'Reward 3',
                'title' => 'NOMINASI REWARD 3',
                'rule_text' => '> 5 booth',
                'criteria_text' => 'Bisa menjawab benar > 5 booth',
                'quota_winners' => 1,
                'min_points' => 6,
                'color' => 'emerald',
                'border_color' => 'border-emerald-400 dark:border-emerald-500',
                'badge_color' => 'bg-emerald-400 text-slate-950',
                'icon' => 'fa-medal',
                'participants' => $participants->filter(fn($p) => $p->total_score > 5 && $p->total_score <= 6)->values(),
            ],
            'reward_4' => [
                'id' => 'reward_4',
                'name' => 'Reward 4',
                'title' => 'NOMINASI REWARD 4',
                'rule_text' => '> 4 booth',
                'criteria_text' => 'Bisa menjawab benar > 4 booth',
                'quota_winners' => 1,
                'min_points' => 5,
                'color' => 'orange',
                'border_color' => 'border-orange-400 dark:border-orange-500',
                'badge_color' => 'bg-orange-400 text-slate-950',
                'icon' => 'fa-award',
                'participants' => $participants->filter(fn($p) => $p->total_score > 4 && $p->total_score <= 5)->values(),
            ],
            'reward_5' => [
                'id' => 'reward_5',
                'name' => 'Reward 5',
                'title' => 'NOMINASI REWARD 5',
                'rule_text' => '> 2 booth',
                'criteria_text' => 'Bisa menjawab benar > 2 booth',
                'quota_winners' => 16,
                'min_points' => 3,
                'color' => 'purple',
                'border_color' => 'border-purple-400 dark:border-purple-500',
                'badge_color' => 'bg-purple-400 text-slate-950',
                'icon' => 'fa-gift',
                'participants' => $participants->filter(fn($p) => $p->total_score > 2 && $p->total_score <= 4)->values(),
            ],
        ];

        $otherParticipants = $participants->filter(fn($p) => $p->total_score <= 2)->values();

        $rewardCandidates = collect($rewardTiers)->mapWithKeys(function ($tier, $key) {
            return [
                $key => [
                    'id' => $tier['id'],
                    'name' => $tier['name'],
                    'title' => $tier['title'],
                    'rule_text' => $tier['rule_text'],
                    'color' => $tier['color'],
                    'icon' => $tier['icon'],
                    'candidates' => $tier['participants']->map(function ($p) {
                        return [
                            'id' => $p->id,
                            'name' => $p->name,
                            'nik' => $p->nik ?? '-',
                            'department' => $p->department ?? 'Umum',
                            'photo' => $p->photo,
                            'total_score' => (int) $p->total_score,
                            'booths_voted_count' => (int) $p->booths_voted_count,
                        ];
                    })->values(),
                ]
            ];
        });

        // Jika request via AJAX / Fetch, kirim partial HTML dan stats untuk live update tanpa reload halaman
        if ($request->ajax() || $request->wantsJson()) {
            $arenaHtml = view('pool_qcc.booth.leaderboard_arena', compact('booths', 'participants', 'rewardTiers', 'otherParticipants'))->render();
            return response()->json([
                'status' => 'success',
                'arena_html' => $arenaHtml,
                'total_votes' => number_format($totalVotes),
                'total_participants' => number_format($totalParticipants),
                'updated_at' => Carbon::now()->format('H:i:s'),
                'booth_ranks' => $booths->values()->map(function ($b, $idx) {
                    return [
                        'id' => $b->id,
                        'name' => $b->name,
                        'rank' => $idx + 1,
                        'points' => (int) $b->total_points,
                    ];
                }),
                'participant_ranks' => $participants->values()->map(function ($p, $idx) {
                    return [
                        'id' => $p->id,
                        'name' => $p->name,
                        'rank' => $idx + 1,
                        'score' => (int) $p->total_score,
                    ];
                }),
                'reward_candidates' => $rewardCandidates,
            ]);
        }

        return view('pool_qcc.booth.leaderboard', compact('booths', 'participants', 'rewardTiers', 'otherParticipants', 'rewardCandidates', 'totalVotes', 'totalParticipants'));
    }

    /**
     * Halaman Dedicated Lucky Draw Spin Wheel (Full Page View)
     */
    public function spinWheel(Request $request)
    {
        // Ambil daftar NIK & RFID juri aktif agar tidak masuk dalam undian lucky draw
        $judgeIdentifiers = PoolJudge::where('is_active', true)
            ->get(['nik', 'rfid'])
            ->flatMap(function ($j) {
                $nik = (string)$j->nik;
                $stripped = ltrim($nik, '0');
                return array_filter([
                    $nik,
                    $stripped,
                    '0' . $stripped,
                    str_pad($stripped, 8, '0', STR_PAD_LEFT),
                    str_pad($stripped, 9, '0', STR_PAD_LEFT),
                    (string)$j->rfid,
                ]);
            })
            ->unique()
            ->values()
            ->toArray();

        $participants = PoolParticipant::whereNotIn('nik', $judgeIdentifiers)
            ->where(function ($q) use ($judgeIdentifiers) {
                $q->whereNull('rfid')
                  ->orWhereNotIn('rfid', $judgeIdentifiers);
            })
            ->orderBy('total_score', 'desc')
            ->orderBy('updated_at', 'asc')
            ->get();

        $rewardTiers = [
            'reward_1' => [
                'id' => 'reward_1',
                'name' => 'Reward 1',
                'title' => 'NOMINASI REWARD 1',
                'rule_text' => '> 8 booth',
                'criteria_text' => 'Bisa menjawab benar > 8 booth',
                'quota_winners' => 1,
                'color' => 'amber',
                'icon' => 'fa-crown',
                'participants' => $participants->filter(fn($p) => $p->total_score > 8)->values(),
            ],
            'reward_2' => [
                'id' => 'reward_2',
                'name' => 'Reward 2',
                'title' => 'NOMINASI REWARD 2',
                'rule_text' => '> 6 booth',
                'criteria_text' => 'Bisa menjawab benar > 6 booth',
                'quota_winners' => 1,
                'color' => 'cyan',
                'icon' => 'fa-gem',
                'participants' => $participants->filter(fn($p) => $p->total_score > 6 && $p->total_score <= 8)->values(),
            ],
            'reward_3' => [
                'id' => 'reward_3',
                'name' => 'Reward 3',
                'title' => 'NOMINASI REWARD 3',
                'rule_text' => '> 5 booth',
                'criteria_text' => 'Bisa menjawab benar > 5 booth',
                'quota_winners' => 1,
                'color' => 'emerald',
                'icon' => 'fa-medal',
                'participants' => $participants->filter(fn($p) => $p->total_score > 5 && $p->total_score <= 6)->values(),
            ],
            'reward_4' => [
                'id' => 'reward_4',
                'name' => 'Reward 4',
                'title' => 'NOMINASI REWARD 4',
                'rule_text' => '> 4 booth',
                'criteria_text' => 'Bisa menjawab benar > 4 booth',
                'quota_winners' => 1,
                'color' => 'orange',
                'icon' => 'fa-award',
                'participants' => $participants->filter(fn($p) => $p->total_score > 4 && $p->total_score <= 5)->values(),
            ],
            'reward_5' => [
                'id' => 'reward_5',
                'name' => 'Reward 5',
                'title' => 'NOMINASI REWARD 5',
                'rule_text' => '> 2 booth',
                'criteria_text' => 'Bisa menjawab benar > 2 booth',
                'quota_winners' => 16,
                'color' => 'purple',
                'icon' => 'fa-gift',
                'participants' => $participants->filter(fn($p) => $p->total_score > 2 && $p->total_score <= 4)->values(),
            ],
        ];

        $rewardCandidates = collect($rewardTiers)->mapWithKeys(function ($tier, $key) {
            return [
                $key => [
                    'id' => $tier['id'],
                    'name' => $tier['name'],
                    'title' => $tier['title'],
                    'rule_text' => $tier['rule_text'],
                    'criteria_text' => $tier['criteria_text'],
                    'quota_winners' => $tier['quota_winners'],
                    'color' => $tier['color'],
                    'icon' => $tier['icon'],
                    'candidates' => $tier['participants']->map(function ($p) {
                        return [
                            'id' => $p->id,
                            'name' => $p->name,
                            'nik' => $p->nik ?? '-',
                            'department' => $p->department ?? 'Umum',
                            'photo' => $p->photo,
                            'total_score' => (int) $p->total_score,
                            'booths_voted_count' => (int) $p->booths_voted_count,
                        ];
                    })->values(),
                ]
            ];
        });

        $activeTier = $request->get('tier', 'reward_1');

        return view('pool_qcc.booth.spin_wheel', compact('rewardTiers', 'rewardCandidates', 'activeTier'));
    }
}
