<?php

namespace App\Services;

use App\Models\KnowledgeDocument;
use App\Models\TelegramBot;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramService
{
    protected RagService $ragService;

    protected ?TelegramBot $activeBot;

    public function __construct(RagService $ragService)
    {
        $this->ragService = $ragService;
        $this->activeBot = TelegramBot::getActive();

        if ($this->activeBot) {
            Telegram::setAccessToken($this->activeBot->token);
        }
    }

    /**
     * Send a message to a chat
     */
    public function sendMessage(int $chatId, string $text): array|object
    {
        try {
            $response = Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => $this->convertMarkdownToHtml($text),
                'parse_mode' => 'HTML',
            ]);

            return is_object($response) && method_exists($response, 'toArray') ? $response->toArray() : (array) $response;
        } catch (\Exception $e) {
            Log::error('Telegram API error', [
                'message' => $e->getMessage(),
                'chat_id' => $chatId,
            ]);

            return [];
        }
    }

    /**
     * Convert Markdown to Telegram-compatible HTML
     */
    protected function convertMarkdownToHtml(string $text): string
    {
        // 1. Escape basic HTML chars first (essential for Telegram HTML mode)
        $text = str_replace(['&', '<', '>'], ['&amp;', '&lt;', '&gt;'], $text);

        // 2. Convert Bold: **text** -> <b>text</b>
        $text = preg_replace('/\*\*(.*?)\*\*/', '<b>$1</b>', $text);

        // 3. Convert Italic: *text* (only if not bold) -> <i>text</i>
        // This regex avoids matching double asterisks
        $text = preg_replace('/(?<!\*)\*(?!\*)(.*?)\*/', '<i>$1</i>', $text);

        // 4. Convert Code: `text` -> <code>text</code>
        $text = preg_replace('/`(.*?)`/', '<code>$1</code>', $text);

        // 5. Convert Bullet Points: "* " or "- " at start of line -> "• "
        $text = preg_replace('/^\s*[\*\-•]\s+/m', '• ', $text);

        return $text;
    }

    /**
     * Set webhook for receiving updates
     */
    public function setWebhook(string $url): bool|object
    {
        return Telegram::setWebhook([
            'url' => $url,
        ]);
    }

    /**
     * Send chat action (e.g. typing)
     */
    public function sendChatAction(int $chatId, string $action = 'typing'): bool|object
    {
        return Telegram::sendChatAction([
            'chat_id' => $chatId,
            'action' => $action,
        ]);

    }

    /**
     * Get file info from Telegram
     */
    public function getFile(string $fileId): array
    {
        $file = Telegram::getFile([
            'file_id' => $fileId,
        ]);

        return [
            'ok' => true,
            'result' => [
                'file_id' => $file->fileId,
                'file_unique_id' => $file->fileUniqueId,
                'file_size' => $file->fileSize,
                'file_path' => $file->filePath,
            ],
        ];
    }

    /**
     * Get updates (for polling if needed)
     */
    public function getUpdates(int $offset = 0): array
    {
        $updates = Telegram::getUpdates([
            'offset' => $offset,
        ]);

        return collect($updates)->map(fn ($update) => $update->toArray())->toArray();
    }

    /**
     * Process incoming message
     */
    public function processMessage(array $message): void
    {
        if (! $this->activeBot) {
            Log::warning('Telegram message received but no active bot is configured. Ignoring.');

            return;
        }

        $chatId = $message['chat']['id'];
        $text = $message['text'] ?? '';
        $document = $message['document'] ?? null;

        Log::info('Processing Telegram message', [
            'chat_id' => $chatId,
            'has_text' => ! empty($text),
            'has_document' => ! empty($document),
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

                    $this->sendMessage($chatId, "✅ *Success!* Your account has been linked.\n\n".
                        'You can now chat with your knowledge base directly from here.');

                    return;
                }
            }

            // Normal start
            $user = User::where('telegram_chat_id', $chatId)->first();
            if ($user) {
                $this->sendMessage($chatId, "👋 *Welcome back, {$user->name}!*\n\n".
                    'How can I help you today?');
            } else {
                $this->sendMessage($chatId, "👋 *Welcome!*\n\n".
                    "To link your account, please go to your profile on the website and click the 'Link Telegram' button.\n\n".
                    "Your Chat ID: `{$chatId}`");
            }

            return;
        }

        // Find user
        $user = User::where('telegram_chat_id', $chatId)
            ->where('telegram_enabled', true)
            ->first();

        if (! $user) {
            $this->sendMessage($chatId, "❌ *Account not linked.*\n\n".
                'Please link your account from your profile settings.');

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

        // Handle inline commands
        if (trim($text) === '/list') {
            $this->handleListCommand($user, $chatId);

            return;
        }

        // Generate response using RAG (stateless)
        try {
            $response = $this->ragService->statelessAnswer($user->id, $text);
            $this->sendMessage($chatId, $response);
        } catch (\Exception $e) {
            Log::error('Error in Telegram RAG processing', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'chat_id' => $chatId,
            ]);
            $this->sendMessage($chatId, '❌ Sorry, I encountered an error while processing your request. Please try again later.');
        }
    }

    /**
     * Handle the /list command to show user's indexed documents
     */
    protected function handleListCommand(User $user, int $chatId): void
    {
        $documents = KnowledgeDocument::forUser($user->id)
            ->latest()
            ->get();

        if ($documents->isEmpty()) {
            $this->sendMessage($chatId, "🗂 *Your Indexed Documents*\n\nYou don't have any documents indexed yet. You can upload files directly here or paste text in the dashboard to get started.");

            return;
        }

        $message = "🗂 *Your Indexed Documents*\n\n";
        foreach ($documents as $index => $doc) {
            $num = $index + 1;
            $type = strtoupper($doc->source_type);
            $message .= "{$num}. *{$doc->title}* ({$type})\n";
            if ($doc->source_type !== 'text') {
                $message .= "   └ `{$doc->source_name}`\n";
            }
        }

        $message .= "\n_To index a new document, simply upload a file to this chat._";

        $this->sendMessage($chatId, $message);
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
        if (! isset($fileInfo['result']['file_path'])) {
            $this->sendMessage($user->telegram_chat_id, '❌ Failed to retrieve file info.');

            return;
        }

        $filePath = $fileInfo['result']['file_path'];
        $downloadUrl = "https://api.telegram.org/file/bot{$this->activeBot->token}/{$filePath}";

        try {
            $response = Http::get($downloadUrl);
            $content = $response->body();

            // If it's a PDF, parse it
            if ($mimeType === 'application/pdf' || str_ends_with(strtolower($fileName), '.pdf')) {
                $parser = new Parser;
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
                throw new \Exception('The document appears to be empty or contains no readable text.');
            }

            $this->ragService->ingest(
                $user->id,
                $fileName,
                $content,
                $fileName,
                'telegram'
            );

            $this->sendMessage($user->telegram_chat_id, '✅ *Document ingested successfully!* You can now ask questions about it.');
        } catch (\Exception $e) {
            Log::error('Telegram document ingestion failed', ['error' => $e->getMessage(), 'file' => $fileName]);
            $this->sendMessage($user->telegram_chat_id, '❌ Error processing document: '.$e->getMessage());
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
