@extends('layouts.admin')

@section('title', 'Edit User')

@section('breadcrumb')
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <polyline points="9,18 15,12 9,6" />
    </svg>
    <a href="{{ route('admin.users.index') }}">Users</a>
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <polyline points="9,18 15,12 9,6" />
    </svg>
    <span class="breadcrumb-active">Edit</span>
@endsection

@section('page-title', 'Edit User')
@section('page-subtitle', 'Update account details, role access, and password settings.')

@section('content')
    <div class="admin-card">
        <div class="card-header">
            <div class="card-header-info">
                <h2 class="card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    User Information
                </h2>
                <span class="header-status">Editing {{ $user->name }}.</span>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')

            <div class="card-body">
                <div class="admin-form-grid">
                    <div class="form-group">
                        <label class="form-label" for="name">Full name</label>
                        <input id="name" class="form-input {{ $errors->has('name') ? 'input-error' : '' }}"
                               name="name" type="text" value="{{ old('name', $user->name) }}" placeholder="Enter full name">
                        @error('name')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">Email address</label>
                        <input id="email" class="form-input {{ $errors->has('email') ? 'input-error' : '' }}"
                               name="email" type="email" value="{{ old('email', $user->email) }}" placeholder="Enter email address">
                        @error('email')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group form-group-full">
                        <input type="hidden" name="is_admin" value="0">

                        <label class="setting-toggle">
                            <input type="checkbox" name="is_admin" value="1"
                                   {{ old('is_admin', $user->is_admin) ? 'checked' : '' }}>
                            <span class="setting-toggle-ui">
                                <span class="setting-toggle-copy">
                                    <strong>Admin access</strong>
                                    <span>Enable this to allow the user to access the admin area and manage users.</span>
                                </span>
                            </span>
                        </label>

                        @error('is_admin')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="telegram_chat_id">Telegram Chat ID</label>
                        <input id="telegram_chat_id" class="form-input {{ $errors->has('telegram_chat_id') ? 'input-error' : '' }}"
                               name="telegram_chat_id" type="text" value="{{ old('telegram_chat_id', $user->telegram_chat_id) }}" placeholder="Enter Telegram Chat ID">
                        @error('telegram_chat_id')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group form-group-full">
                        <input type="hidden" name="telegram_enabled" value="0">

                        <label class="setting-toggle">
                            <input type="checkbox" name="telegram_enabled" value="1"
                                   {{ old('telegram_enabled', $user->telegram_enabled) ? 'checked' : '' }}>
                            <span class="setting-toggle-ui">
                                <span class="setting-toggle-copy">
                                    <strong>Telegram Enabled</strong>
                                    <span>Enable this to allow the user to chat with their knowledge via Telegram bot. @php $activeBot = \App\Models\TelegramBot::getActive(); @endphp @if($activeBot && $activeBot->bot_url) <a href="{{ $activeBot->bot_url }}" class="text-blue" target="_blank">{{ $activeBot->bot_url }}</a> @else <em>No active bot configured</em> @endif</span>
                                </span>
                            </span>
                        </label>

                        @error('telegram_enabled')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group form-group-full">
                        <div class="form-note">
                            Leave the password fields empty if you want to keep the current password unchanged.
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">New password</label>
                        <input id="password" class="form-input {{ $errors->has('password') ? 'input-error' : '' }}"
                               name="password" type="password" placeholder="Leave blank to keep current password">
                        @error('password')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password_confirmation">Confirm new password</label>
                        <input id="password_confirmation" class="form-input"
                               name="password_confirmation" type="password" placeholder="Repeat new password">
                    </div>
                </div>
            </div>

            <div class="card-footer admin-form-footer">
                <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary">Update User</button>
            </div>
        </form>
    </div>
@endsection
