<?php

namespace App\Support;

class PaScaleNormalizer
{
    public const DEFAULT_DISTRIBUTION = [
        'E' => 0.05,
        'D' => 0.10,
        'C' => 0.65,
        'B' => 0.15,
        'A' => 0.05,
    ];

    public const DISTRIBUTION = self::DEFAULT_DISTRIBUTION;

    public const CUTOFFS = [
        'A' => 0.95,
        'B' => 0.80,
        'C' => 0.15,
        'D' => 0.05,
    ];

    public const MIN_N = 3;

    /**
     * Fraksi per grade valid? Wajib lengkap (E..A), tiap nilai 0..1, total ~1.
     */
    public static function isValidDistribution($dist): bool
    {
        if (!is_array($dist) || !$dist) {
            return false;
        }

        $sum = 0.0;
        foreach (array_keys(self::DEFAULT_DISTRIBUTION) as $grade) {
            if (!array_key_exists($grade, $dist)) {
                return false;
            }
            $v = (float) $dist[$grade];
            if ($v < 0.0 || $v > 1.0) {
                return false;
            }
            $sum += $v;
        }

        return abs($sum - 1.0) <= 0.0001;
    }

    /**
     * Distribusi efektif: pakai override bila valid, selain itu default.
     */
    public static function distribution(?array $dist = null): array
    {
        return self::isValidDistribution($dist) ? $dist : self::DEFAULT_DISTRIBUTION;
    }

    /**
     * Cut-off kumulatif (lower-tail) dari persen grade:
     * A = 1 - A%, B = 1 - (A+B)%, C = E+D%, D = E%.
     */
    public static function cutoffsFromDistribution(?array $dist = null): array
    {
        $d = self::distribution($dist);

        return [
            'A' => round(1.0 - (float) $d['A'], 6),
            'B' => round(1.0 - (float) $d['A'] - (float) $d['B'], 6),
            'C' => round((float) $d['E'] + (float) $d['D'], 6),
            'D' => round((float) $d['E'], 6),
        ];
    }

    public static function grades(): array
    {
        return array_keys(self::DISTRIBUTION);
    }

    public static function emptyCounts(): array
    {
        return array_fill_keys(self::grades(), 0);
    }

    public static function percentages(?array $dist = null): array
    {
        $out = [];
        foreach (self::distribution($dist) as $grade => $ratio) {
            $out[$grade] = (int) round($ratio * 100);
        }

        return $out;
    }

    public static function expectedCounts(int $n, ?array $dist = null): array
    {
        $out = [];
        foreach (self::distribution($dist) as $grade => $ratio) {
            $out[$grade] = round($n * $ratio, 1);
        }

        return $out;
    }

    /**
     * Kebalikan CDF normal (z) untuk persentil kumulatif tertentu,
     * dihitung dengan bisection memakai normalCdf().
     */
    public static function zForCumulative(float $p): float
    {
        $lo = -10.0;
        $hi = 10.0;

        for ($i = 0; $i < 60; $i++) {
            $mid = ($lo + $hi) / 2.0;
            if (self::normalCdf($mid) < $p) {
                $lo = $mid;
            } else {
                $hi = $mid;
            }
        }

        return ($lo + $hi) / 2.0;
    }

    public static function zCutoffs(?array $dist = null): array
    {
        $out = [];
        foreach (self::cutoffsFromDistribution($dist) as $grade => $p) {
            $out[$grade] = round(self::zForCumulative($p), 4);
        }

        return $out;
    }

    /**
     * Nilai batas grade pada skala skor: rata-rata + (z x SD).
     */
    public static function cutoffScores(float $mean, float $sd, ?array $dist = null): array
    {
        $out = [];
        foreach (self::cutoffsFromDistribution($dist) as $grade => $p) {
            $out[$grade] = round($mean + (self::zForCumulative($p) * $sd), 2);
        }

        return $out;
    }

    /**
     * @param  array<int, float|int>  $scores
     * @return array{applied: bool, reason: ?string, mean: ?float, sd: ?float, sum: ?float, ss: ?float, cutoffs: array<string, float>, z: array<string, float>, grades: array<int, string>}
     */
    public static function normalize(array $scores, ?array $dist = null, ?int $minN = null): array
    {
        $scores = array_values(array_map('floatval', $scores));
        $n = count($scores);
        $minN = $minN === null ? self::MIN_N : max(1, $minN);

        $out = [
            'applied' => false,
            'reason' => null,
            'mean' => null,
            'sd' => null,
            'sum' => null,
            'ss' => null,
            'cutoffs' => [],
            'z' => [],
            'grades' => [],
        ];

        if ($n < $minN) {
            $out['reason'] = 'min_n';

            return $out;
        }

        $mean = array_sum($scores) / $n;
        $sd = self::sampleSd($scores, $mean);

        $out['mean'] = round($mean, 4);
        $out['sd'] = round($sd, 4);

        if ($sd <= 0.0) {
            $out['reason'] = 'sd_zero';

            return $out;
        }

        $grades = [];
        foreach ($scores as $i => $score) {
            $z = ($score - $mean) / $sd;
            $grades[$i] = self::gradeForPercentile(self::normalCdf($z), $dist);
        }

        $ss = 0.0;
        foreach ($scores as $score) {
            $diff = $score - $mean;
            $ss += $diff * $diff;
        }

        $out['applied'] = true;
        $out['sum'] = round(array_sum($scores), 2);
        $out['ss'] = round($ss, 4);
        $out['cutoffs'] = self::cutoffScores($mean, $sd, $dist);
        $out['z'] = self::zCutoffs($dist);
        $out['grades'] = $grades;

        return $out;
    }

