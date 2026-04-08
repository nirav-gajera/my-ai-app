<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Services\RagService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function store(Request $request, Conversation $conversation, RagService $rag)
    {
        abort_unless($conversation->user_id === $request->user()->id, 404);

        $validated = $request->validate([
            'question' => ['required', 'string', 'min:3', 'max:5000'],
        ]);

        $result = $rag->answer($conversation, $validated['question']);

        return response()->json([
            'conversation' => [
                'id' => $result['conversation']->id,
                'title' => $result['conversation']->title,
                'last_message_at' => optional($result['conversation']->last_message_at)->toIso8601String(),
            ],
            'user_message' => [
                'id' => $result['user_message']->id,
                'role' => $result['user_message']->role,
                'content' => $result['user_message']->content,
                'created_at' => optional($result['user_message']->created_at)->toIso8601String(),
            ],
            'assistant_message' => [
                'id' => $result['assistant_message']->id,
                'role' => $result['assistant_message']->role,
                'content' => $result['assistant_message']->content,
                'citations' => $result['assistant_message']->citations ?? [],
                'created_at' => optional($result['assistant_message']->created_at)->toIso8601String(),
            ],
        ]);
    }
}
