@php
    $appToasts = [];

    if (session('success')) {
        $appToasts[] = ['type' => 'success', 'message' => session('success')];
    }

    if (session('error')) {
        $appToasts[] = ['type' => 'error', 'message' => session('error')];
    }
@endphp

@if (!empty($appToasts))
    <div class="toast-stack" id="toast-stack" aria-live="polite" aria-atomic="true">
        @foreach ($appToasts as $toast)
            <div class="toast-notice toast-{{ $toast['type'] }}" data-toast>
                <div class="toast-icon">
                    @if ($toast['type'] === 'success')
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 6 9 17l-5-5" />
                        </svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="12" y1="8" x2="12" y2="12" />
                            <line x1="12" y1="16" x2="12.01" y2="16" />
                        </svg>
                    @endif
                </div>

                <div class="toast-copy">
                    <strong>{{ $toast['type'] === 'success' ? 'Success' : 'Error' }}</strong>
                    <p>{{ $toast['message'] }}</p>
                </div>

                <button type="button" class="toast-close" data-toast-close aria-label="Dismiss notification">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18" />
                        <line x1="6" y1="6" x2="18" y2="18" />
                    </svg>
                </button>
            </div>
        @endforeach
    </div>
@endif
