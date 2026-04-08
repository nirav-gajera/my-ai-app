<?php

namespace App\Services;

class SimilarityService
{
    public function cosine(array $left, array $right): float
    {
        $count = min(count($left), count($right));

        if ($count === 0) {
            return 0.0;
        }

        $dot = 0.0;
        $leftNorm = 0.0;
        $rightNorm = 0.0;

        for ($index = 0; $index < $count; $index++) {
            $dot += $left[$index] * $right[$index];
            $leftNorm += $left[$index] ** 2;
            $rightNorm += $right[$index] ** 2;
        }

        if ($leftNorm <= 0.0 || $rightNorm <= 0.0) {
            return 0.0;
        }

        return $dot / (sqrt($leftNorm) * sqrt($rightNorm));
    }
}
