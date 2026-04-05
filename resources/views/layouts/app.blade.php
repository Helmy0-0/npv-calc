<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SPK Kelayakan Investasi') | NPV Calculator</title>

    {{-- Google Fonts: Syne (display) + DM Sans (body) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&display=swap" rel="stylesheet">

    <style>
        /* ============================================================
           GLOBAL CSS VARIABLES & RESET
           ============================================================ */
        :root {
            --clr-bg:        #0a0f1e;
            --clr-surface:   #111827;
            --clr-surface-2: #1a2235;
            --clr-border:    #1f2d45;
            --clr-accent:    #00d4aa;
            --clr-accent-2:  #0ea5e9;
            --clr-text:      #e2e8f0;
            --clr-muted:     #64748b;
            --clr-danger:    #f43f5e;
            --clr-success:   #10b981;
            --clr-warning:   #f59e0b;

            --font-display:  'Syne', sans-serif;
            --font-body:     'DM Sans', sans-serif;

            --radius-sm:  6px;
            --radius-md:  12px;
            --radius-lg:  20px;

            --shadow-glow: 0 0 40px rgba(0, 212, 170, 0.08);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html { scroll-behavior: smooth; }

        body {
            font-family: var(--font-body);
            background-color: var(--clr-bg);
            color: var(--clr-text);
            min-height: 100vh;
            line-height: 1.6;
            /* Subtle grid background */
            background-image:
                linear-gradient(rgba(0,212,170,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0,212,170,0.03) 1px, transparent 1px);
            background-size: 40px 40px;
        }

        /* ── NAVIGATION ── */
        .navbar {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(10,15,30,0.85);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--clr-border);
            padding: 0 2rem;
        }

        .navbar-inner {
            max-width: 1100px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 60px;
        }

        .navbar-brand {
            font-family: var(--font-display);
            font-weight: 800;
            font-size: 1.1rem;
            color: var(--clr-accent);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .navbar-brand::before {
            content: '◈';
            font-size: 1.2rem;
        }

        .navbar-nav {
            display: flex;
            gap: 1.5rem;
            list-style: none;
        }

        .navbar-nav a {
            color: var(--clr-muted);
            text-decoration: none;
            font-size: .875rem;
            font-weight: 500;
            transition: color .2s;
        }

        .navbar-nav a:hover { color: var(--clr-accent); }

        /* ── MAIN WRAPPER ── */
        .main-content {
            max-width: 1100px;
            margin: 0 auto;
            padding: 3rem 2rem;
        }

        /* ── PAGE HEADER ── */
        .page-header {
            margin-bottom: 2.5rem;
        }

        .page-header .badge {
            display: inline-block;
            font-size: .7rem;
            font-weight: 600;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: var(--clr-accent);
            background: rgba(0,212,170,.1);
            border: 1px solid rgba(0,212,170,.2);
            padding: .3rem .75rem;
            border-radius: 100px;
            margin-bottom: 1rem;
        }

        .page-header h1 {
            font-family: var(--font-display);
            font-size: clamp(1.8rem, 4vw, 2.8rem);
            font-weight: 800;
            line-height: 1.15;
            color: #fff;
        }

        .page-header h1 span {
            background: linear-gradient(135deg, var(--clr-accent), var(--clr-accent-2));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .page-header p {
            margin-top: .75rem;
            color: var(--clr-muted);
            font-size: .95rem;
            max-width: 520px;
        }

        /* ── CARD ── */
        .card {
            background: var(--clr-surface);
            border: 1px solid var(--clr-border);
            border-radius: var(--radius-lg);
            padding: 2rem;
            box-shadow: var(--shadow-glow);
        }

        /* ── FOOTER ── */
        .footer {
            text-align: center;
            padding: 2rem;
            color: var(--clr-muted);
            font-size: .8rem;
            border-top: 1px solid var(--clr-border);
            margin-top: 4rem;
        }

        .footer strong { color: var(--clr-accent); }

        /* ── ALERT / ERROR ── */
        .alert-error {
            background: rgba(244,63,94,.08);
            border: 1px solid rgba(244,63,94,.25);
            border-radius: var(--radius-md);
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            color: #fda4af;
            font-size: .875rem;
        }

        .alert-error ul { padding-left: 1.2rem; margin-top: .5rem; }
        .alert-error li { margin-bottom: .25rem; }
    </style>

    @stack('styles')
</head>
<body>

    {{-- ── NAVIGATION ── --}}
    <nav class="navbar">
        <div class="navbar-inner">
            <a href="{{ route('npv.index') }}" class="navbar-brand">SPK Investasi</a>
            <ul class="navbar-nav">
                <li><a href="{{ route('npv.index') }}">Kalkulator</a></li>
                <li><a href="{{ route('npv.history') }}">Riwayat</a></li>
            </ul>
        </div>
    </nav>

    {{-- ── CONTENT ── --}}
    <main class="main-content">

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="alert-error">
                <strong>⚠ Terdapat kesalahan input:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    {{-- ── FOOTER ── --}}
    <footer class="footer">
        <p>SPK Kelayakan Investasi &mdash; Metode <strong>Net Present Value (NPV)</strong> &mdash; Dibangun dengan Laravel</p>
    </footer>

    @stack('scripts')
</body>
</html>