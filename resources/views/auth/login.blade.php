<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Stockz Analytics Engine</title>

    <script>
        (function() {
            const savedTheme = localStorage.getItem('stockz_theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root, [data-theme="light"] {
            --bg-canvas: #f4f6f9;
            --surface-card: #ffffff;
            --brand-emerald: #0f543f;
            --brand-emerald-glow: rgba(15, 84, 63, 0.15);
            --royal-violet: #6d28d9;
            --border-light: #e2e8f0;
            --text-heading: #0f172a;
            --text-body: #334155;
            --input-bg: #f8fafc;
        }

        [data-theme="dark"], [data-bs-theme="dark"] {
            --bg-canvas: #0b0f19;
            --surface-card: #161e2e;
            --brand-emerald: #10b981;
            --brand-emerald-glow: rgba(16, 185, 129, 0.25);
            --royal-violet: #8b5cf6;
            --border-light: #2a3447;
            --text-heading: #f8fafc;
            --text-body: #cbd5e1;
            --input-bg: #111827;
        }

        body {
            background: var(--bg-canvas);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            color: var(--text-body);
            padding: 1.5rem;
            position: relative;
            transition: background-color 0.25s ease, color 0.25s ease;
        }

        .login-card {
            background: var(--surface-card);
            border: 1px solid var(--border-light);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.06);
            width: 100%;
            max-width: 440px;
            padding: 2.5rem;
            position: relative;
            overflow: hidden;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--brand-emerald), var(--royal-violet));
        }

        .brand-icon-large {
            width: 54px;
            height: 54px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--brand-emerald), #166534);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.75rem;
            margin: 0 auto 1.25rem;
            box-shadow: 0 4px 15px rgba(15, 84, 63, 0.25);
        }

        .form-control-light {
            background: #f8fafc;
            border: 1px solid var(--border-light);
            color: var(--text-heading);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            font-size: 0.92rem;
        }

        .form-control-light:focus {
            background: #ffffff;
            border-color: var(--brand-emerald);
            color: var(--text-heading);
            box-shadow: 0 0 0 3px var(--brand-emerald-glow);
        }

        .input-group-text-light {
            background: #f8fafc;
            border: 1px solid var(--border-light);
            border-right: none;
            color: #64748b;
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
        }

        .btn-emerald {
            background: linear-gradient(135deg, var(--brand-emerald), #166534);
            color: #ffffff;
            font-weight: 700;
            border: none;
            border-radius: 10px;
            padding: 0.8rem;
            transition: all 0.2s ease;
        }

        .btn-emerald:hover {
            color: #ffffff;
            box-shadow: 0 4px 15px rgba(15, 84, 63, 0.3);
            transform: translateY(-1px);
        }

        .demo-chip {
            background: var(--input-bg);
            border: 1px solid var(--border-light);
            color: var(--text-body);
            border-radius: 8px;
            padding: 6px 10px;
            font-size: 0.75rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .demo-chip:hover {
            background: var(--border-light);
            border-color: var(--brand-emerald);
        }

        /* Theme Toggle Button (Icon Sun & Moon Only) */
        .theme-toggle-btn {
            position: fixed;
            top: 1.5rem;
            right: 1.5rem;
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: var(--surface-card);
            border: 1px solid var(--border-light);
            color: var(--text-heading);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            outline: none;
            padding: 0;
            font-size: 1.2rem;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .theme-toggle-btn:hover {
            border-color: var(--brand-emerald);
            color: var(--brand-emerald);
            transform: translateY(-2px);
        }

        .sun-icon {
            display: none;
            color: #f59e0b;
        }

        .moon-icon {
            display: inline-block;
            color: #6d28d9;
        }

        [data-theme="dark"] .sun-icon {
            display: inline-block;
        }

        [data-theme="dark"] .moon-icon {
            display: none;
        }

        [data-theme="dark"] .text-dark {
            color: var(--text-heading) !important;
        }

        [data-theme="dark"] .form-control-light,
        [data-theme="dark"] .input-group-text-light {
            background-color: var(--input-bg);
            border-color: var(--border-light);
            color: var(--text-heading);
        }

        [data-theme="dark"] .form-control-light:focus {
            background-color: var(--input-bg);
            border-color: var(--brand-emerald);
            color: var(--text-heading);
        }
    </style>
</head>
<body>

    <button type="button" class="theme-toggle-btn" id="themeToggleBtn" title="Mode Terang / Gelap" aria-label="Toggle Theme">
        <i class="bi bi-sun-fill sun-icon"></i>
        <i class="bi bi-moon-stars-fill moon-icon"></i>
    </button>

    <div class="login-card">
        <div class="text-center mb-4">
            <div class="brand-icon-large">
                <i class="bi bi-box-seam-fill"></i>
            </div>
            <h3 class="fw-bold text-dark mb-1">Stockz Engine</h3>
            <p class="text-muted small">Real-Time Inventory & Financial Analytics</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 small mb-3 p-3 rounded-3" style="background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.3) !important;">
                <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger border-0 small mb-3 p-3 rounded-3" style="background: rgba(220, 38, 38, 0.12); border: 1px solid rgba(220, 38, 38, 0.3) !important;">
                <i class="bi bi-exclamation-octagon me-1"></i> {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label text-muted small fw-semibold">Email Account</label>
                <div class="input-group">
                    <span class="input-group-text input-group-text-light"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" id="email" class="form-control form-control-light font-mono" value="{{ old('email', 'admin@stockz.com') }}" required autofocus>
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label text-muted small fw-semibold">Password</label>
                <div class="input-group">
                    <span class="input-group-text input-group-text-light"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" id="password" class="form-control form-control-light font-mono" value="password" required>
                </div>
            </div>

            <div class="mb-4 form-check">
                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                <label class="form-check-label text-muted small" for="remember">Ingat Sesi Login Saya</label>
            </div>

            <button type="submit" class="btn btn-emerald w-100 mb-3">
                <i class="bi bi-box-arrow-in-right me-1"></i> Masuk Ke System
            </button>
        </form>

        <div class="mt-4 pt-3 border-top">
            <div class="text-muted extra-small text-center mb-2 fw-semibold">Klik Akses Demo Cepat (Pass: <code class="font-mono text-success">password</code>):</div>
            <div class="d-flex justify-content-between gap-1">
                <div class="demo-chip text-dark flex-fill text-center" onclick="fillLogin('admin@stockz.com')">
                    <span class="text-danger fw-bold">Admin</span>
                </div>
                <div class="demo-chip text-dark flex-fill text-center" onclick="fillLogin('staff@stockz.com')">
                    <span class="text-success fw-bold">Staff</span>
                </div>
                <div class="demo-chip text-dark flex-fill text-center" onclick="fillLogin('owner@stockz.com')">
                    <span style="color: #6d28d9;" class="fw-bold">Owner</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        function fillLogin(email) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = 'password';
        }

        document.addEventListener('DOMContentLoaded', function() {
            const themeBtn = document.getElementById('themeToggleBtn');
            if (themeBtn) {
                themeBtn.addEventListener('click', function() {
                    const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
                    const nextTheme = currentTheme === 'dark' ? 'light' : 'dark';
                    document.documentElement.setAttribute('data-bs-theme', nextTheme);
                    document.documentElement.setAttribute('data-theme', nextTheme);
                    localStorage.setItem('stockz_theme', nextTheme);
                });
            }
        });
    </script>
</body>
</html>
