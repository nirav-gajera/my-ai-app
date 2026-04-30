@extends('layouts.admin')

@section('title', 'Profile')

@section('breadcrumb')
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9,18 15,12 9,6"/></svg>
    <span class="breadcrumb-active">Profile</span>
@endsection

@section('page-title', 'Profile Settings')
@section('page-subtitle', 'Manage your account information and security settings.')

@section('content')

<div class="profile-layout">

    {{-- Profile information --}}
    <div class="admin-card">
        <div class="card-header">
            <h2 class="card-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
                Profile Information
            </h2>
        </div>
        <div class="card-body">
            @if (session('status') === 'profile-updated')
                <div class="alert alert-success">Profile updated successfully.</div>
            @endif

            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('patch')

                <div class="form-group">
                    <label class="form-label" for="name">Full name</label>
                    <input id="name" name="name" type="text"
                           class="form-input {{ $errors->has('name') ? 'input-error' : '' }}"
                           value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
                    @error('name')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email address</label>
                    <input id="email" name="email" type="email"
                           class="form-input {{ $errors->has('email') ? 'input-error' : '' }}"
                           value="{{ old('email', $user->email) }}" required autocomplete="username">
                    @error('email')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary mt-2">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    {{-- telegram chat support --}}
    <div class="admin-card">
        <div class="card-header">
            <h2 class="card-title">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="18" height="18">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 0 1 .778-.332 48.294 48.294 0 0 0 5.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                </svg>
                Telegram Chat Support
            </h2>
        </div>
        <div class="card-body">
            @if (session('status') === 'telegram-updated')
                <div class="alert alert-success">Telegram records updated successfully.</div>
            @endif
            @if (session('status') === 'telegram-unlinked')
                <div class="alert alert-success">Telegram account unlinked successfully.</div>
            @endif

            <form method="POST" action="{{ route('telegram.update') }}">
                @csrf
                @method('patch')

                <div class="form-group">
                    <label class="form-label" for="telegram_chat_id">Telegram Connection</label>
                    
                    <div style="display: flex; gap: 12px; flex-direction: column;">
                        {{-- Method 1: Automated Link --}}
                        <div class="link-option-box" style="padding: 16px; border: 1px dashed var(--admin-border); border-radius: 8px; background: var(--admin-bg-light);">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <h4 style="margin: 0; font-size: 14px; font-weight: 600;">Automated Link (Recommended)</h4>
                                    <p style="margin: 4px 0 0 0; font-size: 12px; color: var(--text-muted);">
                                        @if($user->telegram_chat_id)
                                            Your account is currently linked to Telegram.
                                        @else
                                            Click the button below to open Telegram and link your account automatically.
                                        @endif
                                    </p>
                                </div>
                                @if($user->telegram_chat_id)
                                    <button type="button" id="telegram-unlink-trigger" class="btn btn-outline-danger">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 6px;"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path><line x1="12" y1="2" x2="12" y2="12"></line></svg>
                                        Disconnect
                                    </button>
                                @else
                                    <button type="button" onclick="document.getElementById('telegram-link-form').submit();" class="btn btn btn-outline-primary">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 6px;"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                                        Link Account
                                    </button>
                                @endif
                            </div>
                        </div>

                        {{-- Method 2: Manual Input --}}
                        <div>
                            <p style="margin: 0 0 8px 0; font-size: 13px; font-weight: 500;">Or enter Chat ID manually</p>
                            <input id="telegram_chat_id" class="form-input {{ $errors->has('telegram_chat_id') ? 'input-error' : '' }}"
                                   name="telegram_chat_id" type="text" value="{{ old('telegram_chat_id', $user->telegram_chat_id) }}" placeholder="Enter Telegram Chat ID">
                            @error('telegram_chat_id')
                                <p class="field-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
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

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary mt-2">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Change password --}}
    <div class="admin-card">
        <div class="card-header">
            <h2 class="card-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                Change Password
            </h2>
        </div>
        <div class="card-body">
            @if (session('status') === 'password-updated')
                <div class="alert alert-success">Password updated successfully.</div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                @method('put')

                <div class="form-group">
                    <label class="form-label" for="current_password">Current password</label>
                    <input id="current_password" name="current_password" type="password"
                           class="form-input {{ $errors->updatePassword->has('current_password') ? 'input-error' : '' }}"
                           autocomplete="current-password" placeholder="••••••••">
                    @if ($errors->updatePassword->has('current_password'))
                        <p class="field-error">{{ $errors->updatePassword->first('current_password') }}</p>
                    @endif
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">New password</label>
                    <input id="password" name="password" type="password"
                           class="form-input {{ $errors->updatePassword->has('password') ? 'input-error' : '' }}"
                           autocomplete="new-password" placeholder="Min. 8 characters">
                    @if ($errors->updatePassword->has('password'))
                        <p class="field-error">{{ $errors->updatePassword->first('password') }}</p>
                    @endif
                </div>

                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Confirm new password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password"
                           class="form-input" autocomplete="new-password" placeholder="Repeat new password">
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary mt-2">Update Password</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete account --}}
    
