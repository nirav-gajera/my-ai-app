<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'My AI App') }} | Knowledge Workspace</title>
    <meta name="description" content="Build a private knowledge base, index documents, and chat with grounded AI answers backed by your own content.">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600&family=Space+Grotesk:wght@400;500;700&display=swap" rel="stylesheet" />
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
                    <strong>{{ config('app.name', 'My AI App') }}</strong>
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
                        Upload policies, manuals, FAQs, notes, CSVs, JSON files, PDF or pasted text.
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
                        <li>Re-index documents</li>
                        <li>Telegram Bot Support</li>
                    </ul>

                    <div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid rgba(19, 41, 72, 0.08);">
                        <span style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.1em; display: block; margin-bottom: 16px;">Platform Navigation</span>
                        <div style="display: flex; gap: 16px; flex-wrap: wrap;">
                            <a href="#how-it-works" style="display: flex; align-items: center; gap: 8px; font-size: 13.5px; font-weight: 700; color: #1d4ed8; text-decoration: none; transition: transform 0.2s;" onmouseover="this.style.transform='translateX(4px)'" onmouseout="this.style.transform='translateX(0)'">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                                How it works
                            </a>
                            <a href="#features" style="display: flex; align-items: center; gap: 8px; font-size: 13.5px; font-weight: 700; color: #1d4ed8; text-decoration: none; transition: transform 0.2s;" onmouseover="this.style.transform='translateX(4px)'" onmouseout="this.style.transform='translateX(0)'">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                                View features
                            </a>
                        </div>
                    </div>
                </div>

                <div class="landing-hero-media">
                <div class="landing-hero-card">
                    <div class="landing-window">
                        <div class="landing-window-bar">
                            <span></span><span></span><span></span>
                            <div class="landing-window-path"> 
                                <div class="landing-green-dot" aria-hidden="true"></div>
                               my-ai-app/conversations</div>
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

                    <aside class="landing-telegram-card" aria-label="Telegram bot preview">
                        <div class="landing-telegram-card-top">
                            <div class="landing-telegram-badge" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M9.06 16.32 8.8 20.1c.38 0 .55-.16.75-.36l1.82-1.75 3.78 2.77c.69.38 1.18.18 1.36-.64l2.47-11.59h0c.22-1.02-.37-1.42-1.04-1.17L3.48 10.72c-1 .39-.99.95-.17 1.2l4.3 1.34L17.6 7.3c.47-.31.9-.14.55.18" />
                                </svg>
                            </div>

                            <div class="landing-telegram-meta">
                                <span class="landing-telegram-label">Telegram bot</span>
                                <strong>My AI App bot</strong>
                            </div>
                        </div>

                        <div class="landing-telegram-thread">
                            <article class="landing-telegram-message landing-telegram-message-user">
                                <span class="landing-telegram-message-label">You</span>
                                <p>/start</p>
                            </article>

                            <article class="landing-telegram-message landing-telegram-message-bot">
                                <span class="landing-telegram-message-label">My AI App bot</span>
                                    <p>Welcome back, user!<br>
                                    I've synced your 10 documents. How can I help you today?</p>
                            </article>
                        </div>
                    </aside>
                </div>
            </section>

            <section class="landing-section" id="how-it-works">
                <div class="landing-section-head">
                    <span class="landing-kicker">How It Works</span>
                    <h2>Built for document-grounded answers.</h2>
                </div>

                <div class="landing-steps">
                    <article class="landing-step-card reveal-on-scroll">
                        <span class="landing-step-index">01</span>
                        <h3>Index knowledge</h3>
                        <p>
                            Add text directly or upload supported files. Each document is stored,
                            normalized, chunked, and prepared for retrieval.
                        </p>
                    </article>
                    <article class="landing-step-card reveal-on-scroll">
                        <span class="landing-step-index">02</span>
                        <h3>Retrieve context</h3>
                        <p>
                            The system embeds the question, compares it against stored chunk embeddings,
                            and selects the highest-similarity matches from the current user's data.
                        </p>
                    </article>
                    <article class="landing-step-card reveal-on-scroll">
                        <span class="landing-step-index">03</span>
                        <h3>Answer with evidence</h3>
                        <p>
                            The assistant responds using retrieved knowledge plus recent conversation history,
                            and returns citations for the underlying source chunks.
                        </p>
                    </article>
                    <article class="landing-step-card reveal-on-scroll">
                        <span class="landing-step-index">04</span>
                        <h3>Telegram Support</h3>
                        <p>
                            Link your Telegram account and chat with your AI assistant directly.
                            Upload files and ask questions in natural language—the assistant will respond
                            based on indexed documents.
                        </p>
                    </article>
                </div>
            </section>

            <section class="landing-section landing-section-alt" id="features">
                <div class="landing-section-head">
                    <span class="landing-kicker">What You Get</span>
                    <h2>Everything centered around your own data.</h2>
                </div>

                <div class="landing-feature-grid">
                    <article class="landing-feature-card reveal-on-scroll">
                        <div class="landing-feature-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        </div>
                        <h3>User-scoped knowledge base</h3>
                        <p>Documents and conversations stay scoped to the authenticated user.</p>
                    </article>
                    <article class="landing-feature-card reveal-on-scroll">
                        <div class="landing-feature-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        </div>
                        <h3>Multiple conversations</h3>
                        <p>Create separate chat threads for different tasks while preserving context.</p>
                    </article>
                    <article class="landing-feature-card reveal-on-scroll">
                        <div class="landing-feature-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                        </div>
                        <h3>Document ingestion</h3>
                        <p>Supports pasted text and text-based file uploads such as TXT, MD, CSV, JSON, PDF and LOG.</p>
                    </article>
                    <article class="landing-feature-card reveal-on-scroll">
                        <div class="landing-feature-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 4v6h-6"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                        </div>
                        <h3>Re-indexing Support</h3>
                        <p>Re-index documents to update their content and embeddings.</p>
                    </article>
                    <article class="landing-feature-card reveal-on-scroll">
                        <div class="landing-feature-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                        </div>
                        <h3>Recent activity dashboard</h3>
                        <p>Track conversations, indexed sources, and jump directly into a fresh chat.</p>
                    </article>
                    <article class="landing-feature-card reveal-on-scroll">
                        <div class="landing-feature-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
                        </div>
                        <h3>Telegram access</h3>
                        <p>Link your account, chat from Telegram, and send files directly into the workspace.</p>
                    </article>
                </div>
            </section>

            <section class="landing-bottom-cta">
                <div class="landing-bottom-cta-copy">
                    <span class="landing-kicker">Ready To Use It?</span>
                    <h2>Sign in and start building your private AI knowledge workspace.</h2>
                    <p class="landing-bottom-cta-lead">
                        Keep your documents private, ask grounded questions, and extend the same experience through Telegram.
                    </p>
                    <div class="landing-cta-points" aria-label="Highlights">
                        <span class="landing-cta-point">
                            <span class="landing-green-dot" aria-hidden="true"></span>
                            Private by design
                        </span>
                        <span class="landing-cta-point">
                            <span class="landing-green-dot" aria-hidden="true"></span>
                            Document-backed answers
                        </span>
                        <span class="landing-cta-point">
                            <span class="landing-green-dot" aria-hidden="true"></span>
                            Telegram ready
                        </span>
                    </div>
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
            <div class="landing-footer-brand">
                <span class="landing-footer-mark" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                    </svg>
                </span>
                <div class="landing-footer-copy">
                    <p class="landing-footer-title">{{ config('app.name', 'My AI App') }}</p>
                    <p class="landing-footer-description">Private knowledge workspace for document-grounded AI and Telegram access.</p>
                </div>
            </div>

            <div class="landing-footer-badges" aria-label="Product highlights">
                <span>RAG search</span>
                <span>Citations</span>
                <span>Telegram bot</span>
            </div>
        </div>

        <div class="landing-footer-meta">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'My AI App') }}. All rights reserved.</p>
            <p>Created by <a href="https://github.com/nirav-gajera" target="_blank" rel="noreferrer">nirav-gajera</a></p>
        </div>
    </footer>

    <button type="button" class="landing-back-to-top" id="landing-back-to-top" aria-label="Back to top">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="18 15 12 9 6 15" />
        </svg>
    </button>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Scroll reveal logic
            const observerOptions = {
                threshold: 0.1
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.reveal-on-scroll').forEach((el) => {
                observer.observe(el);
            });

            // Custom Smooth Scroll Logic (Slower & Premium)
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href');
                    const targetElement = document.querySelector(targetId);

                    if (targetElement) {
                        const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset;
                        const startPosition = window.pageYOffset;
                        const distance = targetPosition - startPosition;
                        const duration = 1000; // Increased duration for a slower, smoother feel
                        let start = null;

                        window.requestAnimationFrame(step);

                        function step(timestamp) {
                            if (!start) start = timestamp;
                            const progress = timestamp - start;
                            
                            // Cubic Bezier-like Easing (OutCubic)
                            const t = progress / duration;
                            const easing = 1 - Math.pow(1 - t, 3);
                            
                            window.scrollTo(0, startPosition + distance * easing);
                            
                            if (progress < duration) {
                                window.requestAnimationFrame(step);
                            }
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>
