<x-guest-layout
    title="Create your account"
    subtitle="Set up your workspace and start building a private searchable knowledge base."
>
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
                placeholder="Create a strong password"
            >
            @error('password')
                <p class="auth-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="auth-form-group">
            <label class="auth-label" for="password_confirmation">Confirm password</label>
            <input
                id="password_confirmation" name="password_confirmation" type="password"
                class="auth-input {{ $errors->has('password_confirmation') ? 'error' : '' }}"
                required autocomplete="new-password"
                placeholder="Repeat your password"
            >
            @error('password_confirmation')
                <p class="auth-error">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="auth-btn auth-btn-spaced">Create account</button>
    </form>

    <div class="auth-footer">
        Already have an account?
        <a href="{{ route('login') }}">Sign in</a>
    </div>
</x-guest-layout>
