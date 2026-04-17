@extends('layouts.admin')

@section('title', 'Create User')

@section('breadcrumb')
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <polyline points="9,18 15,12 9,6" />
    </svg>
    <a href="{{ route('admin.users.index') }}">Users</a>
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <polyline points="9,18 15,12 9,6" />
    </svg>
    <span class="breadcrumb-active">Create</span>
@endsection

@section('page-title', 'Create User')
@section('page-subtitle', 'Add a new user account and optionally grant admin access.')

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
                    User Details
                </h2>
                <span class="header-status">Create a new account with the correct role from the start.</span>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            <div class="card-body">
                <div class="admin-form-grid">
                    <div class="form-group">
                        <label class="form-label" for="name">Full name</label>
                        <input id="name" class="form-input {{ $errors->has('name') ? 'input-error' : '' }}"
                               name="name" type="text" value="{{ old('name') }}" placeholder="Enter full name">
                        @error('name')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">Email address</label>
                        <input id="email" class="form-input {{ $errors->has('email') ? 'input-error' : '' }}"
                               name="email" type="email" value="{{ old('email') }}" placeholder="Enter email address">
                        @error('email')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <input id="password" class="form-input {{ $errors->has('password') ? 'input-error' : '' }}"
                               name="password" type="password" placeholder="Create password">
                        @error('password')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password_confirmation">Confirm password</label>
                        <input id="password_confirmation" class="form-input"
                               name="password_confirmation" type="password" placeholder="Repeat password">
                    </div>

                    <div class="form-group form-group-full">
                        <input type="hidden" name="is_admin" value="0">

                        <label class="setting-toggle">
                            <input type="checkbox" name="is_admin" value="1" {{ old('is_admin') ? 'checked' : '' }}>
                            <span class="setting-toggle-ui">
                                <span class="setting-toggle-copy">
                                    <strong>Grant admin access</strong>
                                    <span>Admins can manage users and access the protected admin resource area.</span>
                                </span>
                            </span>
                        </label>

                        @error('is_admin')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="card-footer admin-form-footer">
                <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary">Create User</button>
            </div>
        </form>
    </div>
@endsection
