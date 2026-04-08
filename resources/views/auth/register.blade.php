<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Account — AI Workspace</title>
    @vite(['resources/css/app.css'])
</head>
<body>
<div class="auth-page">
    <div class="auth-card">

        <div class="auth-card-header">
            <div class="auth-logo">
                <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="3"/>
                    <path d="M12 1v4M12 19v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M1 12h4M19 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83"/>
                </svg>
            </div>
            <p class="auth-title">Create your account</p>
            <p class="auth-subtitle">Start using AI Workspace for free</p>
        </div>

        <div class="auth-card-body">
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="auth-form-group">
                    <label class="auth-label" for="name">Full name</label>
                    <input
                        id="name" name="name" type="text"
                        class="auth-input {{ $errors->has('name') ? 'error' : '' }}"
                        value="{{ old('name') }}"
                        required autofocus autocomplete="name"
                        placeholder="John Doe"
                    >
                    @error('name')
                        <p class="auth-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="auth-form-group">
                    <label class="auth-label" for="email">Email address</label>
                    <input
                        id="email" name="email" type="email"
                        class="auth-input {{ $errors->has('email') ? 'error' : '' }}"
                        value="{{ old('email') }}"
                        required autocomplete="username"
                        placeholder="you@example.com"
                    >
                    @error('email')
                        <p class="auth-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="auth-form-group">
                    <label class="auth-label" for="password">Password</label>
                    <input
                        id="password" name="password" type="password"
                        class="auth-input {{ $errors->has('password') ? 'error' : '' }}"
                        required autocomplete="new-password"
                        placeholder="Min. 8 characters"
                    >
                    @error('password')
                        <p class="auth-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="auth-form-group">
                    <label class="auth-label" for="password_confirmation">Confirm password</label>
                    <input
                        id="password_confirmation" name="password_confirmation" type="password"
                        class="auth-input"
                        required autocomplete="new-password"
                        placeholder="Repeat password"
                    >
                    @error('password_confirmation')
                        <p class="auth-error">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="auth-btn" style="margin-top:4px">Create account</button>
            </form>
        </div>

        <div class="auth-footer">
            Already have an account?
            <a href="{{ route('login') }}">Sign in</a>
        </div>

    </div>
</div>
</body>
</html>
