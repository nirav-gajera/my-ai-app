@extends('layouts.admin')

@section('title', 'Add Telegram Bot')

@section('breadcrumb')
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <polyline points="9,18 15,12 9,6" />
    </svg>
    <a href="{{ route('admin.telegram-bots.index') }}">Telegram Bots</a>
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <polyline points="9,18 15,12 9,6" />
    </svg>
    <span class="breadcrumb-active">Add Bot</span>
@endsection

@section('page-title', 'Add Telegram Bot')
@section('page-subtitle', 'Register a new Telegram bot to connect with your knowledge workspace.')

@section('content')

    <div class="admin-card">
        <div class="card-header">
            <h2 class="card-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M8.625 9.75a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 0 1 .778-.332 48.294 48.294 0 0 0 5.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                </svg>
                Bot Details
            </h2>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.telegram-bots.store') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="name">Bot Name</label>
                    <input id="name" name="name" type="text" class="form-input {{ $errors->has('name') ? 'input-error' : '' }}"
                           value="{{ old('name') }}" placeholder="e.g. My AI Bot">
                    @error('name')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="token">Bot Token</label>
                    <input id="token" name="token" type="text" class="form-input {{ $errors->has('token') ? 'input-error' : '' }}"
                           value="{{ old('token') }}" placeholder="123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11">
                    <p style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">
                        Get this from <a href="https://t.me/BotFather" target="_blank" class="text-blue">@BotFather</a> on Telegram. The token is stored encrypted.
                    </p>
                    @error('token')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="bot_username">Bot Username</label>
                    <input id="bot_username" name="bot_username" type="text" class="form-input {{ $errors->has('bot_username') ? 'input-error' : '' }}"
                           value="{{ old('bot_username') }}" placeholder="my_ai_app_bot">
                    @error('bot_username')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="bot_url">Bot URL (optional)</label>
                    <input id="bot_url" name="bot_url" type="url" class="form-input {{ $errors->has('bot_url') ? 'input-error' : '' }}"
                           value="{{ old('bot_url') }}" placeholder="https://t.me/my_ai_app_bot">
                    @error('bot_url')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group form-group-full">
                    <input type="hidden" name="is_active" value="0">
                    <label class="setting-toggle">
                        <input type="checkbox" name="is_active" value="1"
                               {{ old('is_active') ? 'checked' : '' }}
                               id="is-active-checkbox">
                        <span class="setting-toggle-ui">
                            <span class="setting-toggle-copy">
                                <strong>Set as Active Bot</strong>
                                <span>This bot will be used for all Telegram interactions. Only one bot can be active at a time.</span>
                            </span>
                        </span>
                    </label>
                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.telegram-bots.index') }}" class="btn btn-ghost">Cancel</a>
                    <button type="submit" class="btn btn-primary" id="submit-btn">Create Bot</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Confirmation modal for deactivating existing bot --}}
    @if ($hasActiveBot)
        <div class="confirm-modal" id="activate-confirm-modal" style="display:none">
            <div class="confirm-modal-backdrop" id="activate-confirm-backdrop"></div>
            <div class="confirm-modal-box">
                <h3 class="confirm-modal-title">Replace Active Bot?</h3>
                <p class="confirm-modal-body">
                    There is already an active Telegram bot. Activating this new bot will <strong>deactivate the existing one</strong>. Are you sure?
                </p>
                <div class="confirm-modal-actions">
                    <button type="button" class="btn btn-ghost" id="activate-confirm-cancel">Cancel</button>
                    <button type="button" class="btn btn-primary" id="activate-confirm-yes">Yes, Activate This Bot</button>
                </div>
            </div>
        </div>
    @endif

@endsection

@push('scripts')
<script>
    @if ($hasActiveBot)
        const _activateModal = document.getElementById('activate-confirm-modal');
        const _activateBackdrop = document.getElementById('activate-confirm-backdrop');
        const _activateCancel = document.getElementById('activate-confirm-cancel');
        const _activateYes = document.getElementById('activate-confirm-yes');
        const _isActiveCheckbox = document.getElementById('is-active-checkbox');
        const _submitBtn = document.getElementById('submit-btn');
        const _form = _submitBtn.closest('form');
        let _pendingSubmit = false;

        _form.addEventListener('submit', (e) => {
            if (_isActiveCheckbox.checked && !_pendingSubmit) {
                e.preventDefault();
                _activateModal.style.display = 'flex';
            }
        });

        _activateCancel?.addEventListener('click', () => {
            _activateModal.style.display = 'none';
            _isActiveCheckbox.checked = false;
        });
        _activateBackdrop?.addEventListener('click', () => {
            _activateModal.style.display = 'none';
            _isActiveCheckbox.checked = false;
        });
        _activateYes?.addEventListener('click', () => {
            _pendingSubmit = true;
            _activateModal.style.display = 'none';
            _form.submit();
        });
    @endif
</script>
@endpush
