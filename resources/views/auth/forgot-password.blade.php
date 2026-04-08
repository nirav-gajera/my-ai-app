<x-guest-layout
    title="Reset your password"
    subtitle="Enter your email address and we will send you a password reset link."
>
    @if (session('status'))
        <div class="auth-alert auth-alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
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

        <button type="submit" class="auth-btn">Email reset link</button>
    </form>

    <div class="auth-footer">
        Remembered your password?
        <a href="{{ route('login') }}">Back to sign in</a>
    </div>
</x-guest-layout>
