@extends('layouts.admin')

@section('title', 'Users')

@section('breadcrumb')
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <polyline points="9,18 15,12 9,6" />
    </svg>
    <span class="breadcrumb-active">Users</span>
@endsection

@section('page-title', 'Users')
@section('page-subtitle', 'Manage registered users and admin access from one place.')

@section('page-heading-actions')
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19" />
            <line x1="5" y1="12" x2="19" y2="12" />
        </svg>
        Create User
    </a>
@endsection

@section('content')

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
                    Filter Users
                </h2>
                <span class="header-status">Search by name or email, then narrow by role if needed.</span>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" class="admin-filter-form">
                <input type="hidden" name="per_page" value="{{ request('per_page', $users->perPage()) }}">
                <div class="form-group">
                    <label class="form-label" for="search">Search</label>
                    <input id="search" type="text" name="search" value="{{ request('search') }}" class="form-input"
                           placeholder="Search users by name or email">
                </div>

                <div class="form-group">
                    <label class="form-label" for="role">Role</label>
                    <select id="role" name="role" class="form-input">
                        <option value="">All roles</option>
                        <option value="admin" @selected(request('role') === 'admin')>Admins</option>
                        <option value="user" @selected(request('role') === 'user')>Users</option>
                    </select>
                </div>

                <div class="admin-filter-actions">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="admin-card">
        <div class="card-header">
            <div class="card-header-info">
                <h2 class="card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 6h18" />
                        <path d="M3 12h18" />
                        <path d="M3 18h18" />
                    </svg>
                    User Directory
                </h2>
                <span class="header-status">{{ $users->total() }} total users</span>
            </div>
        </div>

        <div class="card-body p-0">
            @if ($users->count())
                <div class="table-shell">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Role</th>
                                <th>Joined</th>
                                <th class="table-actions-cell">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>
                                        <div class="admin-user-cell">
                                            <div class="admin-user-avatar">
                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                            </div>
                                            <div class="admin-user-meta">
                                                <div class="admin-user-name">{{ $user->name }}</div>
                                                <div class="admin-user-subtitle">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="role-badge {{ $user->is_admin ? 'role-badge-admin' : 'role-badge-user' }}">
                                            {{ $user->is_admin ? 'Admin' : 'User' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="table-meta">
                                            <strong>{{ $user->created_at->format('M d, Y') }}</strong>
                                            <span>ID: {{ $user->id }}</span>
                                        </div>
                                    </td>
                                    <td class="table-actions-cell">
                                        <div class="table-actions">
                                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-primary btn-sm">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none"
                                                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M12 20h9" />
                                                    <path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4L16.5 3.5z" />
                                                </svg>
                                                Edit
                                            </a>

                                            @if (Auth::id() !== $user->id)
                                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                                      id="delete-user-form-{{ $user->id }}" class="inline-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                            class="btn btn-outline-danger btn-sm"
                                                            data-user-delete-trigger
                                                            data-user-id="{{ $user->id }}"
                                                            data-user-name="{{ $user->name }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none"
                                                             stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                            <polyline points="3 6 5 6 21 6"></polyline>
                                                            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path>
                                                            <path d="M10 11v6M14 11v6M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"></path>
                                                        </svg>
                                                        Delete
                                                    </button>
                                                </form>
                                            @else
                                                <span class="self-lock-badge" title="You cannot delete your own account">
                                                    Current account
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="overview-empty">
                    <p>No users found for the current filters.</p>
                </div>
            @endif
        </div>
    </div>

    @if ($users->total() > 0)
        <div class="admin-pagination">
            @php
                $perPage = (int) request('per_page', $users->perPage());
                $pageWindow = 1;
                $startPage = max(1, $users->currentPage() - $pageWindow);
                $endPage = min($users->lastPage(), $users->currentPage() + $pageWindow);
            @endphp

            <div class="admin-pagination-bar">
                <form method="GET" class="admin-per-page-form">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="role" value="{{ request('role') }}">
                    <label class="pagination-select-label" for="users-per-page">
                        <span>Show</span>
                        <select id="users-per-page" name="per_page" class="pagination-select" onchange="this.form.submit()">
                            @foreach ([5, 10, 25, 50, 100] as $option)
                                <option value="{{ $option }}" @selected($perPage === $option)>{{ $option }}</option>
                            @endforeach
                        </select>
                        <span>of {{ $users->total() }}</span>
                    </label>
                </form>

                <div class="pagination-list">
                    @if ($users->onFirstPage())
                        <span class="pagination-link" aria-disabled="true">« Previous</span>
                    @else
                        <a href="{{ $users->previousPageUrl() }}" class="pagination-link">« Previous</a>
                    @endif

                    @if ($startPage > 1)
                        <a href="{{ $users->url(1) }}" class="pagination-link">1</a>
                        @if ($startPage > 2)
                            <span class="pagination-ellipsis">…</span>
                        @endif
                    @endif

                    @for ($page = $startPage; $page <= $endPage; $page++)
                        @if ($page === $users->currentPage())
                            <span class="pagination-link active">{{ $page }}</span>
                        @else
                            <a href="{{ $users->url($page) }}" class="pagination-link">{{ $page }}</a>
                        @endif
                    @endfor

                    @if ($endPage < $users->lastPage())
                        @if ($endPage < $users->lastPage() - 1)
                            <span class="pagination-ellipsis">…</span>
                        @endif
                        <a href="{{ $users->url($users->lastPage()) }}" class="pagination-link">{{ $users->lastPage() }}</a>
                    @endif

                    @if ($users->hasMorePages())
                        <a href="{{ $users->nextPageUrl() }}" class="pagination-link">Next »</a>
                    @else
                        <span class="pagination-link" aria-disabled="true">Next »</span>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <div class="confirm-modal" id="user-delete-modal" style="display:none">
        <div class="confirm-modal-backdrop" id="user-delete-backdrop"></div>

        <div class="confirm-modal-box">
            <h3 class="confirm-modal-title">Delete user?</h3>

            <p class="confirm-modal-body" id="user-delete-text">
                This action cannot be undone.
            </p>

            <div class="confirm-modal-actions">
                <button type="button" class="btn btn-ghost" id="user-delete-cancel">
                    Cancel
                </button>

                <button type="button" class="btn btn-danger-solid" id="user-delete-confirm">
                    Delete
                </button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    const _userDeleteModal = document.getElementById('user-delete-modal');
    const _userDeleteBackdrop = document.getElementById('user-delete-backdrop');
    const _userDeleteCancel = document.getElementById('user-delete-cancel');
    const _userDeleteConfirm = document.getElementById('user-delete-confirm');
    const _userDeleteText = document.getElementById('user-delete-text');
    let _activeUserDeleteForm = null;

    const _openUserDeleteModal = () => {
        if (_userDeleteModal) _userDeleteModal.style.display = '';
    };

    const _closeUserDeleteModal = () => {
        if (_userDeleteModal) _userDeleteModal.style.display = 'none';
        _activeUserDeleteForm = null;
    };

    document.querySelectorAll('[data-user-delete-trigger]').forEach((button) => {
        button.addEventListener('click', () => {
            _activeUserDeleteForm = document.getElementById(`delete-user-form-${button.dataset.userId}`);
            if (_userDeleteText) {
                _userDeleteText.textContent = `Delete "${button.dataset.userName}"? This cannot be undone.`;
            }
            _openUserDeleteModal();
        });
    });

    _userDeleteCancel?.addEventListener('click', _closeUserDeleteModal);
    _userDeleteBackdrop?.addEventListener('click', _closeUserDeleteModal);
    _userDeleteConfirm?.addEventListener('click', () => _activeUserDeleteForm?.submit());
</script>
@endpush
