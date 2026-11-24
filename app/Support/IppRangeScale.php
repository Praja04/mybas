<?php

namespace App\Support;

/**
 * Helper untuk classification "Range" dengan score scale dinamis 1-5.
 *
 * User hanya mengisi 4 angka boundary. Sistem menyusun 5 tier:
 *   seg0: ( -inf , b1 ]  ->  min null, max b1
 *   seg1: ( b1 , b2 ]
 *   seg2: ( b2 , b3 ]
 *   seg3: ( b3 , b4 ]
 *   seg4: ( b4 , +inf )  ->  min b4, max null
 *
 * Arah (direction) menentukan mapping skor:
 *   low_is_good  : nilai kecil = skor 5  (seg0=5 ... seg4=1)
 *   high_is_good : nilai kecil = skor 1  (seg0=1 ... seg4=5)
 *
 * Achievement category dari skor: 5 = Exceed, 4-3 = Achieve, 2-1 = Not Achieve.
 */
class IppRangeScale
{
    public const DIRECTION_LOW_IS_GOOD = 'low_is_good';
    public const DIRECTION_HIGH_IS_GOOD = 'high_is_good';

    public const MATCH_RANGE = 'range';
    public const MATCH_EQUALS = 'equals';

    // Mode Custom: operator eksplisit per skor.
    public const MATCH_GTE = 'gte';       // => lebih dari (strict, x > a)
    public const MATCH_LTE = 'lte';       // =< kurang dari (strict, x < a)
    public const MATCH_EQ = 'eq';         // = tepat nilai
    public const MATCH_BETWEEN = 'between'; // a - b => a <= nilai <= b (kedua batas termasuk)

    public const LABELS = [
        5 => 'Very Good',
        4 => 'Good',
        3 => 'Standard',
        2 => 'Low',
        1 => 'Very Low',
    ];

    public static function normalizeDirection(?string $direction): string
    {
        return $direction === self::DIRECTION_HIGH_IS_GOOD
            ? self::DIRECTION_HIGH_IS_GOOD
            : self::DIRECTION_LOW_IS_GOOD;
    }

    /**
     * Susun 4 boundary jadi 5 tier (skor 1-5) — wrapper kompatibilitas.
     *
     * @param float[] $boundaries 4 angka (boleh belum terurut)
     */
    public static function buildTiers(array $boundaries, ?string $direction): array
    {
        return self::buildTiersForScores([1, 2, 3, 4, 5], $boundaries, $direction);
    }

    /**
     * Susun N boundary + daftar skor aktif (mis. [1,3,5]) jadi tier dinamis.
     * Segmen kanonik: (t_{k-1}, t_k], t_0=-inf, t_N=+inf.
     * high_is_good: skor menaik mengikuti segmen menaik; low_is_good: kebalikannya.
     *
     * @param int[]   $scores     skor aktif (menaik, unik, 1-5)
     * @param float[] $boundaries N-1 angka (boleh belum terurut)
     */
    public static function buildTiersForScores(array $scores, array $boundaries, ?string $direction): array
    {
        $scores = self::normalizeScores($scores);
        $m = count($scores);
        if ($m < 1 || count($boundaries) !== $m - 1) {
            return [];
        }

        $nums = [];
        foreach ($boundaries as $b) {
            if ($b === null || $b === '' || !is_numeric($b)) {
                return [];
            }
            $nums[] = (float) $b;
        }
        sort($nums);

        $dir = self::normalizeDirection($direction);
        $tiers = [];
        for ($i = 0; $i < $m; $i++) {
            $min = $i === 0 ? null : $nums[$i - 1];
            $max = $i === $m - 1 ? null : $nums[$i];
            $score = $dir === self::DIRECTION_HIGH_IS_GOOD ? $scores[$i] : $scores[$m - 1 - $i];
            $tiers[] = [
                'score' => $score,
                'match_type' => self::MATCH_RANGE,
                'min_value' => $min,
                'max_value' => $max,
                'label' => self::LABELS[$score] ?? null,
            ];
        }
        return $tiers;
    }

    /**
     * Normalisasi daftar skor: integer 1-5, unik, urut menaik.
     *
     * @return int[]
     */
    public static function normalizeScores(?array $scores): array
    {
        if (!is_array($scores)) {
            return [];
        }
        $seen = [];
        foreach ($scores as $s) {
            if ($s === null || $s === '' || !is_numeric($s)) {
                continue;
            }
            $n = (int) $s;
            if ($n >= 1 && $n <= 5) {
                $seen[$n] = true;
            }
        }
        $out = array_keys($seen);
        sort($out);
        return $out;
    }

    /**
     * Validasi set skor + boundary untuk mode Range numerik.
     * Mengembalikan pesan error, atau null bila valid.
     *
     * @param int[]   $scores
     * @param mixed[] $boundaries
     */
    public static function validateRangeSet(array $scores, array $boundaries): ?string
    {
        $scores = self::normalizeScores($scores);
        if (count($scores) < 3) {
            return 'Skor Range minimal 3 level.';
        }
        if (!in_array(1, $scores, true) || !in_array(5, $scores, true)) {
            return 'Skor Range wajib menyertakan skor 1 dan 5.';
        }
        if (count($boundaries) !== count($scores) - 1) {
            return 'Jumlah boundary Range harus ' . (count($scores) - 1)
                . ' angka (jumlah skor - 1).';
        }
        foreach ($boundaries as $b) {
            if ($b === null || $b === '' || !is_numeric($b)) {
                return 'Boundary Range harus berupa angka.';
            }
        }
        if (!self::boundariesAreAscending($boundaries)) {
            return 'Boundary Range harus berurutan menaik (nilai kecil ke besar).';
        }
        return null;
    }