</div>

<div class="admin-card danger-card">
    <div class="card-header">
        <h2 class="card-title danger-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="3 6 5 6 21 6"/>
                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                <path d="M10 11v6M14 11v6M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
            </svg>
            Delete Account
        </h2>
    </div>
    <div class="card-body">
        <p class="danger-description">
            Once deleted, all conversations, messages, and indexed documents are permanently removed. This cannot be undone.
        </p>
        <button type="button" class="btn btn-danger-solid" id="delete-account-trigger">Delete My Account</button>
    </div>
</div>
{{-- Telegram Unlink Confirmation --}}
<div class="confirm-modal" id="telegram-unlink-modal" style="display:none">
    <div class="confirm-modal-backdrop" id="tg-modal-backdrop"></div>
    <div class="confirm-modal-box">
        <h3 class="confirm-modal-title">Unlink Telegram Account?</h3>
        <p class="confirm-modal-body">
            Are you sure you want to disconnect your Telegram account? You will no longer be able to chat with your knowledge base via the bot.
        </p>
        <div class="confirm-modal-actions">
            <button type="button" class="btn btn-ghost" id="tg-modal-cancel">Cancel</button>
            <button type="button" class="btn btn-danger-solid" id="tg-confirm-unlink">Yes, Disconnect</button>
        </div>
    </div>
</div>

{{-- Delete confirmation modal --}}
<div class="confirm-modal" id="delete-account-modal" style="display:none">
    <div class="confirm-modal-backdrop" id="modal-backdrop"></div>
    <div class="confirm-modal-box">
        <h3 class="confirm-modal-title">Are you absolutely sure?</h3>
        <p class="confirm-modal-body">
            This will permanently delete your account and all associated data. Enter your password to confirm.
        </p>
        <form method="POST" action="{{ route('profile.destroy') }}">
            @csrf
            @method('delete')
            <div class="form-group" style="margin-top:16px">
                <label class="form-label" for="delete_password">Your current password</label>
                <input id="delete_password" name="password" type="password"
                       class="form-input {{ $errors->userDeletion->has('password') ? 'input-error' : '' }}"
                       placeholder="Enter your password">
                @if ($errors->userDeletion->has('password'))
                    <p class="field-error">{{ $errors->userDeletion->first('password') }}</p>
                @endif
            </div>
            <div class="confirm-modal-actions">
                <button type="button" class="btn btn-ghost" id="modal-cancel">Cancel</button>
                <button type="submit" class="btn btn-danger-solid">Yes, delete my account</button>
            </div>
        </form>
    </div>
</div>

<form id="telegram-link-form" action="{{ route('telegram.link') }}" method="POST" style="display: none;">
    @csrf
</form>

<form id="telegram-unlink-form" action="{{ route('telegram.unlink') }}" method="POST" style="display: none;">
    @csrf
</form>

@endsection

@push('scripts')
<script>
    const _modal    = document.getElementById('delete-account-modal');
    const _trigger  = document.getElementById('delete-account-trigger');
    const _cancel   = document.getElementById('modal-cancel');
    const _backdrop = document.getElementById('modal-backdrop');

    _trigger?.addEventListener('click',   () => _modal.style.display = 'flex');
    _cancel?.addEventListener('click',    () => _modal.style.display = 'none');
    _backdrop?.addEventListener('click',  () => _modal.style.display = 'none');

    @if ($errors->userDeletion->isNotEmpty())
        document.getElementById('delete-account-modal').style.display = 'flex';
    @endif

    // Telegram Unlink Logic
    const tgModal = document.getElementById('telegram-unlink-modal');
    const tgTrigger = document.getElementById('telegram-unlink-trigger');
    const tgCancel = document.getElementById('tg-modal-cancel');
    const tgBackdrop = document.getElementById('tg-modal-backdrop');
    const tgConfirm = document.getElementById('tg-confirm-unlink');
    const tgUnlinkForm = document.getElementById('telegram-unlink-form');

    tgTrigger?.addEventListener('click', () => tgModal.style.display = 'flex');
    tgCancel?.addEventListener('click', () => tgModal.style.display = 'none');
    tgBackdrop?.addEventListener('click', () => tgModal.style.display = 'none');
    
    tgConfirm?.addEventListener('click', () => {
        tgUnlinkForm.submit();
    });

    // Handle session toasts
    window.addEventListener('DOMContentLoaded', () => {
        @if (session('status') === 'telegram-unlinked')
            window.createToast({
                type: 'success',
                title: 'Account Unlinked',
                message: 'Your Telegram account has been disconnected.'
            });
        @endif

        @if (session('status') === 'telegram-updated')
            window.createToast({
                type: 'success',
                title: 'Settings Updated',
                message: 'Your Telegram preferences have been saved.'
            });
        @endif
    });
</script>
@endpush
