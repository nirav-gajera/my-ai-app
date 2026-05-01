<x-guest-layout
    title="Welcome back"
    subtitle="Sign in to continue working with your indexed knowledge base and saved conversations."
>
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
            <div class="auth-inline-row">
                <label class="auth-label" for="password">Password</label>
                @if (Route::has('password.request'))
                    <a class="auth-forgot" href="{{ route('password.request') }}">Forgot password?</a>
                @endif
            </div>
            <input
                id="password" name="password" type="password"
                class="auth-input {{ $errors->has('password') ? 'error' : '' }}"
                required autocomplete="current-password"
                placeholder="Enter your password"
            >
            @error('password')
                <p class="auth-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-2 auth-remember">
            <input type="checkbox" name="remember" id="remember_me" {{ old('remember') ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-600" id="default-check" />
            <label for="remember_me" class="text-sm font-medium text-gray-600">Keep me signed in</label>
        </div>

        <button type="submit" class="auth-btn">Sign in</button>
    </form>

    <div class="auth-footer">
        Don't have an account?
        <a href="{{ route('register') }}">Create one</a>
    </div>
</x-guest-layout>