    /**
     * Apakah boundary terurut menaik ketat (sesuai urutan indeks kanonik).
     *
     * @param mixed[] $boundaries
     */
    public static function boundariesAreAscending(array $boundaries): bool
    {
        $prev = null;
        foreach ($boundaries as $b) {
            if ($b === null || $b === '' || !is_numeric($b)) {
                return false;
            }
            $v = (float) $b;
            if ($prev !== null && $v <= $prev) {
                return false;
            }
            $prev = $v;
        }
        return count($boundaries) > 0;
    }

    /**
     * Jumlah desimal maksimum dari sekumpulan angka (minimal 2).
     *
     * @param mixed[] $values
     */
    public static function decimalsFor(array $values): int
    {
        $decimals = 0;
        foreach ($values as $v) {
            if ($v === null || $v === '' || !is_numeric($v)) {
                continue;
            }
            $s = rtrim(rtrim(number_format((float) $v, 6, '.', ''), '0'), '.');
            $pos = strpos($s, '.');
            $d = $pos === false ? 0 : strlen($s) - $pos - 1;
            if ($d > $decimals) {
                $decimals = $d;
            }
        }
        return max(2, $decimals);
    }

    /**
     * Selisih 0.01 (atau 0.001 untuk 3 desimal) agar batas antar tier tidak kembar.
     *
     * @param mixed[] $values
     */
    public static function gapFor(array $values): float
    {
        return pow(10, -self::decimalsFor($values));
    }

    /**
     * Format angka tanpa nol ekor, mis. 70.01 / 1.824.
     */
    public static function formatNumber($value, int $decimals): string
    {
        $s = number_format((float) $value, $decimals, '.', '');
        $s = rtrim(rtrim($s, '0'), '.');
        return ($s === '' || $s === '-') ? '0' : $s;
    }

    /**
     * Label rentang satu tier: `< max` (terbawah), `min – max` (segmen di atas
     * segmen terbawah, batas terkecil miliknya), `min+ε – max` (tengah lain),
     * `> min` (teratas).
     *
     * @param array{min_value:?float,max_value:?float} $tier
     * @param mixed[]                                  $allBoundaries
     */
    public static function tierRangeLabel(array $tier, array $allBoundaries): string
    {
        $decimals = self::decimalsFor($allBoundaries);
        $eps = self::gapFor($allBoundaries);
        $min = $tier['min_value'] ?? null;
        $max = $tier['max_value'] ?? null;
        if ($min === null && $max === null) {
            return 'semua nilai';
        }
        if ($min === null) {
            return '< ' . self::formatNumber($max, $decimals);
        }
        if ($max === null) {
            return '> ' . self::formatNumber($min, $decimals);
        }
        $first = null;
        foreach ($allBoundaries as $v) {
            if ($v === null || $v === '' || !is_numeric($v)) {
                continue;
            }
            $f = (float) $v;
            if ($first === null || $f < $first) {
                $first = $f;
            }
        }
        $lower = ($first !== null && (float) $min === $first)
            ? (float) $min
            : (float) $min + $eps;
        return self::formatNumber($lower, $decimals)
            . ' – ' . self::formatNumber($max, $decimals);
    }

    /**
     * Susun 5 label teks (indeks 0 => skor 1 ... indeks 4 => skor 5) jadi 5 tier `==`.
     * Skor mengikuti urutan label: label pertama = 1, label terakhir = 5.
     *
     * @param string[] $labels 5 label teks
     */
    public static function buildTextTiers(array $labels): array
    {
        $clean = [];
        foreach (array_values($labels) as $i => $label) {
            if ($i >= 5) {
                break;
            }
            $text = trim((string) $label);
            if ($text === '') {
                return [];
            }
            $clean[] = $text;
        }
        if (count($clean) !== 5) {
            return [];
        }

        $tiers = [];
        foreach ($clean as $i => $text) {
            $tiers[] = [
                'score' => $i + 1,
                'match_type' => self::MATCH_EQUALS,
                'min_value' => null,
                'max_value' => null,
                'label' => $text,
            ];
        }
        return $tiers;
    }

    /**
     * Apakah tier memakai mode teks `==`.
     *
     * @param array<int,array>|null $tiers
     */
    public static function isTextTiers(?array $tiers): bool
    {
        if (empty($tiers)) {
            return false;
        }
        foreach ($tiers as $t) {
            if (($t['match_type'] ?? null) === self::MATCH_EQUALS) {
                return true;
            }
        }
        return false;
    }

    /**
     * Cocokkan aktual teks ke tier `==` (case-insensitive, spasi dinormalisasi).
     *
     * @param array<int,array{score:int,match_type:?string,label:?string}> $tiers
     */
    public static function matchTierText(array $tiers, ?string $actual): ?array
    {
        $needle = self::normalizeText($actual);
        if ($needle === '') {
            return null;
        }
        foreach ($tiers as $t) {
            if (($t['match_type'] ?? null) !== self::MATCH_EQUALS) {
                continue;
            }
            if (self::normalizeText((string) ($t['label'] ?? '')) === $needle) {
                return $t;
            }
        }
        return null;
    }

    /**
     * Daftar label mode `==` terurut skor 1-5.
     *
     * @param array<int,array> $tiers
     * @return string[]
     */
    public static function textLabels(array $tiers): array
    {
        $labels = [];
        foreach ($tiers as $t) {
            if (($t['match_type'] ?? null) !== self::MATCH_EQUALS) {
                continue;
            }
            $score = (int) ($t['score'] ?? 0);
            if ($score >= 1 && $score <= 5) {
                $labels[$score] = (string) ($t['label'] ?? '');
            }
        }
        ksort($labels);
        return array_values($labels);
    }

