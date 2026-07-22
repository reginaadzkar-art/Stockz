<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Stockz</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        .navbar-brand {
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .card-stat {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: transform 0.2s;
        }
        .card-stat:hover {
            transform: translateY(-2px);
        }
        .role-badge {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 4px 8px;
            border-radius: 20px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
        <div class="container-fluid px-4">
            <a class="navbar-brand text-warning" href="{{ route('dashboard') }}">
                <i class="bi bi-box-seam-fill me-2"></i>Stockz
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="bi bi-speedometer2 me-1"></i> Dashboard
                        </a>
                    </li>

                    @auth
                        @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ request()->is('items*') || request()->is('categories*') || request()->is('suppliers*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-database me-1"></i> Data Master
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('items.index') }}"><i class="bi bi-box me-2"></i>Data Barang</a></li>
                                    <li><a class="dropdown-item" href="{{ route('categories.index') }}"><i class="bi bi-tags me-2"></i>Kategori Barang</a></li>
                                    <li><a class="dropdown-item" href="{{ route('suppliers.index') }}"><i class="bi bi-truck me-2"></i>Supplier</a></li>
                                </ul>
                            </li>
                        @endif

                        @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ request()->is('stock-movements*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-arrow-left-right me-1"></i> Transaksi Stok
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('stock-movements.in.create') }}"><i class="bi bi-arrow-down-left-circle text-success me-2"></i>Input Barang Masuk</a></li>
                                    <li><a class="dropdown-item" href="{{ route('stock-movements.out.create') }}"><i class="bi bi-arrow-up-right-circle text-danger me-2"></i>Input Barang Keluar</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('stock-movements.index') }}"><i class="bi bi-list-ul me-2"></i>Semua Transaksi</a></li>
                                </ul>
                            </li>
                        @endif

                        @if(Auth::user()->isAdmin() || Auth::user()->isOwner())
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ request()->is('reports*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-file-earmark-bar-graph me-1"></i> Laporan
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('reports.stock') }}"><i class="bi bi-card-checklist me-2"></i>Laporan Stok Barang</a></li>
                                    <li><a class="dropdown-item" href="{{ route('reports.transactions') }}"><i class="bi bi-clock-history me-2"></i>Histori Transaksi</a></li>
                                </ul>
                            </li>
                        @endif

                        @if(Auth::user()->isAdmin())
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                                    <i class="bi bi-people me-1"></i> Kelola User
                                </a>
                            </li>
                        @endif
                    @endauth
                </ul>

                @auth
                    <div class="d-flex align-items-center gap-3">
                        <div class="text-light text-end me-2">
                            <div class="fw-semibold">{{ Auth::user()->name }}</div>
                            @if(Auth::user()->isAdmin())
                                <span class="badge bg-danger role-badge">Admin</span>
                            @elseif(Auth::user()->isStaff())
                                <span class="badge bg-primary role-badge">Staff</span>
                            @elseif(Auth::user()->isOwner())
                                <span class="badge bg-success role-badge">Owner</span>
                            @endif
                        </div>
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-light btn-sm" title="Keluar">
                                <i class="bi bi-box-arrow-right me-1"></i> Logout
                            </button>
                        </form>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <main class="container-fluid px-4 py-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-octagon-fill me-2"></i><strong>Terjadi kesalahan input:</strong>
                <ul class="mb-0 mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
