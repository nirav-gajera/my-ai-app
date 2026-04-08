<x-guest-layout
    title="Confirm your password"
    subtitle="For security, please confirm your password before continuing."
>
    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="auth-form-group">
            <label class="auth-label" for="password">Password</label>
            <input
                id="password" name="password" type="password"
                class="auth-input {{ $errors->has('password') ? 'error' : '' }}"
                required autofocus autocomplete="current-password"
                placeholder="Enter your password"
            >
            @error('password')
                <p class="auth-error">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="auth-btn">Confirm password</button>
    </form>
</x-guest-layout>