    /**
     * Ringkasan target mode `==`, contoh: "1=Tidak submit / 2=Hitam / ..." (maks 255 char).
     *
     * @param string[] $labels 5 label terurut skor 1-5
     */
    public static function summarizeText(array $labels): string
    {
        $parts = [];
        foreach (array_values($labels) as $i => $label) {
            $parts[] = ($i + 1) . '=' . trim((string) $label);
        }
        return mb_substr(implode(' / ', $parts), 0, 255);
    }

    private static function normalizeText(?string $text): string
    {
        $text = trim((string) $text);
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;
        return mb_strtolower($text);
    }

    /**
     * Cocokkan nilai aktual ke salah satu tier. Mengembalikan tier tsb atau null.
     *
     * Partisi: (-∞, b0) segmen terbawah; [b0, b1] segmen di atasnya (batas
     * terkecil milik segmen ini); (b_{j-1}, b_j] segmen tengah lain; (b_{m-2}, ∞)
     * segmen teratas.
     *
     * @param array<int,array{score:int,min_value:?float,max_value:?float}> $tiers
     */
    public static function matchTier(array $tiers, float $actual): ?array
    {
        $firstMin = null;
        foreach ($tiers as $t) {
            if ($t['min_value'] === null) {
                continue;
            }
            $m = (float) $t['min_value'];
            if ($firstMin === null || $m < $firstMin) {
                $firstMin = $m;
            }
        }
        foreach ($tiers as $t) {
            $min = $t['min_value'] !== null ? (float) $t['min_value'] : null;
            $max = $t['max_value'] !== null ? (float) $t['max_value'] : null;
            if ($min === null) {
                if ($max === null || $actual < $max) {
                    return $t;
                }
                continue;
            }
            $okMin = $actual > $min
                || ($firstMin !== null && $min === $firstMin && $actual >= $min);
            $okMax = $max === null || $actual <= $max;
            if ($okMin && $okMax) {
                return $t;
            }
        }
        return null;
    }

    public static function achievementFromScore(int $score): string
    {
        if ($score >= 5) {
            return 'Exceed';
        }
        if ($score >= 3) {
            return 'Achieve';
        }
        return 'Not Achieve';
    }

    /**
     * Ringkasan teks untuk kolom target (display singkat, tier asli tetap di tabel master).
     *
     * @param int[]|null $scores daftar skor aktif; null = [1,2,3,4,5]
     */
    public static function summarize(array $boundaries, ?string $direction, ?array $scores = null): string
    {
        $scores = self::normalizeScores($scores);
        if (empty($scores)) {
            $scores = [1, 2, 3, 4, 5];
        }

        $tiers = self::buildTiersForScores($scores, $boundaries, $direction);
        if (empty($tiers)) {
            $dir = self::normalizeDirection($direction);
            $b = array_map(fn ($x) => (string) (float) $x, $boundaries);
            $joined = implode(' / ', $b);
            return $dir === self::DIRECTION_HIGH_IS_GOOD
                ? '< ' . $joined . ' >'
                : '> ' . $joined . ' <';
        }

        usort($tiers, fn ($a, $b) => $a['score'] <=> $b['score']);
        $parts = [];
        foreach ($tiers as $t) {
            $parts[] = $t['score'] . '=' . self::tierRangeLabel($t, $boundaries);
        }
        return mb_substr(implode(' / ', $parts), 0, 255);
    }

    /**
     * Parse ringkasan skala Range dari teks target (hasil summarize atau tulisan
     * di kolom Target template) menjadi skor + boundary + arah, atau label teks.
     *
     * Format yang dikenali (multi-baris atau satu baris dipisah " / "):
     *   low_is_good : 1 = > 10 / 2 = 7.01 - 10 / ... / 5 = < 4
     *   high_is_good: 1 = < 1  / 2 = 1 - 4     / ... / 5 = > 10
     *   custom      : 1 =< 1 / 2 = 1 - 3 / ... / 5 => 8
     *   teks (==)   : 1 = "Tidak submit" / 2 = "Hitam" / ... / 5 = "Biru"
     * Simbol <=, >=, ≤, ≥, en dash/em dash, dan sufiks % juga diterima.
     *
     * @return array{scores:int[],boundaries:float[],direction:string}|array{custom:true,tiers:array}|array{text:true,labels:string[]}|null
     */
    public static function parseTargetSummary(string $text): ?array
    {
        $canonical = self::parseCanonicalTargetSummary($text);
        if ($canonical !== null) {
            return $canonical;
        }
        $custom = self::parseCustomTargetSummary($text);
        if ($custom !== null) {
            return $custom;
        }
        return self::parseTextTargetSummary($text);
    }

