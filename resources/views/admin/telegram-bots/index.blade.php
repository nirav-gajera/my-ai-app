@extends('layouts.admin')

@section('title', 'Telegram Bots')

@section('breadcrumb')
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <polyline points="9,18 15,12 9,6" />
    </svg>
    <span class="breadcrumb-active">Telegram Bots</span>
@endsection

@section('page-title', 'Telegram Bots')
@section('page-subtitle', 'Manage your Telegram bot integrations. Only one bot can be active at a time.')

@section('page-heading-actions')
    <a href="{{ route('admin.telegram-bots.create') }}" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19" />          
            <line x1="5" y1="12" x2="19" y2="12" />
        </svg>
        Add Bot
    </a>
@endsection

@section('content')

    @if (session('success'))
        {{-- <div class="alert alert-success">{{ session('success') }}</div> --}}
    @endif

    <div class="admin-card">
        <div class="card-header">
            <div class="card-header-info">
                <h2 class="card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                        <circle cx="8.5" cy="7" r="4" />
                        <path d="M20 8v6" />
                        <path d="M23 11h-6" />
                    </svg>
                    Filter Bots
                </h2>
                <span class="header-status">Search by name or username, then narrow by status if needed.</span>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" class="admin-filter-form">
                <input type="hidden" name="per_page" value="{{ request('per_page', $bots->perPage()) }}">
                <div class="form-group">
                    <label class="form-label" for="search">Search</label>
                    <input id="search" type="text" name="search" value="{{ request('search') }}" class="form-input"
                           placeholder="Search bots by name or username">
                </div>

                <div class="form-group">
                    <label class="form-label" for="status">Status</label>
                    <select id="status" name="status" class="form-input">
                        <option value="">All statuses</option>
                        <option value="active" @selected(request('status') === 'active')>Active</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                    </select>
                </div>

                <div class="admin-filter-actions">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="{{ route('admin.telegram-bots.index') }}" class="btn btn-ghost">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="admin-card">
        <div class="card-header">
            <div class="card-header-info">
                <h2 class="card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24"
                         stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M8.625 9.75a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 0 1 .778-.332 48.294 48.294 0 0 0 5.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                    </svg>
                    Bot Directory
                </h2>
                <span class="header-status">{{ $bots->total() }} total {{ Str::plural('bot', $bots->total()) }}</span>
            </div>
        </div>

        <div class="card-body p-0">
            @if ($bots->count())
                <div class="table-shell">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Bot</th>
                                <th>Username</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th class="table-actions-cell">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bots as $bot)
                                <tr>
                                    <td>
                                        <div class="admin-user-cell">
                                            <div class="admin-user-avatar" style="{{ $bot->is_active ? 'background: linear-gradient(135deg, #1d4ed8, #0f766e)' : 'background: #3a3f4b' }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8.287 5.906q-1.168.486-4.666 2.01-.567.225-.595.442c-.03.243.275.339.69.47l.175.055c.408.133.958.288 1.243.294q.39.01.868-.32 3.269-2.206 3.374-2.23c.05-.012.12-.026.166.016s.042.12.037.141c-.03.129-1.227 1.241-1.846 1.817-.193.18-.33.307-.358.336a8 8 0 0 1-.188.186c-.38.366-.664.64.015 1.088.327.216.589.393.85.571.284.194.568.387.936.629q.14.092.27.187c.331.236.63.448.997.414.214-.02.435-.22.547-.82.265-1.417.786-4.486.906-5.751a1.4 1.4 0 0 0-.013-.315.34.34 0 0 0-.114-.217.53.53 0 0 0-.31-.093c-.3.005-.763.166-2.984 1.09"/>
                                                </svg>
                                            </div>
                                            <div class="admin-user-meta">
                                                <div class="admin-user-name">{{ $bot->name }}</div>
                                                <div class="admin-user-subtitle">ID: {{ $bot->id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($bot->bot_url)
                                            <a href="{{ $bot->bot_url }}" target="_blank" class="text-blue">{{ '@' . $bot->bot_username }}</a>
                                        @else
                                            <code>{{ '@' . $bot->bot_username }}</code>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="role-badge {{ $bot->is_active ? 'telegram-enabled' : 'telegram-disabled' }}">
                                            {{ $bot->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="table-meta">
                                            <strong>{{ $bot->created_at->format('M d, Y') }}</strong>
                                        </div>
                                    </td>
                                    <td class="table-actions-cell">
                                        <div class="table-actions">
                                            <a href="{{ route('admin.telegram-bots.edit', $bot) }}" class="btn btn-outline-primary btn-sm">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none"
                                                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M12 20h9" />
                                                    <path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4L16.5 3.5z" />
                                                </svg>
                                                Edit
                                            </a>
                                            <form method="POST" action="{{ route('admin.telegram-bots.destroy', $bot) }}"
                                                  id="delete-bot-form-{{ $bot->id }}" class="inline-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-outline-danger btn-sm"
                                                        data-bot-delete-trigger
                                                        data-bot-id="{{ $bot->id }}"
                                                        data-bot-name="{{ $bot->name }}"
                                                        data-bot-active="{{ $bot->is_active ? '1' : '0' }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none"
                                                         stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                        <polyline points="3 6 5 6 21 6"></polyline>
                                                        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path>
                                                        <path d="M10 11v6M14 11v6M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"></path>
                                                    </svg>
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="overview-empty">
                    <p>No Telegram bots configured yet. Add one to get started.</p>
                </div>
            @endif
        </div>
    </div>

    @if ($bots->total() > 5)
        <div class="admin-pagination">
            @php
                $perPage = (int) request('per_page', $bots->perPage());
                $pageWindow = 1;
                $startPage = max(1, $bots->currentPage() - $pageWindow);
                $endPage = min($bots->lastPage(), $bots->currentPage() + $pageWindow);
            @endphp

            <div class="admin-pagination-bar">
                <form method="GET" class="admin-per-page-form">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="status" value="{{ request('status') }}">
                    <label class="pagination-select-label" for="bots-per-page">
                        <span>Show</span>
                        <select id="bots-per-page" name="per_page" class="pagination-select" onchange="this.form.submit()">
                            @foreach ([5, 10, 25, 50, 100] as $option)
                                <option value="{{ $option }}" @selected($perPage === $option)>{{ $option }}</option>
                            @endforeach
                        </select>
                        <span>of {{ $bots->total() }}</span>
                    </label>
                </form>

                <div class="pagination-list">
                    @if ($bots->onFirstPage())
                        <span class="pagination-link" aria-disabled="true">« Previous</span>
                    @else
                        <a href="{{ $bots->previousPageUrl() }}" class="pagination-link">« Previous</a>
                    @endif

                    @if ($startPage > 1)
                        <a href="{{ $bots->url(1) }}" class="pagination-link">1</a>
                        @if ($startPage > 2)
                            <span class="pagination-ellipsis">…</span>
                        @endif
                    @endif

                    @for ($page = $startPage; $page <= $endPage; $page++)
                        @if ($page === $bots->currentPage())
                            <span class="pagination-link active">{{ $page }}</span>
                        @else
                            <a href="{{ $bots->url($page) }}" class="pagination-link">{{ $page }}</a>
                        @endif
                    @endfor

                    @if ($endPage < $bots->lastPage())
                        @if ($endPage < $bots->lastPage() - 1)
                            <span class="pagination-ellipsis">…</span>
                        @endif
                        <a href="{{ $bots->url($bots->lastPage()) }}" class="pagination-link">{{ $bots->lastPage() }}</a>
                    @endif

                    @if ($bots->hasMorePages())
                        <a href="{{ $bots->nextPageUrl() }}" class="pagination-link">Next »</a>
                    @else
                        <span class="pagination-link" aria-disabled="true">Next »</span>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Delete confirmation modal --}}
    <div class="confirm-modal" id="bot-delete-modal" style="display:none">
        <div class="confirm-modal-backdrop" id="bot-delete-backdrop"></div>
        <div class="confirm-modal-box">
            <h3 class="confirm-modal-title">Delete bot?</h3>
            <p class="confirm-modal-body" id="bot-delete-text">
                This action cannot be undone.
            </p>
            <div class="confirm-modal-actions">
                <button type="button" class="btn btn-ghost" id="bot-delete-cancel">Cancel</button>
                <button type="button" class="btn btn-danger-solid" id="bot-delete-confirm">Delete</button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    const _botDeleteModal = document.getElementById('bot-delete-modal');
    const _botDeleteBackdrop = document.getElementById('bot-delete-backdrop');
    const _botDeleteCancel = document.getElementById('bot-delete-cancel');
    const _botDeleteConfirm = document.getElementById('bot-delete-confirm');
    const _botDeleteText = document.getElementById('bot-delete-text');
    let _activeBotDeleteForm = null;

    const _openBotDeleteModal = () => {
        if (_botDeleteModal) _botDeleteModal.style.display = '';
    };
    const _closeBotDeleteModal = () => {
        if (_botDeleteModal) _botDeleteModal.style.display = 'none';
        _activeBotDeleteForm = null;
    };

    document.querySelectorAll('[data-bot-delete-trigger]').forEach((button) => {
        button.addEventListener('click', () => {
            _activeBotDeleteForm = document.getElementById(`delete-bot-form-${button.dataset.botId}`);
            const isActive = button.dataset.botActive === '1';
            const extra = isActive ? ' This is your currently ACTIVE bot — deleting it will disable Telegram integration.' : '';
            _botDeleteText.textContent = `Delete "${button.dataset.botName}"? This cannot be undone.${extra}`;
            _openBotDeleteModal();
        });
    });

    _botDeleteCancel?.addEventListener('click', _closeBotDeleteModal);
    _botDeleteBackdrop?.addEventListener('click', _closeBotDeleteModal);
    _botDeleteConfirm?.addEventListener('click', () => _activeBotDeleteForm?.submit());
</script>
@endpush
