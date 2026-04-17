@props([
    'title' => config('app.name', 'My AI App'),
    'subtitle' => 'Secure access to your private AI knowledge workspace.',
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

        <title>{{ $title }} | {{ config('app.name', 'My AI App') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,700|figtree:400,500,600" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="auth-page auth-page-shell">
        @include('layouts.partials.toasts')
        <div class="auth-shell">
            <section class="auth-stage">
                <div class="auth-card auth-card-rich">
                    <a href="{{ route('welcome') }}" class="auth-home-link">
                        <span class="landing-brand-mark">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                            </svg>
                        </span>
                        <span class="auth-home-copy">
                            <strong>{{ config('app.name', 'My AI App') }}</strong>
                            <span>Private knowledge workspace</span>
                        </span>
                    </a>

                    <div class="auth-card-header auth-card-header-left">
                        <p class="auth-title">{{ $title }}</p>
                        <p class="auth-subtitle">{{ $subtitle }}</p>
                    </div>

                    <div class="auth-card-body">
                        {{ $slot }}
                    </div>
                </div>
            </section>

            <aside class="auth-side-panel">
                <div class="auth-side-inner">
                    <span class="landing-kicker">Document-Grounded AI</span>
                    <h1 class="auth-side-title">Chat with answers sourced from your own indexed knowledge.</h1>
                    <p class="auth-side-copy">
                        Upload text files, paste internal documentation, and search through your own content using
                        embeddings, retrieval, and grounded responses backed by citations.
                    </p>

                    <div class="auth-side-grid">
                        <article class="auth-side-card">
                            <span>Private Sources</span>
                            <strong>User-scoped documents and conversations</strong>
                        </article>
                        <article class="auth-side-card">
                            <span>Semantic Retrieval</span>
                            <strong>Chunk embeddings + cosine similarity</strong>
                        </article>
                        <article class="auth-side-card">
                            <span>Grounded Output</span>
                            <strong>Answers generated from retrieved context</strong>
                        </article>
                    </div>
                </div>
            </aside>
        </div>
    </body>
</html>
