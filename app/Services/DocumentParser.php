<?php

namespace App\Services;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser;

class DocumentParser
{
    /**
     * Parse content from an uploaded file or raw content.
     */
    public function parse(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $mime = $file->getMimeType();
        $content = '';

        try {
            if ($extension === 'pdf' || $mime === 'application/pdf') {
                $content = $this->parsePdf($file->getRealPath());
            } else {
                $content = file_get_contents($file->getRealPath());
            }
        } catch (Exception $e) {
            Log::error('Document parsing failed', [
                'file' => $file->getClientOriginalName(),
                'error' => $e->getMessage(),
            ]);
            throw new Exception('Failed to extract text from file: '.$e->getMessage());
        }

        return $this->cleanContent($content);
    }

    /**
     * Parse PDF content from a string (useful for Telegram).
     */
    public function parsePdfFromContent(string $content): string
    {
        try {
            $parser = new Parser;
            $pdf = $parser->parseContent($content);

            return $this->cleanContent($pdf->getText());
        } catch (Exception $e) {
            Log::error('PDF content parsing failed', ['error' => $e->getMessage()]);
            throw new Exception('Failed to extract text from PDF content.');
        }
    }

    /**
     * Parse PDF file directly.
     */
    protected function parsePdf(string $path): string
    {
        $parser = new Parser;
        $pdf = $parser->parseFile($path);

        return $pdf->getText();
    }

    /**
     * Clean and normalize content to valid UTF-8.
     */
    public function cleanContent(string $content): string
    {
        // 1. Detect encoding and convert to UTF-8 if necessary
        $encoding = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'ASCII', 'UTF-16', 'UTF-32'], true);

        if ($encoding !== 'UTF-8') {
            $content = mb_convert_encoding($content, 'UTF-8', $encoding ?: 'auto');
        }

        // 2. Remove non-printable characters and malformed UTF-8 characters
        // This regex keeps standard whitespace and printable ASCII + common extended characters
        $content = preg_replace('/[^\x20-\x7E\t\r\n\x80-\xFF]/', '', $content);

        // 3. Final conversion to strip any remaining invalid UTF-8 sequences
        $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8');

        return trim($content);
    }
}
