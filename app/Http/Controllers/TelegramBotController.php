<?php

namespace App\Http\Controllers;

use App\Models\TelegramBot;
use Illuminate\Http\Request;

class TelegramBotController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 10);
        $search = $request->input('search');
        $status = $request->input('status');

        $bots = TelegramBot::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('bot_username', 'like', "%{$search}%");
                });
            })
            ->when($status !== null, function ($query) use ($status) {
                if ($status === 'active') {
                    $query->where('is_active', true);
                } elseif ($status === 'inactive') {
                    $query->where('is_active', false);
                }
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.telegram-bots.index', compact('bots'));
    }

    public function create()
    {
        $hasActiveBot = TelegramBot::active()->exists();

        return view('admin.telegram-bots.create', compact('hasActiveBot'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'token' => 'required|string|max:500',
            'bot_username' => 'required|string|max:255',
            'bot_url' => 'nullable|string|url|max:500',
            'is_active' => 'nullable|boolean',
        ]);

        $bot = TelegramBot::create([
            'name' => $validated['name'],
            'token' => $validated['token'],
            'bot_username' => $validated['bot_username'],
            'bot_url' => $validated['bot_url'] ?? null,
            'is_active' => false,
        ]);

        if ($request->boolean('is_active')) {
            $bot->activate();
        }

        return redirect()->route('admin.telegram-bots.index')
            ->with('success', 'Telegram bot created successfully.');
    }

    public function edit(TelegramBot $telegramBot)
    {
        $hasOtherActiveBot = TelegramBot::active()
            ->where('id', '!=', $telegramBot->id)
            ->exists();

        return view('admin.telegram-bots.edit', compact('telegramBot', 'hasOtherActiveBot'));
    }

    public function update(Request $request, TelegramBot $telegramBot)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'token' => 'nullable|string|max:500',
            'bot_username' => 'required|string|max:255',
            'bot_url' => 'nullable|string|url|max:500',
            'is_active' => 'nullable|boolean',
        ]);

        $telegramBot->update([
            'name' => $validated['name'],
            'token' => $validated['token'] ?: $telegramBot->token,
            'bot_username' => $validated['bot_username'],
            'bot_url' => $validated['bot_url'] ?? null,
        ]);

        if ($request->boolean('is_active')) {
            $telegramBot->activate();
        } else {
            $telegramBot->deactivate();
        }

        return redirect()->route('admin.telegram-bots.index')
            ->with('success', 'Telegram bot updated successfully.');
    }

    public function destroy(TelegramBot $telegramBot)
    {
        $telegramBot->delete();

        return redirect()->route('admin.telegram-bots.index')
            ->with('success', 'Telegram bot deleted successfully.');
    }
}
