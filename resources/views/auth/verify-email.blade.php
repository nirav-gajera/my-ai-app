<x-guest-layout
    title="Verify your email"
    subtitle="Activate your account before accessing the dashboard and knowledge workspace."
>
    <p class="auth-copy-block">
        Thanks for signing up. Please verify your email address by clicking the link we sent you.
        If the email did not arrive, you can request another verification message below.
    </p>

    @if (session('status') === 'verification-link-sent')
        <div class="auth-alert auth-alert-success">
            A new verification link has been sent to your email address.
        </div>
    @endif

    <div class="auth-stack-actions">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="auth-btn">Resend verification email</button>
        </form>

        <form method="POST" action="{{ route('logout') }}" id="verify-email-logout-form">
            @csrf
            <button type="button" id="verify-email-logout-trigger" class="auth-text-link">
                Log out
            </button>
        </form>
    </div>

    <div class="confirm-modal" id="verify-email-logout-modal" style="display:none">
        <div class="confirm-modal-backdrop" id="verify-email-logout-backdrop"></div>

        <div class="confirm-modal-box">
            <h3 class="confirm-modal-title">Log out?</h3>

            <p class="confirm-modal-body">
                You will be signed out of your account and returned to the login screen.
            </p>

            <div class="confirm-modal-actions">
                <button type="button" class="btn btn-ghost" id="verify-email-logout-cancel">
                    Cancel
                </button>

                <button type="button" class="btn btn-danger-solid" id="verify-email-logout-confirm">
                    Logout
                </button>
            </div>
        </div>
    </div>

    <script>
        (() => {
            const form = document.getElementById('verify-email-logout-form');
            const trigger = document.getElementById('verify-email-logout-trigger');
            const modal = document.getElementById('verify-email-logout-modal');
            const backdrop = document.getElementById('verify-email-logout-backdrop');
            const cancel = document.getElementById('verify-email-logout-cancel');
            const confirm = document.getElementById('verify-email-logout-confirm');

            const openModal = () => {
                if (modal) modal.style.display = '';
            };

            const closeModal = () => {
                if (modal) modal.style.display = 'none';
            };

            trigger?.addEventListener('click', openModal);
            cancel?.addEventListener('click', closeModal);
            backdrop?.addEventListener('click', closeModal);
            confirm?.addEventListener('click', () => form?.submit());
        })();
    </script>
</x-guest-layout>
