<?php

namespace App\Services;

use App\Models\User;
use App\Services\RagService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected string $apiKey;
    protected string $baseUrl;
    protected RagService $ragService;

    public function __construct(RagService $ragService)
    {
        $this->apiKey = config('services.telegram.api_key');
        $this->baseUrl = "https://api.telegram.org/bot{$this->apiKey}";
        $this->ragService = $ragService;
    }

    /**
     * Send a message to a chat
     */
    public function sendMessage(int $chatId, string $text): array
    {
        $response = Http::post("{$this->baseUrl}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'Markdown',
        ]);

        return $response->json();
    }

    /**
     * Set webhook for receiving updates
     */
    public function setWebhook(string $url): array
    {
        $response = Http::post("{$this->baseUrl}/setWebhook", [
            'url' => $url,
        ]);

        return $response->json();
    }

    /**
     * Send chat action (e.g. typing)
     */
    public function sendChatAction(int $chatId, string $action = 'typing'): array
    {
        $response = Http::post("{$this->baseUrl}/sendChatAction", [
            'chat_id' => $chatId,
            'action' => $action,
        ]);

        return $response->json();
    }

    /**
     * Get file info from Telegram
     */
    public function getFile(string $fileId): array
    {
        $response = Http::get("{$this->baseUrl}/getFile", [
            'file_id' => $fileId,
        ]);

        return $response->json();
    }

    /**
     * Get updates (for polling if needed)
     */
    public function getUpdates(int $offset = 0): array
    {
        $response = Http::get("{$this->baseUrl}/getUpdates", [
            'offset' => $offset,
        ]);

        return $response->json();
    }

    /**
     * Process incoming message
     */
    public function processMessage(array $message): void
    {
        $chatId = $message['chat']['id'];
        $text = $message['text'] ?? '';
        $document = $message['document'] ?? null;

        Log::info('Processing Telegram message', [
            'chat_id' => $chatId,
            'has_text' => !empty($text),
            'has_document' => !empty($document)
        ]);

        // Send typing indicator early
        $this->sendChatAction($chatId, 'typing');

        // Handle /start command (with potential token)
        if (str_starts_with($text, '/start')) {
            $parts = explode(' ', $text);
            $token = $parts[1] ?? null;

            if ($token) {
                $user = User::where('telegram_token', $token)->first();
                if ($user) {
                    $user->update([
                        'telegram_chat_id' => $chatId,
                        'telegram_enabled' => true,
                        'telegram_token' => null, // Clear token after use
                    ]);

                    $this->sendMessage($chatId, "✅ *Success!* Your account has been linked.\n\n" .
                        "You can now chat with your knowledge base directly from here.");
                    return;
                }
            }

            // Normal start
            $user = User::where('telegram_chat_id', $chatId)->first();
            if ($user) {
                $this->sendMessage($chatId, "👋 *Welcome back, {$user->name}!*\n\n" .
                    "How can I help you today?");
            } else {
                $this->sendMessage($chatId, "👋 *Welcome!*\n\n" .
                    "To link your account, please go to your profile on the website and click the 'Link Telegram' button.\n\n" .
                    "Your Chat ID: `{$chatId}`");
            }
            return;
        }

        // Find user
        $user = User::where('telegram_chat_id', $chatId)
            ->where('telegram_enabled', true)
            ->first();

        if (!$user) {
            $this->sendMessage($chatId, "❌ *Account not linked.*\n\n" .
                "Please link your account from your profile settings.");
            return;
        }

        // Handle Document Ingestion
        if ($document) {
            $this->handleDocument($user, $document);
            return;
        }

        if (empty($text)) {
            return;
        }

        // Handle Conversation Context
        $conversation = $user->conversations()->where('title', 'Telegram Chat')->first()
            ?? $user->conversations()->create(['title' => 'Telegram Chat']);

        // Generate response using RAG with history
        $result = $this->ragService->answer($conversation, $text);
        $response = $result['assistant_message']['content'] ?? 'I encountered an error processing your request.';

        $this->sendMessage($chatId, $response);
    }

    /**
     * Handle document upload from Telegram
     */
    protected function handleDocument(User $user, array $document): void
    {
        $fileId = $document['file_id'];
        $fileName = $document['file_name'] ?? 'telegram_upload';
        $mimeType = $document['mime_type'] ?? '';

        $this->sendMessage($user->telegram_chat_id, "⏳ *Processing document:* `{$fileName}`...");

        // Get file path
        $fileInfo = $this->getFile($fileId);
        if (!isset($fileInfo['result']['file_path'])) {
            $this->sendMessage($user->telegram_chat_id, "❌ Failed to retrieve file info.");
            return;
        }

        $filePath = $fileInfo['result']['file_path'];
        $downloadUrl = "https://api.telegram.org/file/bot{$this->apiKey}/{$filePath}";

        try {
            $response = Http::get($downloadUrl);
            $content = $response->body();

            // If it's a PDF, parse it
            if ($mimeType === 'application/pdf' || str_ends_with(strtolower($fileName), '.pdf')) {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseContent($content);
                $content = $pdf->getText();
            }

            // Ensure UTF-8 and clean up
            $encoding = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'ASCII'], true);
            if ($encoding !== 'UTF-8') {
                $content = mb_convert_encoding($content, 'UTF-8', $encoding ?: 'auto');
            }
            $content = preg_replace('/[^\x20-\x7E\t\r\n\x80-\xFF]/', '', $content); // Remove non-printable characters

            if (empty(trim($content))) {
                throw new \Exception("The document appears to be empty or contains no readable text.");
            }

            $this->ragService->ingest(
                $user->id,
                $fileName,
                $content,
                $fileName,
                'telegram'
            );

            $this->sendMessage($user->telegram_chat_id, "✅ *Document ingested successfully!* You can now ask questions about it.");
        } catch (\Exception $e) {
            Log::error('Telegram document ingestion failed', ['error' => $e->getMessage(), 'file' => $fileName]);
            $this->sendMessage($user->telegram_chat_id, "❌ Error processing document: " . $e->getMessage());
        }
    }
    /**
     * Generate response using user's knowledge
     */
    protected function generateResponse(User $user, string $text): string
    {
        return $this->ragService->generateResponse($text, $user->id);
    }

    /**
     * Link user to chat ID
     */
    public function linkUser(int $chatId, User $user): bool
    {
        $user->telegram_chat_id = $chatId;
        return $user->save();
    }

    /**
     * Enable/disable Telegram for user
     */
    public function toggleEnabled(User $user, bool $enabled): bool
    {
        $user->telegram_enabled = $enabled;
        return $user->save();
    }
}