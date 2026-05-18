<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $page->title }} | {{ config('app.name', 'My AI App') }}</title>
    <meta name="description" content="{{ $page->title }} for {{ config('app.name') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600&family=Space+Grotesk:wght@400;500;700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .landing-header, .landing-shell, .landing-footer {
            z-index: 1;
        }
        .prose {
            line-height: 1.8;
            font-size: 1.05rem;
            color: #334155;
        }
        .prose h1, .prose h2, .prose h3 {
            color: #0c1a31;
            margin-top: 2.5rem;
            /* margin-bottom: 1.25rem; */
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            letter-spacing: -0.02em;
        }
        .prose p {
            margin-bottom: 1.5rem;
        }
        .prose ul {
            list-style-type: disc;
            padding-left: 1.5rem;
            /* margin-bottom: 1.5rem; */
        }
        .prose li {
            /* margin-bottom: 0.5rem; */
        }
    </style>
</head>
<body class="landing-body" id="top">
    <canvas id="bg-canvas" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: 0; pointer-events: none; opacity: 1;"></canvas>
    <header class="landing-header">
        <div class="landing-header-inner">
            <a href="{{ route('welcome') }}" class="landing-brand">
                <span class="landing-brand-mark">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                    </svg>
                </span>
                <span class="landing-brand-copy">
                    <strong>{{ config('app.name', 'My AI App') }}</strong>
                    <span>{{ config('app.sub_title','Private knowledge workspace') }}</span>
                </span>
            </a>

            @if (!Auth::user())
            <nav class="landing-actions">
                <a href="{{ route('login') }}" class="landing-btn landing-btn-ghost">Login</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="landing-btn landing-btn-primary">Register</a>
                @endif
            </nav>
            @else
            <nav class="landing-actions">
                <a href="{{ route('dashboard') }}" class="landing-btn landing-btn-primary">Dashboard</a>
            </nav>
            @endif
        </div>
    </header>

    <div class="landing-shell">
        <main class="landing-main">
            <section class="landing-section">
                <div class="landing-section-head">
                    <span class="landing-kicker">{{ $page->title }}</span>
                    <h2 style="font-size: clamp(2rem, 4vw, 2.5rem); line-height: 1.1;">{{ $page->content_heading }}</h2>
                </div>

                <div class="prose mt-12">
                    {!! ($content->content) !!}
                </div>

                <div class="mt-16 pt-8 border-t border-gray-100 flex items-center justify-between text-sm text-gray-500">
                    <p>Published: {{ $content->active_from ? $content->active_from->format('M d, Y') : $content->created_at->format('M d, Y') }}</p>
                    <p>Last updated: {{ $content->updated_at->format('M d, Y') }}</p>
                </div>
            </section>
        </main>
    </div>

    <footer class="landing-footer">
        <div class="landing-footer-inner">
            <div class="landing-footer-brand">
                <span class="landing-footer-mark">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                    </svg>
                </span>
                <div class="landing-footer-copy">
                    <p class="landing-footer-title">{{ config('app.name', 'My AI App') }}</p>
                    <p class="landing-footer-description">Private knowledge workspace for document-grounded AI and Telegram access.</p>
                </div>
            </div>
        </div>

        <div class="landing-footer-meta">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'My AI App') }}. All rights reserved.</p>
        </div>
    </footer>
    <script>
        const canvas = document.getElementById('bg-canvas');
        const ctx = canvas.getContext('2d');
        let width, height, particles;
        

        function initAnimation() {
            width = canvas.width = window.innerWidth;
            height = canvas.height = window.innerHeight;
            particles = [];

            const numParticles = Math.floor((width * height) / 12000);

            for (let i = 0; i < numParticles; i++) {
                particles.push({
                    x: Math.random() * width,
                    y: Math.random() * height,
                    vx: (Math.random() - 0.5) * 0.8,
                    vy: (Math.random() - 0.5) * 0.8,
                    radius: Math.random() * 1.5 + 0.5
                });
            }
        }

        window.addEventListener('resize', initAnimation);
        initAnimation();

        function animateBg() {
            ctx.clearRect(0, 0, width, height);
            
            for (let i = 0; i < particles.length; i++) {
                let p = particles[i];
                p.x += p.vx;
                p.y += p.vy;
                
                if (p.x < 0 || p.x > width) p.vx *= -1;
                if (p.y < 0 || p.y > height) p.vy *= -1;
                
                ctx.beginPath();
                ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
                ctx.fillStyle = 'rgba(15, 118, 110, 0.65)';
                ctx.fill();
                
                for (let j = i + 1; j < particles.length; j++) {
                    let p2 = particles[j];
                    let dx = p.x - p2.x;
                    let dy = p.y - p2.y;
                    let distance = Math.sqrt(dx * dx + dy * dy);
                    
                    if (distance < 120) {
                        ctx.beginPath();
                        ctx.strokeStyle = `rgba(29, 78, 216, ${0.30 * (1 - distance/120)})`;
                        ctx.lineWidth = 0.8;
                        ctx.moveTo(p.x, p.y);
                        ctx.lineTo(p2.x, p2.y);
                        ctx.stroke();
                    }
                }
            }
            requestAnimationFrame(animateBg);
        }
        animateBg();
    </script>
</body>
</html>
