<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\KnowledgeDocument;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $userId = $request->user()->id;

        $conversationCount = Conversation::forUser($userId)->count();
        $documentCount     = KnowledgeDocument::forUser($userId)->count();

        $recentConversations = Conversation::forUser($userId)
            ->orderByDesc('last_message_at')
            ->orderByDesc('updated_at')
            ->take(5)
            ->get();

        $recentDocuments = KnowledgeDocument::forUser($userId)
            ->withCount('chunks')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'conversationCount',
            'documentCount',
            'recentConversations',
            'recentDocuments',
        ));
    }
}
