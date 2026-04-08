<?php

namespace App\Services;

class TextChunker
{
    public function split(string $text, int $targetLength = 1200, int $overlap = 180): array
    {
        $normalized = preg_replace('/\R{3,}/', "\n\n", trim($text)) ?? '';

        if ($normalized === '') {
            return [];
        }

        if (mb_strlen($normalized) <= $targetLength) {
            return [$normalized];
        }

        $paragraphs = preg_split('/\n\s*\n/', $normalized) ?: [];
        $chunks = [];
        $buffer = '';

        foreach ($paragraphs as $paragraph) {
            $paragraph = trim($paragraph);

            if ($paragraph === '') {
                continue;
            }

            $candidate = trim($buffer === '' ? $paragraph : "{$buffer}\n\n{$paragraph}");

            if (mb_strlen($candidate) <= $targetLength) {
                $buffer = $candidate;
                continue;
            }

            if ($buffer !== '') {
                $chunks[] = $buffer;
                $buffer = $this->withOverlap($buffer, $paragraph, $overlap);
                continue;
            }

            foreach ($this->splitLongParagraph($paragraph, $targetLength, $overlap) as $part) {
                $chunks[] = $part;
            }
        }

        if ($buffer !== '') {
            $chunks[] = $buffer;
        }

        return array_values(array_filter($chunks));
    }

    private function splitLongParagraph(string $paragraph, int $targetLength, int $overlap): array
    {
        $parts = [];
        $start = 0;
        $length = mb_strlen($paragraph);

        while ($start < $length) {
            $slice = mb_substr($paragraph, $start, $targetLength);
            $parts[] = trim($slice);

            if (($start + $targetLength) >= $length) {
                break;
            }

            $start += max(1, $targetLength - $overlap);
        }

        return $parts;
    }

    private function withOverlap(string $previousChunk, string $nextParagraph, int $overlap): string
    {
        $tail = trim(mb_substr($previousChunk, max(0, mb_strlen($previousChunk) - $overlap)));

        return trim($tail === '' ? $nextParagraph : "{$tail}\n\n{$nextParagraph}");
    }
}
