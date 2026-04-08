<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    {{ __('Resend Verification Email') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}" id="verify-email-logout-form">
            @csrf

            <button type="button" id="verify-email-logout-trigger" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Log Out') }}
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