    public static function gradeForPercentile(float $p, ?array $dist = null): string
    {
        foreach (self::cutoffsFromDistribution($dist) as $grade => $cutoff) {
            if ($p >= $cutoff) {
                return $grade;
            }
        }

        return 'E';
    }

    public static function sampleSd(array $scores, ?float $mean = null): float
    {
        $n = count($scores);
        if ($n < 2) {
            return 0.0;
        }

        if ($mean === null) {
            $mean = array_sum($scores) / $n;
        }

        $sum = 0.0;
        foreach ($scores as $score) {
            $diff = (float) $score - $mean;
            $sum += $diff * $diff;
        }

        return sqrt($sum / ($n - 1));
    }

    /**
     * Aproksimasi Abramowitz & Stegun 26.2.17 (|error| < 7.5e-8).
     */
    public static function normalCdf(float $z): float
    {
        if ($z < 0.0) {
            return 1.0 - self::normalCdf(-$z);
        }

        $t = 1.0 / (1.0 + 0.2316419 * $z);
        $poly = $t * (0.319381530 + $t * (-0.356563782 + $t * (1.781477937 + $t * (-1.821255978 + $t * 1.330274429))));
        $pdf = exp(-0.5 * $z * $z) / sqrt(2.0 * M_PI);

        return 1.0 - $pdf * $poly;
    }

    public static function summarize(array $grades): array
    {
        $counts = self::emptyCounts();
        foreach ($grades as $grade) {
            if ($grade !== null && array_key_exists($grade, $counts)) {
                $counts[$grade]++;
            }
        }

        return $counts;
    }

    /**
     * Kuota integer per grade (key E..A, total = N) memakai metode sisa terbesar:
     * tiap kuota dibulatkan ke bawah, kursi sisa dibagikan ke pecahan terbesar.
     * Bila pecahan seri, grade yang lebih tinggi diprioritaskan.
     *
     * @return array<string, int>
     */
    public static function largestRemainderQuota(int $n, ?array $dist = null): array
    {
        $d = self::distribution($dist);
        $grades = ['A', 'B', 'C', 'D', 'E'];
        $priority = ['A' => 4, 'B' => 3, 'C' => 2, 'D' => 1, 'E' => 0];

        $quota = [];
        $remainder = [];
        foreach ($grades as $grade) {
            $exact = max(0, $n) * (float) $d[$grade];
            $quota[$grade] = (int) floor($exact);
            $remainder[$grade] = $exact - $quota[$grade];
        }

        $left = max(0, $n) - array_sum($quota);
        $order = $grades;
        usort($order, function ($a, $b) use ($remainder, $priority) {
            $cmp = $remainder[$b] <=> $remainder[$a];
            if ($cmp !== 0) {
                return $cmp;
            }

            return $priority[$b] <=> $priority[$a];
        });

        $i = 0;
        while ($left > 0) {
            $quota[$order[$i % count($order)]]++;
            $left--;
            $i++;
        }

        return [
            'E' => $quota['E'],
            'D' => $quota['D'],
            'C' => $quota['C'],
            'B' => $quota['B'],
            'A' => $quota['A'],
        ];
    }

    /**
     * Grade PA berbasis ranking (forced distribution) dari daftar skor:
     * urut menurun, potong sesuai kuota sisa terbesar, dan bila skor di batas
     * grade seri maka seluruh nilai seri tersebut naik ke grade atas.
     * Index hasil paralel dengan index $scores.
     *
     * @param  array<int, float|int>  $scores
     * @return array<int, string>
     */
    public static function rankGrades(array $scores, ?array $dist = null): array
    {
        $scores = array_values(array_map('floatval', $scores));
        $n = count($scores);
        if ($n === 0) {
            return [];
        }

        $order = range(0, $n - 1);
        usort($order, function ($a, $b) use ($scores) {
            $cmp = $scores[$b] <=> $scores[$a];
            if ($cmp !== 0) {
                return $cmp;
            }

            return $a <=> $b;
        });

        $quota = self::largestRemainderQuota($n, $dist);
        $grades = array_fill(0, $n, 'E');

        $i = 0;
        foreach (['A', 'B', 'C', 'D'] as $grade) {
            $end = min($i + $quota[$grade], $n);
            while ($end > 0 && $end < $n && $scores[$order[$end]] == $scores[$order[$end - 1]]) {
                $end++;
            }
            for ($k = $i; $k < $end; $k++) {
                $grades[$order[$k]] = $grade;
            }
            $i = $end;
        }

        return $grades;
    }
}
