<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Stockz</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: system-ui, -apple-system, sans-serif;
        }
        .login-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 420px;
        }
    </style>
</head>
<body>
    <div class="card login-card p-4 bg-white">
        <div class="text-center mb-4">
            <h2 class="fw-bold text-dark mb-1"><i class="bi bi-box-seam-fill text-warning me-2"></i>Stockz</h2>
            <p class="text-muted small">Sistem Manajemen Stok Barang</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success small mb-3">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger small mb-3">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">Email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email', 'admin@stockz.com') }}" required autofocus>
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label fw-semibold">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" id="password" class="form-control" value="password" required>
                </div>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                <label class="form-check-label small" for="remember">Ingat Saya</label>
            </div>

            <button type="submit" class="btn btn-dark w-100 py-2 fw-semibold">
                <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
            </button>
        </form>

        <div class="mt-4 pt-3 border-top text-center text-muted small">
            <div class="fw-semibold mb-2">Akun Demo Default (Password: <code>password</code>):</div>
            <div class="d-flex justify-content-around small">
                <span class="badge bg-danger">Admin: admin@stockz.com</span>
                <span class="badge bg-primary">Staff: staff@stockz.com</span>
                <span class="badge bg-success">Owner: owner@stockz.com</span>
            </div>
        </div>
    </div>
</body>
</html>