    private static function parseCanonicalTargetSummary(string $text): ?array
    {
        $text = trim($text);
        if ($text === '') {
            return null;
        }
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        if (strpos($text, "\n") === false && strpos($text, ' / ') !== false) {
            $text = str_replace(' / ', "\n", $text);
        }

        $rows = [];
        foreach (preg_split('/\n+/', $text) as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            if (preg_match('/^([1-5])\s*=\s*(.+)$/u', $line, $m) !== 1) {
                return null;
            }
            $score = (int) $m[1];
            if (isset($rows[$score])) {
                return null;
            }
            $expr = trim($m[2]);

            if (preg_match('/^(?:<=|≤|<)\s*(.+)$/u', $expr, $mm) === 1) {
                $v = self::toFloat($mm[1]);
                if ($v === null) {
                    return null;
                }
                $rows[$score] = ['side' => 'max', 'value' => $v];
                continue;
            }
            if (preg_match('/^(?:>=|≥|>)\s*(.+)$/u', $expr, $mm) === 1) {
                $v = self::toFloat($mm[1]);
                if ($v === null) {
                    return null;
                }
                $rows[$score] = ['side' => 'min', 'value' => $v];
                continue;
            }
            $range = self::parseRangeExpr($expr);
            if ($range === null || $range[0] >= $range[1]) {
                return null;
            }
            $rows[$score] = ['side' => 'mid', 'lo' => $range[0], 'hi' => $range[1]];
        }

        ksort($rows);
        $scores = array_keys($rows);
        if (count($scores) < 3 || !in_array(1, $scores, true) || !in_array(5, $scores, true)) {
            return null;
        }
        if ($rows[1]['side'] === 'max' && $rows[5]['side'] === 'min') {
            $direction = self::DIRECTION_HIGH_IS_GOOD;
        } elseif ($rows[1]['side'] === 'min' && $rows[5]['side'] === 'max') {
            $direction = self::DIRECTION_LOW_IS_GOOD;
        } else {
            return null;
        }

        $values = [];
        foreach ($scores as $score) {
            if ($score === 1 || $score === 5) {
                $values[] = $rows[$score]['value'];
                continue;
            }
            if ($rows[$score]['side'] !== 'mid') {
                return null;
            }
            $values[] = $rows[$score]['hi'];
        }
        $values = array_values(array_unique($values));
        sort($values);
        if (count($values) !== count($scores) - 1) {
            return null;
        }
        if (self::validateRangeSet($scores, $values) !== null) {
            return null;
        }

        return [
            'scores' => $scores,
            'boundaries' => $values,
            'direction' => $direction,
        ];
    }

    /**
     * Isi range_tiers/range_scores/range_direction (atau range_labels mode teks)
     * untuk detail ber-classification Range yang kolom target-nya cocok dengan
     * format ringkasan skala. Detail lain (atau teks yang tidak dikenali)
     * dibiarkan apa adanya.
     */
    public static function hydrateDetails(array $details): array
    {
        foreach ($details as $i => $detail) {
            if (($detail['classification'] ?? null) !== 'Range') {
                continue;
            }
            $parsed = self::parseTargetSummary((string) ($detail['target'] ?? ''));
            if ($parsed === null) {
                continue;
            }
            if (!empty($parsed['text'])) {
                $tiers = self::buildTextTiers($parsed['labels'] ?? []);
                if (empty($tiers)) {
                    continue;
                }
                $labels = array_map(fn ($t) => (string) $t['label'], $tiers);
                $details[$i]['range_tiers'] = $tiers;
                $details[$i]['range_labels'] = $labels;
                $details[$i]['range_scores'] = [1, 2, 3, 4, 5];
                $details[$i]['range_direction'] = null;
                $details[$i]['range_match_type'] = self::MATCH_EQUALS;
                $details[$i]['target'] = self::summarizeText($labels);
                continue;
            }
            if (!empty($parsed['custom'])) {
                $tiers = $parsed['tiers'] ?? [];
                if (empty($tiers)) {
                    continue;
                }
                $details[$i]['range_tiers'] = $tiers;
                $details[$i]['range_scores'] = array_map(fn ($t) => (int) $t['score'], $tiers);
                $details[$i]['range_direction'] = null;
                $details[$i]['range_match_type'] = 'custom';
                continue;
            }
            $tiers = self::buildTiersForScores($parsed['scores'], $parsed['boundaries'], $parsed['direction']);
            if (empty($tiers)) {
                continue;
            }
            $details[$i]['range_tiers'] = $tiers;
            $details[$i]['range_scores'] = $parsed['scores'];
            $details[$i]['range_direction'] = $parsed['direction'];
        }
        return $details;
    }

    /**
     * Apakah match_type termasuk operator mode Custom.
     */
    public static function isCustomMatchType(?string $type): bool
    {
        return in_array($type, [self::MATCH_GTE, self::MATCH_LTE, self::MATCH_EQ, self::MATCH_BETWEEN], true);
    }

