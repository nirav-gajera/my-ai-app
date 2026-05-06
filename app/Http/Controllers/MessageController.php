<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /** Update message reaction (JSON API) */
    public function react(Request $request, Message $message)
    {
        abort_unless($message->conversation->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'reaction' => ['nullable', 'string', 'in:like,dislike'],
        ]);

        $message->update([
            'reaction' => $validated['reaction'],
        ]);

        return response()->json([
            'message' => 'Reaction updated.',
            'reaction' => $message->reaction,
        ]);
    }

    /** Toggle message pin status (JSON API) */
    public function togglePin(Request $request, Message $message)
    {
        abort_unless($message->conversation->user_id === $request->user()->id, 403);

        $message->update([
            'is_pinned' => ! $message->is_pinned,
        ]);

        return response()->json([
            'message' => $message->is_pinned ? 'Message pinned.' : 'Message unpinned.',
            'is_pinned' => $message->is_pinned,
        ]);
    }
}
