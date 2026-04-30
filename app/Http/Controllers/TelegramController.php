<?php

namespace App\Http\Controllers;

use App\Models\TelegramBot;
use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TelegramController extends Controller
{
    protected TelegramService $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    /**
     * Handle Telegram webhook
     */
    public function webhook(Request $request, TelegramBot $bot)
    {
        $data = $request->all();

        Log::info('Telegram webhook received', [
            'bot_id' => $bot->id,
            'is_active' => $bot->is_active,
            'data' => $data,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        if (! $bot->is_active) {
            Log::warning("Telegram webhook received for inactive bot ID: {$bot->id}. Ignoring to prevent errors.");

            return response()->json(['status' => 'ok']);
        }

        if (isset($data['message'])) {
            Log::info('Processing Telegram message', ['message' => $data['message']]);
            $this->telegramService->processMessage($data['message']);
        } else {
            Log::warning('Telegram webhook received without message', ['data' => $data]);
        }

        return response()->json(['status' => 'ok']);
    }

    public function telegramUpdate(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $request->validate([
            'telegram_chat_id' => 'nullable|string|max:255|unique:users,telegram_chat_id,'.$user->id,
            'telegram_enabled' => 'required|boolean',
        ]);

        $user->update([
            'telegram_chat_id' => $request->telegram_chat_id,
            'telegram_enabled' => $request->boolean('telegram_enabled'),
        ]);

        return redirect()->route('profile.edit')
            ->with('status', 'telegram-updated');
    }

    public function generateLink(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $token = bin2hex(random_bytes(16));
        $user->update(['telegram_token' => $token]);

        $activeBot = TelegramBot::getActive();
        $botUsername = $activeBot ? $activeBot->bot_username : 'YourBot';
        $url = "https://t.me/{$botUsername}?start={$token}";

        return redirect($url);
    }

    public function unlink(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $chatId = $user->telegram_chat_id;

        if ($chatId) {
            // Send notification to user on telegram before unlinking
            $this->telegramService->sendMessage($chatId, "⚠️ *Account Unlinked*\n\nYour account has been successfully unlinked from the website. You can re-link at any time from your profile settings or by using the `/start` command with a new link.");
        }

        $user->update([
            'telegram_chat_id' => null,
            'telegram_enabled' => false,
            'telegram_token' => null,
        ]);

        return redirect()->route('profile.edit')
            ->with('status', 'telegram-unlinked');
    }
}
