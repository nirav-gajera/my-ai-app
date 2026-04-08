<x-guest-layout
    title="Choose a new password"
    subtitle="Set a new password to regain access to your AI knowledge workspace."
>
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="auth-form-group">
            <label class="auth-label" for="email">Email address</label>
            <input
                id="email" name="email" type="email"
                class="auth-input {{ $errors->has('email') ? 'error' : '' }}"
                value="{{ old('email', $request->email) }}"
                required autofocus autocomplete="username"
                placeholder="you@example.com"
            >
            @error('email')
                <p class="auth-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="auth-form-group">
            <label class="auth-label" for="password">New password</label>
            <input
                id="password" name="password" type="password"
                class="auth-input {{ $errors->has('password') ? 'error' : '' }}"
                required autocomplete="new-password"
                placeholder="Enter your new password"
            >
            @error('password')
                <p class="auth-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="auth-form-group">
            <label class="auth-label" for="password_confirmation">Confirm new password</label>
            <input
                id="password_confirmation" name="password_confirmation" type="password"
                class="auth-input {{ $errors->has('password_confirmation') ? 'error' : '' }}"
                required autocomplete="new-password"
                placeholder="Repeat your new password"
            >
            @error('password_confirmation')
                <p class="auth-error">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="auth-btn">Reset password</button>
    </form>

    <div class="auth-footer">
        Need to try again?
        <a href="{{ route('password.request') }}">Request another link</a>
    </div>
</x-guest-layout>
