@extends('layouts.admin')

@section('title', 'Conversations')

@section('breadcrumb')
<a href="{{ route('dashboard') }}">Dashboard</a>
<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
    <polyline points="9,18 15,12 9,6" /></svg>
<span class="breadcrumb-active">Conversations</span>
@endsection

@section('page-title', 'Conversations')
@section('page-subtitle', 'Ask questions grounded in your indexed knowledge base.')
@section('page-heading-actions')
<button class="btn btn-primary" id="new-conversation-button" type="button">
    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <line x1="12" y1="5" x2="12" y2="19" />
        <line x1="5" y1="12" x2="19" y2="12" />
    </svg>
    New Conversation
</button>
@endsection

@section('content')

<div
    id="conversations-page"
    data-state='@json($state)'
    data-routes='@json(["conversations" => route("conversations.store")])'
    data-start-new="{{ !empty($state['startNew']) ? '1' : '0' }}"
>

    <div class="conversations-layout">

        {{-- Left: conversation list --}}
        <div class="admin-card conv-list-card">
            <div class="card-header">
                <h2 class="card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                    </svg>
                    Saved Chats
                </h2>
                <span class="count-badge" id="conversation-count">0</span>
            </div>
            <div class="card-body">
                <div class="conversation-list" id="conversation-list">
                    <p class="empty-state">No chats yet. Click "New Conversation" to begin.</p>
                </div>
            </div>
        </div>

        {{-- Right: chat panel --}}
        <div class="admin-card chat-card">
            <div class="card-header">
                <div class="card-header-info">
                    <h2 class="card-title" id="conversation-title">New Conversation</h2>
                    <span class="header-status" id="composer-status">Ready.</span>
                </div>
                <div class="card-header-actions">
                    <button class="btn btn-ghost btn-sm btn-pin-toggle" id="toggle-pinned-panel" type="button" title="View Pinned Messages">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m19 21-7-4-7 4V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16z"/>
                        </svg>
                        Pinned
                        <span class="pin-count-badge" id="pin-count">0</span>
                    </button>
                    <button class="btn btn-outline-danger btn-sm" id="delete-conversation-button" type="button" disabled>
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6" />
                            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
                            <path d="M10 11v6M14 11v6M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" />
                        </svg>
                        Delete
                    </button>
                </div>
            </div>

            <div class="chat-container">
                <div class="card-body message-body-area">
                    <div class="message-stream" id="message-stream">
                        <div class="empty-state-chat">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="opacity:.2">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                            </svg>
                            <p>Select a conversation or create a new one, then ask a question grounded in your indexed knowledge.</p>
                        </div>
                    </div>
                </div>

                {{-- Pinned messages panel --}}
                <div class="pinned-panel" id="pinned-panel" style="display:none">
                    <div class="pinned-panel-header">
                        <h3>Pinned Messages</h3>
                        <button type="button" class="btn-close-panel" id="close-pinned-panel">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                        </button>
                    </div>
                    <div class="pinned-messages-list" id="pinned-messages-list">
                        <p class="empty-state">No pinned messages yet.</p>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <form class="composer" id="message-form">
                    <textarea id="question" name="question" rows="3" placeholder="Ask anything about your indexed documents..."></textarea>
                    <div class="composer-actions">
                        <button class="btn btn-primary" id="send-button" type="submit">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="22" y1="2" x2="11" y2="13" />
                                <polygon points="22 2 15 22 11 13 2 9 22 2" />
                            </svg>
                            Send Message
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
{{-- delete conversation modal--}}
<div class="confirm-modal" id="con-delete-modal" style="display:none">
    <div class="confirm-modal-backdrop" id="con-modal-backdrop"></div>

    <div class="confirm-modal-box">
        <h3 class="confirm-modal-title">Delete conversation?</h3>

        <p class="confirm-modal-body" id="con-delete-text">
            This action cannot be undone.
        </p>

        <div class="confirm-modal-actions">
            <button type="button" class="btn btn-ghost" id="con-modal-cancel">
                Cancel
            </button>

            <button type="button" class="btn btn-danger-solid" id="con-confirm-delete">
                Delete
            </button>
        </div>
    </div>
</div>

@endsection
