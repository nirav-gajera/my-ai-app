@extends('layouts.admin')

@section('title', 'Dashboard')

@section('breadcrumb')
    <span class="breadcrumb-active">Dashboard</span>
@endsection

@section('topbar-actions')
    <a href="{{ route('conversations.index', ['new' => 1]) }}" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        New Conversation
    </a>
@endsection

@section('page-title', 'Dashboard')
@section('page-subtitle', 'Overview of your AI workspace activity.')

@section('content')

    {{-- Stats row --}}
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon stat-blue">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
            </div>
            <div class="stat-body">
                <p class="stat-label">Total Conversations</p>
                <p class="stat-value">{{ $conversationCount }}</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-green">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                </svg>
            </div>
            <div class="stat-body">
                <p class="stat-label">Indexed Documents</p>
                <p class="stat-value">{{ $documentCount }}</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-purple">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
            </div>
            <div class="stat-body">
                <p class="stat-label">Logged in as @if (Auth::user()->is_admin): Admin @endif</p>
                <p class="stat-value stat-value-status">
                    <span class="status-dot-green"></span>
                    {{ Str::limit(auth()->user()->name, 14) }}
                </p>
            </div>
        </div>
    </div>

    {{-- Overview grid: recent conversations + recent documents --}}
    <div class="overview-grid">

        {{-- Recent conversations --}}
        <div class="admin-card">
            <div class="card-header">
                <h2 class="card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                    Recent Conversations
                </h2>
                <a href="{{ route('conversations.index') }}" class="card-link">View all</a>
            </div>
            <div class="card-body p-0">
                @forelse($recentConversations as $conversation)
                    <a href="{{ route('conversations.index') }}?id={{ $conversation->id }}"
                       class="overview-row">
                        <div class="overview-row-icon overview-icon-blue">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                            </svg>
                        </div>
                        <div class="overview-row-body">
                            <p class="overview-row-title">{{ $conversation->title }}</p>
                            <p class="overview-row-meta">
                                {{ optional($conversation->last_message_at ?? $conversation->updated_at)->diffForHumans() ?? 'Just now' }}
                            </p>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" class="overview-row-arrow">
                            <polyline points="9,18 15,12 9,6"/>
                        </svg>
                    </a>
                @empty
                    <div class="overview-empty">
                        <p>No conversations yet.</p>
                        <a href="{{ route('conversations.index', ['new' => 1]) }}" class="btn btn-primary btn-sm" style="margin-top:10px">Start your first chat</a>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Recent documents --}}
        <div class="admin-card">
            <div class="card-header">
                <h2 class="card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                    </svg>
                    Indexed Documents
                </h2>
                <a href="{{ route('knowledge.index') }}" class="card-link">View all</a>
            </div>
            <div class="card-body p-0">
                @forelse($recentDocuments as $document)
                    <div class="overview-row">
                        <div class="overview-row-icon overview-icon-green">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round">
                                <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/>
                                <polyline points="13 2 13 9 20 9"/>
                            </svg>
                        </div>
                        <div class="overview-row-body">
                            <p class="overview-row-title">{{ $document->title }}</p>
                            <p class="overview-row-meta">{{ $document->chunks_count }} chunks · {{ $document->source_type }}</p>
                        </div>
                        <span class="chunk-badge">{{ $document->chunks_count }}</span>
                    </div>
                @empty
                    <div class="overview-empty">
                        <p>No documents indexed yet.</p>
                        <a href="{{ route('knowledge.index') }}" class="btn btn-primary btn-sm" style="margin-top:10px">Index your first document</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

@endsection