    /**
     * Apakah kumpulan tier memakai mode Custom (array/Collection).
     *
     * @param array<int,array>|\Illuminate\Support\Collection|null $tiers
     */
    public static function isCustomTiers($tiers): bool
    {
        if ($tiers === null) {
            return false;
        }
        foreach ($tiers as $t) {
            $type = is_array($t) ? ($t['match_type'] ?? null) : ($t['match_type'] ?? null);
            if (self::isCustomMatchType($type)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Normalisasi baris Custom {score, match_type, min_value, max_value} jadi tier.
     *
     * @param array<int,array> $rows
     */
    public static function buildCustomTiers(array $rows): array
    {
        $byScore = [];
        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }
            $score = isset($row['score']) && is_numeric($row['score']) ? (int) $row['score'] : 0;
            $type = (string) ($row['match_type'] ?? '');
            if ($score < 1 || $score > 5 || !self::isCustomMatchType($type)) {
                continue;
            }
            $min = self::toNullableFloat($row['min_value'] ?? null);
            $max = self::toNullableFloat($row['max_value'] ?? null);
            if ($type === self::MATCH_EQ) {
                $v = $min !== null ? $min : $max;
                if ($v === null) {
                    continue;
                }
                $tier = ['score' => $score, 'match_type' => $type, 'min_value' => $v, 'max_value' => $v];
            } elseif ($type === self::MATCH_GTE) {
                if ($min === null) {
                    continue;
                }
                $tier = ['score' => $score, 'match_type' => $type, 'min_value' => $min, 'max_value' => null];
            } elseif ($type === self::MATCH_LTE) {
                if ($max === null) {
                    continue;
                }
                $tier = ['score' => $score, 'match_type' => $type, 'min_value' => null, 'max_value' => $max];
            } else {
                if ($min === null || $max === null || $min >= $max) {
                    continue;
                }
                $tier = ['score' => $score, 'match_type' => $type, 'min_value' => $min, 'max_value' => $max];
            }
            $tier['label'] = self::LABELS[$score] ?? null;
            $byScore[$score] = $tier;
        }
        ksort($byScore);
        return array_values($byScore);
    }

    /**
     * Validasi aturan Custom: operator & nilai, skor 1 dan 5 wajib, tidak
     * tumpang tindih, serta tidak ada celah yang memuat angka pada presisi
     * desimal seragam. Skor 1 dan 5 boleh memakai operator `=` (titik); nilai
     * di luar cakupan aturan tidak memperoleh skor.
     * Mengembalikan pesan error, atau null bila valid.
     *
     * @param array<int,array> $rows
     */
    public static function validateCustomRows(array $rows): ?string
    {
        $byScore = [];
        foreach ($rows as $row) {
            if (!is_array($row)) {
                return 'Aturan Custom tidak dikenali.';
            }
            $score = isset($row['score']) && is_numeric($row['score']) ? (int) $row['score'] : 0;
            if ($score < 1 || $score > 5) {
                return 'Skor Custom harus bernilai 1-5.';
            }
            $type = (string) ($row['match_type'] ?? '');
            if (!self::isCustomMatchType($type)) {
                return 'Operator Custom skor ' . $score . ' tidak dikenali.';
            }
            if (isset($byScore[$score])) {
                return 'Aturan Custom skor ' . $score . ' duplikat.';
            }
            $min = self::toNullableFloat($row['min_value'] ?? null);
            $max = self::toNullableFloat($row['max_value'] ?? null);
            if ($type === self::MATCH_EQ) {
                $v = $min !== null ? $min : $max;
                if ($v === null) {
                    return 'Aturan Custom skor ' . $score . ': nilai wajib diisi.';
                }
                $byScore[$score] = ['score' => $score, 'match_type' => $type, 'min_value' => $v, 'max_value' => $v];
            } elseif ($type === self::MATCH_GTE) {
                if ($min === null) {
                    return 'Aturan Custom skor ' . $score . ': nilai wajib diisi.';
                }
                $byScore[$score] = ['score' => $score, 'match_type' => $type, 'min_value' => $min, 'max_value' => null];
            } elseif ($type === self::MATCH_LTE) {
                if ($max === null) {
                    return 'Aturan Custom skor ' . $score . ': nilai wajib diisi.';
                }
                $byScore[$score] = ['score' => $score, 'match_type' => $type, 'min_value' => null, 'max_value' => $max];
            } else {
                if ($min === null || $max === null) {
                    return 'Aturan Custom skor ' . $score . ': kedua angka rentang wajib diisi.';
                }
                if ($min >= $max) {
                    return 'Aturan Custom skor ' . $score . ': angka pertama harus lebih kecil dari angka kedua.';
                }
                $byScore[$score] = ['score' => $score, 'match_type' => $type, 'min_value' => $min, 'max_value' => $max];
            }
        }

        if (empty($byScore)) {
            return 'Aturan Custom wajib diisi.';
        }
        ksort($byScore);
        $rules = array_values($byScore);
        $scores = array_column($rules, 'score');
        if (!in_array(1, $scores, true) || !in_array(5, $scores, true)) {
            return 'Aturan Custom wajib menyertakan skor 1 dan 5.';
        }

        $decimals = self::customScaleDecimals($rules);
        $factor = pow(10, $decimals);
        $intervals = [];
        foreach ($rules as $rule) {
            $score = (int) $rule['score'];
            if ($rule['match_type'] === self::MATCH_EQ) {
                $point = self::gridPoint((float) $rule['min_value'], $factor);
                $intervals[] = ['score' => $score, 'lo' => $point, 'hi' => $point];
            } elseif ($rule['match_type'] === self::MATCH_GTE) {
                $intervals[] = ['score' => $score, 'lo' => self::gridCeil((float) $rule['min_value'], $factor) + 1, 'hi' => INF];
            } elseif ($rule['match_type'] === self::MATCH_LTE) {
                $intervals[] = ['score' => $score, 'lo' => -INF, 'hi' => self::gridCeil((float) $rule['max_value'], $factor) - 1];
            } else {
                $intervals[] = [
                    'score' => $score,
                    'lo' => self::gridCeil((float) $rule['min_value'], $factor),
                    'hi' => self::gridFloor((float) $rule['max_value'], $factor),
                ];
            }
        }

        $count = count($intervals);
        for ($i = 0; $i < $count; $i++) {
            for ($j = $i + 1; $j < $count; $j++) {
                if (max($intervals[$i]['lo'], $intervals[$j]['lo']) <= min($intervals[$i]['hi'], $intervals[$j]['hi'])) {
                    return 'Aturan Custom tumpang tindih antara skor ' . $intervals[$i]['score']
                        . ' dan skor ' . $intervals[$j]['score'] . '.';
                }
            }
        }

        usort($intervals, function ($a, $b) {
            return $a['lo'] <=> $b['lo'];
        });
        $reach = -INF;
        foreach ($intervals as $i => $iv) {
            if ($i > 0 && $iv['lo'] > $reach + 1) {
                return 'Aturan Custom belum rapat: ada celah antara ' . self::formatGridBound($reach, $decimals)
                    . ' dan ' . self::formatGridBound($iv['lo'], $decimals) . '.';
            }
            if ($iv['hi'] > $reach) {
                $reach = $iv['hi'];
            }
        }

        return null;
    }

    /**
     * Presisi desimal seragam mode Custom: jumlah desimal terbanyak dari
     * semua angka aturan (0 bila semua bilangan bulat).
     *
     * @param array<int,array> $rows
     */
    public static function customScaleDecimals(array $rows): int
    {
        $decimals = 0;
        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }
            foreach (['min_value', 'max_value'] as $key) {
                $d = self::decimalPlacesOf($row[$key] ?? null);
                if ($d > $decimals) {
                    $decimals = $d;
                }
            }
        }
        return $decimals;
    }

    /**
     * Presisi desimal skala Range untuk pembulatan input aktual:
     * null bila tier kosong/bermode teks; Custom memakai customScaleDecimals;
     * kanonik (range) memakai desimal terbanyak dari batas min/max tanpa
     * lantai 2 (0 = wajib bilangan bulat).
     *
     * @param array<int,array>|\Illuminate\Support\Collection|null $tiers
     */
    public static function scaleDecimals($tiers): ?int
    {
        if ($tiers === null) {
            return null;
        }
        $rows = [];
        foreach ($tiers as $t) {
            $rows[] = is_array($t) ? $t : (array) $t;
        }
        if (empty($rows) || self::isTextTiers($rows)) {
            return null;
        }
        if (self::isCustomTiers($rows)) {
            return self::customScaleDecimals($rows);
        }
        $decimals = 0;
        foreach ($rows as $row) {
            foreach (['min_value', 'max_value'] as $key) {
                $d = self::decimalPlacesOf($row[$key] ?? null);
                if ($d > $decimals) {
                    $decimals = $d;
                }
            }
        }
        return $decimals;
    }

    private static function decimalPlacesOf($value): int
    {
        if ($value === null || $value === '' || !is_numeric($value)) {
            return 0;
        }
        $s = rtrim(rtrim(number_format((float) $value, 10, '.', ''), '0'), '.');
        $pos = strpos($s, '.');
        return $pos === false ? 0 : strlen($s) - $pos - 1;
    }

    private static function gridPoint(float $value, float $factor): int
    {
        return (int) round($value * $factor, 6);
    }

    private static function gridCeil(float $value, float $factor): int
    {
        return (int) ceil(round($value * $factor, 6));
    }

    private static function gridFloor(float $value, float $factor): int
    {
        return (int) floor(round($value * $factor, 6));
    }

    private static function formatGridBound($value, int $decimals): string
    {
        if ($value === INF) {
            return '+∞';
        }
        if ($value === -INF) {
            return '-∞';
        }
        $scaled = $decimals > 0 ? ((float) $value / pow(10, $decimals)) : (float) $value;
        return self::formatNumber($scaled, max(0, $decimals));
    }

    /**
     * Cocokkan nilai aktual ke tier mode Custom.
     *
     * @param array<int,array>|\Illuminate\Support\Collection $tiers
     */
    public static function matchTierCustom($tiers, float $actual): ?array
    {
        if ($tiers === null) {
            return null;
        }
        foreach ($tiers as $t) {
            $type = $t['match_type'] ?? null;
            $min = $t['min_value'] ?? null;
            $max = $t['max_value'] ?? null;
            if ($type === self::MATCH_EQ) {
                if ($min !== null && $actual == (float) $min) {
                    return $t;
                }
            } elseif ($type === self::MATCH_GTE) {
                if ($min !== null && $actual > (float) $min) {
                    return $t;
                }
            } elseif ($type === self::MATCH_LTE) {
                if ($max !== null && $actual < (float) $max) {
                    return $t;
                }
            } elseif ($type === self::MATCH_BETWEEN) {
                if ($min !== null && $max !== null && $actual >= (float) $min && $actual <= (float) $max) {
                    return $t;
                }
            }
        }
        return null;
    }

    /**
     * Apakah nilai aktual (setelah pembulatan presisi skala) masuk salah satu
     * tier skor. Tier teks/kosong dianggap cocok (validasi label terpisah).
     *
     * @param array<int,array>|null $tiers
     */
    public static function matchesAnyTier(?array $tiers, float $actual): bool
    {
        if (empty($tiers)) {
            return true;
        }
        if (self::isTextTiers($tiers)) {
            return true;
        }
        if (self::isCustomTiers($tiers)) {
            return self::matchTierCustom($tiers, $actual) !== null;
        }
        return self::matchTier($tiers, $actual) !== null;
    }

    /**
     * Ringkasan tier Custom, contoh: "1=< 1 / 2=1 – 3 / 5=> 7" (maks 255 char).
     *
     * @param array<int,array> $tiers
     */
    public static function summarizeCustom(array $tiers): string
    {
        usort($tiers, fn ($a, $b) => ((int) ($a['score'] ?? 0)) <=> ((int) ($b['score'] ?? 0)));
        $decimals = self::customScaleDecimals($tiers);
        $parts = [];
        foreach ($tiers as $t) {
            $score = (int) ($t['score'] ?? 0);
            if ($score < 1 || $score > 5) {
                continue;
            }
            $type = $t['match_type'] ?? null;
            $min = self::toNullableFloat($t['min_value'] ?? null);
            $max = self::toNullableFloat($t['max_value'] ?? null);
            if ($type === self::MATCH_EQ && $min !== null) {
                $parts[] = $score . '=' . self::formatFixedNumber($min, $decimals);
            } elseif ($type === self::MATCH_GTE && $min !== null) {
                $parts[] = $score . '=> ' . self::formatFixedNumber($min, $decimals);
            } elseif ($type === self::MATCH_LTE && $max !== null) {
                $parts[] = $score . '=< ' . self::formatFixedNumber($max, $decimals);
            } elseif ($type === self::MATCH_BETWEEN && $min !== null && $max !== null) {
                $parts[] = $score . '=' . self::formatFixedNumber($min, $decimals) . ' – ' . self::formatFixedNumber($max, $decimals);
            }
        }
        return mb_substr(implode(' / ', $parts), 0, 255);
    }

    /**
     * Format angka dengan jumlah desimal seragam (tetap menampilkan nol ekor
     * bila desimal > 0, mis. "1.00").
     */
    public static function formatFixedNumber($value, int $decimals): string
    {
        return number_format((float) $value, max(0, $decimals), '.', '');
    }

    /**
     * Label satu tier Custom untuk tampilan, contoh: "= 7", "> 5", "< 3",
     * "5.00 – 7.00" (desimal seragam bila dipakai).
     *
     * @param array{score?:int,match_type:?string,min_value:?float,max_value:?float} $tier
     */
    public static function customTierLabel(array $tier, ?int $decimals = null): string
    {
        $type = $tier['match_type'] ?? null;
        $min = self::toNullableFloat($tier['min_value'] ?? null);
        $max = self::toNullableFloat($tier['max_value'] ?? null);
        if ($decimals === null) {
            $decimals = self::customScaleDecimals([$tier]);
        }
        if ($type === self::MATCH_EQ && $min !== null) {
            return '= ' . self::formatFixedNumber($min, $decimals);
        }
        if ($type === self::MATCH_GTE && $min !== null) {
            return '> ' . self::formatFixedNumber($min, $decimals);
        }
        if ($type === self::MATCH_LTE && $max !== null) {
            return '< ' . self::formatFixedNumber($max, $decimals);
        }
        if ($type === self::MATCH_BETWEEN && $min !== null && $max !== null) {
            return self::formatFixedNumber($min, $decimals) . ' – ' . self::formatFixedNumber($max, $decimals);
        }
        return '';
    }

    /**
     * Gabungkan skor dengan label agar operator "=" tidak dobel pada kolom
     * Target, contoh: "1 => 7", "2 = 3", "5 =< 3", "3 = 5 – 6", "1 = Baik".
     */
    public static function targetLine($score, string $label): string
    {
        if ($label !== '' && ($label[0] === '>' || $label[0] === '<')) {
            return $score . ' =' . $label;
        }
        if ($label !== '' && $label[0] === '=') {
            return $score . ' ' . $label;
        }
        return $score . ' = ' . $label;
    }

    /**
     * Parse ringkasan skala Range mode Custom:
     *   1 =< 1 / 2 = 1 - 3 / 3 = 3 - 5 / 5 => 5 / 2 = 7
     * Operator: => (x > a), =< (x < a), = (tepat), a - b (a <= x <= b).
     * Presisi desimal seragam mengikuti angka terbanyak.
     *
     * @return array{custom:true,tiers:array}|null
     */
    public static function parseCustomTargetSummary(string $text): ?array
    {
        $text = trim($text);
        if ($text === '') {
            return null;
        }
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        if (strpos($text, "\n") === false && strpos($text, ' / ') !== false) {
            $text = str_replace(' / ', "\n", $text);
        }

        $rows = [];
        foreach (preg_split('/\n+/', $text) as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            if (preg_match('/^([1-5])\s*=\s*(.+)$/u', $line, $m) !== 1) {
                return null;
            }
            $score = (int) $m[1];
            if (isset($rows[$score])) {
                return null;
            }
            $expr = trim($m[2]);

            if (preg_match('/^=>\s*(.+)$/u', $expr, $mm) === 1) {
                $v = self::toFloat($mm[1]);
                if ($v === null) {
                    return null;
                }
                $rows[$score] = ['score' => $score, 'match_type' => self::MATCH_GTE, 'min_value' => $v, 'max_value' => null];
                continue;
            }
            if (preg_match('/^=<\s*(.+)$/u', $expr, $mm) === 1) {
                $v = self::toFloat($mm[1]);
                if ($v === null) {
                    return null;
                }
                $rows[$score] = ['score' => $score, 'match_type' => self::MATCH_LTE, 'min_value' => null, 'max_value' => $v];
                continue;
            }
            if (preg_match('/^(?:>=|≥|>)\s*(.+)$/u', $expr, $mm) === 1) {
                $v = self::toFloat($mm[1]);
                if ($v === null) {
                    return null;
                }
                $rows[$score] = ['score' => $score, 'match_type' => self::MATCH_GTE, 'min_value' => $v, 'max_value' => null];
                continue;
            }
            if (preg_match('/^(?:<=|≤|<)\s*(.+)$/u', $expr, $mm) === 1) {
                $v = self::toFloat($mm[1]);
                if ($v === null) {
                    return null;
                }
                $rows[$score] = ['score' => $score, 'match_type' => self::MATCH_LTE, 'min_value' => null, 'max_value' => $v];
                continue;
            }
            $range = self::parseRangeExpr($expr);
            if ($range !== null) {
                if ($range[0] >= $range[1]) {
                    return null;
                }
                $rows[$score] = ['score' => $score, 'match_type' => self::MATCH_BETWEEN, 'min_value' => $range[0], 'max_value' => $range[1]];
                continue;
            }
            $v = self::toFloat($expr);
            if ($v === null) {
                return null;
            }
            $rows[$score] = ['score' => $score, 'match_type' => self::MATCH_EQ, 'min_value' => $v, 'max_value' => $v];
        }

        if (empty($rows)) {
            return null;
        }
        $rows = array_values($rows);
        if (self::validateCustomRows($rows) !== null) {
            return null;
        }
        return ['custom' => true, 'tiers' => self::buildCustomTiers($rows)];
    }

    /**
     * Parse ringkasan skala Range mode teks `==` (5 label) dari kolom Target:
     *   1 = "Tidak submit" / 2 = "Hitam" / ... / 5 = "Biru"
     * Label boleh dibungkus kutip lurus/curly; label tanpa kutip diterima
     * selama bukan angka, beroperator, atau pola rentang (agar format
     * numerik yang salah tulis tidak ikut tertelan). Wajib skor 1-5 lengkap.
     *
     * @return array{text:true,labels:string[]}|null
     */
    private static function parseTextTargetSummary(string $text): ?array
    {
        $text = trim($text);
        if ($text === '') {
            return null;
        }
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        if (strpos($text, "\n") === false && strpos($text, ' / ') !== false) {
            $text = str_replace(' / ', "\n", $text);
        }

        $byScore = [];
        foreach (preg_split('/\n+/', $text) as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            if (preg_match('/^([1-5])\s*=\s*(.+)$/u', $line, $m) !== 1) {
                return null;
            }
            $score = (int) $m[1];
            if (isset($byScore[$score])) {
                return null;
            }
            $label = self::textLabelFromExpr(trim($m[2]));
            if ($label === null) {
                return null;
            }
            $byScore[$score] = $label;
        }

        if (count($byScore) !== 5) {
            return null;
        }
        ksort($byScore);
        $tiers = self::buildTextTiers(array_values($byScore));
        if (empty($tiers)) {
            return null;
        }
        return [
            'text' => true,
            'labels' => array_map(fn ($t) => (string) $t['label'], $tiers),
        ];
    }

    /**
     * Ambil label teks dari ekspresi mode `==`. Nilai berkutip selalu label;
     * tanpa kutip hanya label non-angka/non-operator/non-rentang.
     */
    private static function textLabelFromExpr(string $expr): ?string
    {
        $quoted = self::stripWrappingQuotes($expr);
        if ($quoted !== null) {
            $quoted = trim($quoted);
            return $quoted === '' ? null : $quoted;
        }

        $expr = trim($expr);
        if ($expr === '' || preg_match('/^[<>=]/u', $expr) === 1) {
            return null;
        }
        if (self::toFloat($expr) !== null || self::parseRangeExpr($expr) !== null) {
            return null;
        }
        return $expr;
    }

    /**
     * Buang pasangan kutip pembungkus (lurus atau curly); null bila tidak
     * dibungkus kutip.
     */
    private static function stripWrappingQuotes(string $expr): ?string
    {
        $expr = trim($expr);
        $pairs = [
            ['"', '"'],
            ["'", "'"],
            ['“', '”'],
            ['‘', '’'],
        ];
        $len = mb_strlen($expr);
        if ($len < 2) {
            return null;
        }
        $first = mb_substr($expr, 0, 1);
        $last = mb_substr($expr, -1);
        foreach ($pairs as $pair) {
            if ($first === $pair[0] && $last === $pair[1]) {
                return mb_substr($expr, 1, $len - 2);
            }
        }
        return null;
    }

    /**
     * Pecah ekspresi rentang "a - b" menjadi [lo, hi]. Menerima dash dengan
     * atau tanpa spasi (termasuk en/em dash dan minus U+2212), desimal
     * koma/titik, persen, tanda kurung, dan angka negatif (mis. "-6--5").
     *
     * @return array{0:float,1:float}|null
     */
    private static function parseRangeExpr(string $expr): ?array
    {
        $expr = trim($expr);
        if ($expr === '') {
            return null;
        }

        $parts = preg_split('/\s+[–—\-]\s+/u', $expr);
        if (count($parts) === 2) {
            $lo = self::toFloat($parts[0]);
            $hi = self::toFloat($parts[1]);
            return ($lo === null || $hi === null) ? null : [$lo, $hi];
        }

        $normalized = str_replace(['–', '—', '−'], '-', $expr);
        $len = strlen($normalized);
        for ($i = 1; $i < $len; $i++) {
            if ($normalized[$i] !== '-') {
                continue;
            }
            $prev = $normalized[$i - 1];
            if ($prev === '-' || $prev === '(') {
                continue;
            }
            $lo = self::toFloat(substr($normalized, 0, $i));
            $hi = self::toFloat(substr($normalized, $i + 1));
            if ($lo !== null && $hi !== null) {
                return [$lo, $hi];
            }
        }

        return null;
    }

    private static function toNullableFloat($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }
        $text = trim((string) $value);
        if ($text === '') {
            return null;
        }
        return self::toFloat($text);
    }

    /**
     * Konversi teks angka (desimal koma/titik, sufiks % opsional, boleh
     * dibungkus kurung untuk angka negatif) ke float.
     */
    private static function toFloat(string $value): ?float
    {
        $value = trim($value);
        if (preg_match('/^\(\s*(.+?)\s*\)$/u', $value, $m) === 1) {
            $value = trim($m[1]);
        }
        $value = preg_replace('/\s*%\s*$/', '', $value);
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }
        if (strpos($value, ',') !== false && strpos($value, '.') !== false) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        } elseif (strpos($value, ',') !== false) {
            $value = str_replace(',', '.', $value);
        }
        if (!is_numeric($value)) {
            return null;
        }
        return (float) $value;
    }
}
