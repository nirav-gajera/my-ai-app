<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In — AI Workspace</title>
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
            <p class="auth-title">Welcome back</p>
            <p class="auth-subtitle">Sign in to your AI Workspace</p>
        </div>

        <div class="auth-card-body">
            @if (session('status'))
                <div class="auth-alert">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="auth-form-group">
                    <label class="auth-label" for="email">Email address</label>
                    <input
                        id="email" name="email" type="email"
                        class="auth-input {{ $errors->has('email') ? 'error' : '' }}"
                        value="{{ old('email') }}"
                        required autofocus autocomplete="username"
                        placeholder="you@example.com"
                    >
                    @error('email')
                        <p class="auth-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="auth-form-group">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <label class="auth-label" for="password">Password</label>
                        @if (Route::has('password.request'))
                            <a class="auth-forgot" href="{{ route('password.request') }}">Forgot password?</a>
                        @endif
                    </div>
                    <input
                        id="password" name="password" type="password"
                        class="auth-input {{ $errors->has('password') ? 'error' : '' }}"
                        required autocomplete="current-password"
                        placeholder="••••••••"
                    >
                    @error('password')
                        <p class="auth-error">{{ $message }}</p>
                    @enderror
                </div>

                <label class="auth-remember">
                    <input type="checkbox" name="remember" id="remember_me" {{ old('remember') ? 'checked' : '' }}>
                    Remember me for 30 days
                </label>

                <button type="submit" class="auth-btn">Sign in</button>
            </form>
        </div>

        <div class="auth-footer">
            Don't have an account?
            <a href="{{ route('register') }}">Create one free</a>
        </div>

    </div>
</div>
</body>
</html>
