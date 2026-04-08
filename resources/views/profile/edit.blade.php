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
</script>
@endpush
