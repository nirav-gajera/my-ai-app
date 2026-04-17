<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'My AI App') }} | Knowledge Workspace</title>
    <meta name="description" content="Build a private knowledge base, index documents, and chat with grounded AI answers backed by your own content.">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,700|figtree:400,500,600" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="landing-body" id="top">
    <div class="landing-shell">
        <header class="landing-header">
            <a href="{{ route('welcome') }}" class="landing-brand">
                <span class="landing-brand-mark">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                    </svg>
                </span>
                <span class="landing-brand-copy">
                    <strong>My AI App</strong>
                    <span>Private knowledge workspace</span>
                </span>
            </a>

            <nav class="landing-actions">
                <a href="{{ route('login') }}" class="landing-btn landing-btn-ghost">Login</a>
                @if (Route::has('register'))
                <a href="{{ route('register') }}" class="landing-btn landing-btn-primary">Register</a>
                @endif
            </nav>
        </header>

        <main class="landing-main">
            <section class="landing-hero">
                <div class="landing-copy">
                    <span class="landing-kicker">RAG Knowledge Assistant</span>
                    <h1>Turn your documents into a searchable AI workspace.</h1>
                    <p class="landing-lead">
                        Upload policies, manuals, FAQs, notes, CSVs, JSON files, or pasted text.
                        The system chunks your content, creates embeddings, retrieves relevant context,
                        and answers questions using only your indexed knowledge base.
                    </p>

                    <div class="landing-cta-row">
                        @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="landing-btn landing-btn-primary landing-btn-lg">Start Free</a>
                        @endif
                        <a href="{{ route('login') }}" class="landing-btn landing-btn-outline landing-btn-lg">Sign In</a>
                    </div>

                    <ul class="landing-trust-list">
                        <li>Private user-scoped document retrieval</li>
                        <li>Chunk-based semantic search</li>
                        <li>Citation-backed AI answers</li>
                    </ul>
                </div>

                <div class="landing-hero-card">
                    <div class="landing-window">
                        <div class="landing-window-bar">
                            <span></span><span></span><span></span>
                        </div>

                        <div class="landing-window-body">
                            <div class="landing-stat-grid">
                                <article class="landing-stat-card">
                                    <span class="landing-stat-label">Sources</span>
                                    <strong>Text files, docs, notes</strong>
                                </article>
                                <article class="landing-stat-card">
                                    <span class="landing-stat-label">Retrieval</span>
                                    <strong>Embedding + cosine similarity</strong>
                                </article>
                                <article class="landing-stat-card">
                                    <span class="landing-stat-label">Output</span>
                                    <strong>Grounded answers with citations</strong>
                                </article>
                            </div>

                            <div class="landing-chat-preview">
                                <article class="landing-bubble landing-bubble-user">
                                    What does our support policy say about escalation?
                                </article>
                                <article class="landing-bubble landing-bubble-ai">
                                    Escalations move to tier 2 when the issue remains unresolved after first-response troubleshooting.
                                    The assistant can cite the matching document chunk used for this answer.
                                </article>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="landing-section">
                <div class="landing-section-head">
                    <span class="landing-kicker">How It Works</span>
                    <h2>Built for document-grounded answers.</h2>
                </div>

                <div class="landing-steps">
                    <article class="landing-step-card">
                        <span class="landing-step-index">01</span>
                        <h3>Index knowledge</h3>
                        <p>
                            Add text directly or upload supported files. Each document is stored,
                            normalized, chunked, and prepared for retrieval.
                        </p>
                    </article>
                    <article class="landing-step-card">
                        <span class="landing-step-index">02</span>
                        <h3>Retrieve context</h3>
                        <p>
                            The system embeds the question, compares it against stored chunk embeddings,
                            and selects the highest-similarity matches from the current user's data.
                        </p>
                    </article>
                    <article class="landing-step-card">
                        <span class="landing-step-index">03</span>
                        <h3>Answer with evidence</h3>
                        <p>
                            The assistant responds using retrieved knowledge plus recent conversation history,
                            and returns citations for the underlying source chunks.
                        </p>
                    </article>
                </div>
            </section>

            <section class="landing-section landing-section-alt">
                <div class="landing-section-head">
                    <span class="landing-kicker">What You Get</span>
                    <h2>Everything centered around your own data.</h2>
                </div>

                <div class="landing-feature-grid">
                    <article class="landing-feature-card">
                        <h3>User-scoped knowledge base</h3>
                        <p>Documents and conversations stay scoped to the authenticated user.</p>
                    </article>
                    <article class="landing-feature-card">
                        <h3>Multiple conversations</h3>
                        <p>Create separate chat threads for different tasks while preserving context.</p>
                    </article>
                    <article class="landing-feature-card">
                        <h3>Document ingestion</h3>
                        <p>Supports pasted text and text-based file uploads such as TXT, MD, CSV, JSON, and LOG.</p>
                    </article>
                    <article class="landing-feature-card">
                        <h3>Recent activity dashboard</h3>
                        <p>Track conversations, indexed sources, and jump directly into a fresh chat.</p>
                    </article>
                </div>
            </section>

            <section class="landing-bottom-cta">
                <div>
                    <span class="landing-kicker">Ready To Use It?</span>
                    <h2>Sign in and start building your private AI knowledge workspace.</h2>
                </div>
                <div class="landing-cta-row">
                    <a href="{{ route('login') }}" class="landing-btn landing-btn-outline">Login</a>
                    @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="landing-btn landing-btn-primary">Create Account</a>
                    @endif
                </div>
            </section>
        </main>
    </div>
    @include('layouts.partials.toasts')

    <footer class="landing-footer">
        <div class="landing-footer-inner">
            <p>&copy; {{ date('Y') }} My AI App. All rights reserved.</p>
            <p>Built for private, document-grounded AI workflows.</p>
        </div>

        <div class="landing-footer-meta">
            <p>Created by <a href="https://github.com/nirav-gajera" target="_blank" rel="noreferrer">nirav-gajera</a></p>
        </div>
    </footer>

    <button type="button" class="landing-back-to-top" id="landing-back-to-top" aria-label="Back to top">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="18 15 12 9 6 15" />
        </svg>
    </button>
</body>
</html>
