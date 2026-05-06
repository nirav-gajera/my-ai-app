<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    /** Conversations page (HTML) */
    public function index(Request $request)
    {
        $userId = $request->user()->id;
        $startNew = $request->boolean('new');

        $conversations = Conversation::forUser($userId)
            ->where('title', '!=', 'Telegram Chat')
            ->orderByDesc('last_message_at')
            ->orderByDesc('updated_at')
            ->get();

        // Allow deep-linking to a specific conversation via ?id=
        $selectedId = (int) $request->query('id', 0);
        $selectedConversation = $startNew
            ? null
            : ($selectedId
                ? $conversations->firstWhere('id', $selectedId)
                : $conversations->first());

        if ($selectedConversation) {
            $selectedConversation->load(['messages' => fn ($q) => $q->orderBy('created_at')]);
        }

        return view('conversations', [
            'state' => [
                'conversations' => $conversations->map(fn (Conversation $c) => [
                    'id' => $c->id,
                    'title' => $c->title,
                    'last_message_at' => optional($c->last_message_at)->toIso8601String(),
                    'updated_at' => optional($c->updated_at)->toIso8601String(),
                ])->values(),
                'selectedConversation' => $selectedConversation ? [
                    'id' => $selectedConversation->id,
                    'title' => $selectedConversation->title,
                    'messages' => $selectedConversation->messages->map(fn ($m) => [
                        'id' => $m->id,
                        'role' => $m->role,
                        'content' => $m->content,
                        'citations' => $m->citations ?? [],
                        'created_at' => optional($m->created_at)->toIso8601String(),
                        'reaction' => $m->reaction,
                        'is_pinned' => (bool) $m->is_pinned,
                    ])->values(),
                ] : null,
                'startNew' => $startNew,
            ],
        ]);
    }

    /** Load a single conversation with messages (JSON API) */
    public function show(Request $request, Conversation $conversation)
    {
        abort_unless($conversation->user_id === $request->user()->id, 404);

        $conversation->load(['messages' => fn ($q) => $q->orderBy('created_at')]);

        return response()->json([
            'conversation' => [
                'id' => $conversation->id,
                'title' => $conversation->title,
                'messages' => $conversation->messages->map(fn ($m) => [
                    'id' => $m->id,
                    'role' => $m->role,
                    'content' => $m->content,
                    'citations' => $m->citations ?? [],
                    'created_at' => optional($m->created_at)->toIso8601String(),
                    'reaction' => $m->reaction,
                    'is_pinned' => (bool) $m->is_pinned,
                ])->values(),
            ],
        ]);
    }

    /** Create a new conversation (JSON API) */
    public function store(Request $request)
    {
        $conversation = Conversation::create([
            'user_id' => $request->user()->id,
            'title' => 'New conversation',
        ]);

        return response()->json([
            'conversation' => [
                'id' => $conversation->id,
                'title' => $conversation->title,
                'messages' => [],
                'last_message_at' => null,
            ],
        ], 201);
    }

    /** Delete a conversation (JSON API) */
    public function destroy(Request $request, Conversation $conversation)
    {
        abort_unless($conversation->user_id === $request->user()->id, 404);

        $conversation->delete();

        return response()->json(['message' => 'Conversation deleted.']);
    }
}
