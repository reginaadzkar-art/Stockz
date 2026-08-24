<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Stockz Command Engine</title>
    
    <script>
        (function() {
            const savedTheme = localStorage.getItem('stockz_theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>

    <!-- Google Fonts: Plus Jakarta Sans & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <style>
        :root, [data-theme="light"] {
            --bg-canvas: #f4f6f9;
            --surface-white: #ffffff;
            --surface-hover: #f8fafc;
            --brand-emerald: #0f543f;
            --brand-emerald-light: #10b981;
            --brand-emerald-glow: rgba(16, 185, 129, 0.15);
            --royal-violet: #6d28d9;
            --royal-violet-glow: rgba(109, 40, 217, 0.12);
            --coral-alert: #dc2626;
            --coral-glow: rgba(220, 38, 38, 0.12);
            --amber-warn: #d97706;
            --text-heading: #0f172a;
            --text-body: #334155;
            --text-muted: #64748b;
            --border-light: #e2e8f0;
            --border-light-subtle: #f1f5f9;
            --sidebar-width: 270px;
            --topbar-bg: rgba(255, 255, 255, 0.85);
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            --table-header-bg: #f8fafc;
            --input-bg: #ffffff;
        }

        [data-theme="dark"], [data-bs-theme="dark"] {
            --bg-canvas: #0b0f19;
            --surface-white: #161e2e;
            --surface-hover: #1f293d;
            --brand-emerald: #10b981;
            --brand-emerald-light: #34d399;
            --brand-emerald-glow: rgba(16, 185, 129, 0.25);
            --royal-violet: #8b5cf6;
            --royal-violet-glow: rgba(139, 92, 246, 0.2);
            --coral-alert: #ef4444;
            --coral-glow: rgba(239, 68, 68, 0.2);
            --amber-warn: #f59e0b;
            --text-heading: #f8fafc;
            --text-body: #cbd5e1;
            --text-muted: #94a3b8;
            --border-light: #2a3447;
            --border-light-subtle: #1e293b;
            --sidebar-width: 270px;
            --topbar-bg: rgba(22, 30, 46, 0.85);
            --card-shadow: 0 4px 25px rgba(0, 0, 0, 0.35);
            --table-header-bg: #1e293d;
            --input-bg: #111827;
        }

        body {
            background-color: var(--bg-canvas);
            color: var(--text-body);
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
            transition: background-color 0.25s ease, color 0.25s ease;
        }

        /* Typography */
        h1, h2, h3, h4, h5, h6, .fw-heading {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text-heading);
            letter-spacing: -0.02em;
        }

        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }

        /* Layout Structure */
        .app-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styling (Donezo Light Style) */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--surface-white);
            border-right: 1px solid var(--border-light);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1040;
            transition: transform 0.3s ease;
        }

        .sidebar-brand {
            padding: 1.5rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-bottom: 1px solid var(--border-light-subtle);
        }

        .brand-logo-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--brand-emerald), #166534);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 1.35rem;
            box-shadow: 0 4px 12px rgba(15, 84, 63, 0.2);
        }

        .brand-title {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--text-heading);
            margin: 0;
            line-height: 1.1;
        }

        .brand-subtitle {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--brand-emerald);
            font-weight: 700;
        }

        .sidebar-menu {
            padding: 1.25rem 1rem;
            flex: 1;
            overflow-y: auto;
        }

        .menu-label {
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: var(--text-muted);
            padding: 0.75rem 0.75rem 0.35rem;
        }

        .nav-link-custom {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            padding: 0.7rem 1rem;
            color: var(--text-body);
            border-radius: 10px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            margin-bottom: 0.25rem;
        }

        .nav-link-custom i {
            font-size: 1.15rem;
            color: var(--text-muted);
            transition: color 0.2s ease;
        }

        .nav-link-custom:hover {
            color: var(--text-heading);
            background: var(--surface-hover);
        }

        .nav-link-custom.active {
            background: rgba(15, 84, 63, 0.08);
            color: var(--brand-emerald);
            font-weight: 700;
            border: 1px solid rgba(15, 84, 63, 0.2);
        }

        .nav-link-custom.active i {
            color: var(--brand-emerald);
        }

        .sidebar-user {
            padding: 1.25rem;
            border-top: 1px solid var(--border-light);
            background: var(--surface-hover);
        }

        .user-card {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: var(--surface-white);
            padding: 0.75rem;
            border-radius: 12px;
            border: 1px solid var(--border-light);
        }

        .avatar-box {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--royal-violet), #4c1d95);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 0.95rem;
            box-shadow: 0 4px 10px rgba(109, 40, 217, 0.2);
        }

        /* Main Content Area */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        /* Top Bar Header */
        .topbar {
            height: 70px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-light);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        .search-trigger-btn {
            background: var(--bg-canvas);
            border: 1px solid var(--border-light);
            color: var(--text-muted);
            padding: 0.55rem 1.1rem;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s ease;
            width: 320px;
        }

        .search-trigger-btn:hover {
            border-color: #cbd5e1;
            color: var(--text-heading);
            background: #e2e8f0;
        }

        .shortcut-chip {
            margin-left: auto;
            background: var(--surface-white);
            border: 1px solid var(--border-light);
            border-radius: 6px;
            padding: 2px 6px;
            font-size: 0.7rem;
            font-family: 'JetBrains Mono', monospace;
            color: var(--text-muted);
        }

        /* Light Cards & Glass Elements */
        .glass-card {
            background: var(--surface-white);
            border: 1px solid var(--border-light);
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .glass-card:hover {
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
        }

        .glass-card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border-light-subtle);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* Status & Role Badges */
        .role-pill {
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 3px 9px;
            border-radius: 20px;
        }

        .role-admin {
            background: rgba(220, 38, 38, 0.1);
            color: var(--coral-alert);
            border: 1px solid rgba(220, 38, 38, 0.2);
        }

        .role-staff {
            background: rgba(15, 84, 63, 0.1);
            color: var(--brand-emerald);
            border: 1px solid rgba(15, 84, 63, 0.2);
        }

        .role-owner {
            background: rgba(109, 40, 217, 0.1);
            color: var(--royal-violet);
            border: 1px solid rgba(109, 40, 217, 0.2);
        }

        .badge-emerald {
            background: rgba(16, 185, 129, 0.12);
            color: #065f46;
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 20px;
            padding: 4px 10px;
            font-weight: 600;
        }

        .badge-coral {
            background: rgba(220, 38, 38, 0.12);
            color: #991b1b;
            border: 1px solid rgba(220, 38, 38, 0.3);
            border-radius: 20px;
            padding: 4px 10px;
            font-weight: 600;
        }

        /* Custom Table Styling (Light) */
        .table-custom {
            color: var(--text-body);
            margin: 0;
        }

        .table-custom thead th {
            background: #f8fafc;
            color: var(--text-muted);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            font-weight: 700;
            border-bottom: 1px solid var(--border-light);
            padding: 0.9rem 1.25rem;
        }

        .table-custom tbody td {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--border-light-subtle);
            vertical-align: middle;
            font-size: 0.9rem;
        }

        .table-custom tbody tr:hover td {
            background: #f8fafc;
        }

        /* Form Controls Light */
        .form-control-custom, .form-select-custom {
            background: var(--surface-white);
            border: 1px solid var(--border-light);
            color: var(--text-heading);
            border-radius: 10px;
            padding: 0.65rem 1rem;
            font-size: 0.9rem;
        }

        .form-control-custom:focus, .form-select-custom:focus {
            background: var(--surface-white);
            border-color: var(--brand-emerald);
            color: var(--text-heading);
            box-shadow: 0 0 0 3px var(--brand-emerald-glow);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: var(--bg-canvas);
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Theme Toggle Button (Icon Sun & Moon Only) */
        .theme-toggle-btn {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: var(--surface-white);
            border: 1px solid var(--border-light);
            color: var(--text-heading);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            outline: none;
            padding: 0;
            font-size: 1.15rem;
        }

        .theme-toggle-btn:hover {
            background: var(--surface-hover);
            border-color: var(--brand-emerald);
            color: var(--brand-emerald);
            transform: translateY(-1px);
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

        /* Comprehensive Dark Theme Overrides for Bootstrap elements & custom components */
        [data-theme="dark"], [data-bs-theme="dark"] {
            color-scheme: dark;
        }

        [data-theme="dark"] .topbar {
            background: var(--topbar-bg) !important;
            border-bottom-color: var(--border-light) !important;
        }

        [data-theme="dark"] .search-trigger-btn {
            background: var(--surface-white) !important;
            border-color: var(--border-light) !important;
            color: var(--text-muted) !important;
        }

        [data-theme="dark"] .search-trigger-btn:hover {
            background: var(--surface-hover) !important;
            color: var(--text-heading) !important;
            border-color: var(--brand-emerald) !important;
        }

        [data-theme="dark"] .shortcut-chip {
            background: var(--surface-hover) !important;
            border-color: var(--border-light) !important;
            color: var(--text-muted) !important;
        }

        [data-theme="dark"] .sidebar {
            background: var(--sidebar-bg) !important;
            border-right-color: var(--border-light) !important;
        }

        [data-theme="dark"] .sidebar-brand {
            border-bottom-color: var(--border-light-subtle) !important;
        }

        [data-theme="dark"] .sidebar-user {
            background: var(--surface-hover) !important;
            border-top-color: var(--border-light) !important;
        }

        [data-theme="dark"] .user-card {
            background: var(--surface-white) !important;
            border-color: var(--border-light) !important;
        }

        [data-theme="dark"] .text-dark,
        [data-theme="dark"] h1, [data-theme="dark"] h2, [data-theme="dark"] h3,
        [data-theme="dark"] h4, [data-theme="dark"] h5, [data-theme="dark"] h6 {
            color: var(--text-heading) !important;
        }

        [data-theme="dark"] .text-muted {
            color: var(--text-muted) !important;
        }

        [data-theme="dark"] .bg-white,
        [data-theme="dark"] .bg-light,
        [data-theme="dark"] .bg-body {
            background-color: var(--surface-white) !important;
            color: var(--text-body) !important;
        }

        [data-theme="dark"] .glass-card,
        [data-theme="dark"] .card {
            background-color: var(--surface-white) !important;
            background: var(--surface-white) !important;
            border-color: var(--border-light) !important;
            color: var(--text-body) !important;
            box-shadow: var(--card-shadow);
        }

        [data-theme="dark"] .glass-card-header,
        [data-theme="dark"] .card-header,
        [data-theme="dark"] .card-footer {
            background-color: var(--surface-white) !important;
            background: var(--surface-white) !important;
            border-color: var(--border-light) !important;
            color: var(--text-heading) !important;
        }

        /* Overriding inline style background gradients & hardcoded light containers */
        [data-theme="dark"] [style*="background: linear-gradient(145deg, #ffffff, #fefefe)"],
        [data-theme="dark"] [style*="background: linear-gradient(135deg, #ecfdf5"],
        [data-theme="dark"] [style*="background: linear-gradient(135deg, #fef2f2"],
        [data-theme="dark"] [style*="background: #ffffff"],
        [data-theme="dark"] [style*="background:#ffffff"],
        [data-theme="dark"] [style*="background: #f8fafc"],
        [data-theme="dark"] [style*="background:#f8fafc"],
        [data-theme="dark"] [style*="background: #f1f5f9"],
        [data-theme="dark"] [style*="background:#f1f5f9"] {
            background: var(--surface-white) !important;
            border-color: var(--border-light) !important;
        }

        [data-theme="dark"] [style*="border: 1px solid #e2e8f0"],
        [data-theme="dark"] [style*="border-color: #e2e8f0"] {
            border-color: var(--border-light) !important;
        }

        /* Tables & Headers */
        [data-theme="dark"] .table,
        [data-theme="dark"] .table-custom,
        [data-theme="dark"] .table > :not(caption) > * > * {
            background-color: var(--surface-white) !important;
            color: var(--text-body) !important;
            border-color: var(--border-light) !important;
        }

        [data-theme="dark"] .table-light,
        [data-theme="dark"] .table-custom thead th,
        [data-theme="dark"] .table thead th,
        [data-theme="dark"] .table tfoot td,
        [data-theme="dark"] .table tfoot th {
            background-color: var(--table-header-bg) !important;
            color: var(--text-heading) !important;
            border-color: var(--border-light) !important;
        }

        [data-theme="dark"] .table-hover tbody tr:hover td,
        [data-theme="dark"] .table-custom tbody tr:hover td {
            background-color: var(--surface-hover) !important;
            color: var(--text-heading) !important;
        }

        [data-theme="dark"] .table-bordered,
        [data-theme="dark"] .table-bordered td,
        [data-theme="dark"] .table-bordered th {
            border-color: var(--border-light) !important;
        }

        /* Tabs (nav-tabs & nav-pills) */
        [data-theme="dark"] .nav-tabs {
            border-bottom-color: var(--border-light) !important;
        }

        [data-theme="dark"] .nav-tabs .nav-link {
            color: var(--text-muted) !important;
            background-color: transparent !important;
            border-color: transparent !important;
        }

        [data-theme="dark"] .nav-tabs .nav-link:hover {
            color: var(--text-heading) !important;
            border-color: var(--border-light) !important;
        }

        [data-theme="dark"] .nav-tabs .nav-link.active {
            color: var(--brand-emerald-light) !important;
            background-color: var(--surface-white) !important;
            border-color: var(--border-light) var(--border-light) var(--surface-white) !important;
            font-weight: 700;
        }

        [data-theme="dark"] .nav-pills .nav-link {
            color: var(--text-muted) !important;
            background-color: var(--surface-hover) !important;
        }

        [data-theme="dark"] .nav-pills .nav-link.active {
            background-color: var(--brand-emerald) !important;
            color: #ffffff !important;
        }

        /* Form Controls & Inputs */
        [data-theme="dark"] .form-control,
        [data-theme="dark"] .form-select,
        [data-theme="dark"] .form-control-custom,
        [data-theme="dark"] .form-select-custom,
        [data-theme="dark"] .input-group-text,
        [data-theme="dark"] .input-group-text-light {
            background-color: var(--input-bg) !important;
            border-color: var(--border-light) !important;
            color: var(--text-heading) !important;
        }

        [data-theme="dark"] .form-control:focus,
        [data-theme="dark"] .form-select:focus,
        [data-theme="dark"] .form-control-custom:focus,
        [data-theme="dark"] .form-select-custom:focus {
            background-color: var(--input-bg) !important;
            border-color: var(--brand-emerald) !important;
            color: var(--text-heading) !important;
            box-shadow: 0 0 0 3px var(--brand-emerald-glow) !important;
        }

        /* Dropdowns, Modals, Offcanvas */
        [data-theme="dark"] .dropdown-menu {
            background-color: var(--surface-white) !important;
            border-color: var(--border-light) !important;
            color: var(--text-body) !important;
            box-shadow: var(--card-shadow) !important;
        }

        [data-theme="dark"] .dropdown-item {
            color: var(--text-body) !important;
        }

        [data-theme="dark"] .dropdown-item:hover,
        [data-theme="dark"] .dropdown-item:focus {
            background-color: var(--surface-hover) !important;
            color: var(--text-heading) !important;
        }

        [data-theme="dark"] .modal-content,
        [data-theme="dark"] .offcanvas {
            background-color: var(--surface-white) !important;
            border-color: var(--border-light) !important;
            color: var(--text-body) !important;
        }

        [data-theme="dark"] .modal-header,
        [data-theme="dark"] .modal-footer {
            border-color: var(--border-light) !important;
        }

        /* Buttons, Badges, Pagination & Code */
        [data-theme="dark"] .btn-light,
        [data-theme="dark"] .btn-outline-secondary,
        [data-theme="dark"] .btn-outline-dark {
            background-color: var(--surface-white) !important;
            border-color: var(--border-light) !important;
            color: var(--text-heading) !important;
        }

        [data-theme="dark"] .btn-light:hover,
        [data-theme="dark"] .btn-outline-secondary:hover,
        [data-theme="dark"] .btn-outline-dark:hover {
            background-color: var(--surface-hover) !important;
            border-color: var(--brand-emerald) !important;
            color: var(--brand-emerald-light) !important;
        }

        [data-theme="dark"] .badge.bg-light,
        [data-theme="dark"] .badge.bg-white,
        [data-theme="dark"] .badge.bg-success-subtle,
        [data-theme="dark"] .badge.bg-danger-subtle,
        [data-theme="dark"] .badge.bg-warning-subtle {
            background-color: var(--surface-hover) !important;
            color: var(--text-heading) !important;
            border: 1px solid var(--border-light) !important;
        }

        [data-theme="dark"] .pagination .page-link {
            background-color: var(--surface-white) !important;
            border-color: var(--border-light) !important;
            color: var(--text-body) !important;
        }

        [data-theme="dark"] .pagination .page-item.active .page-link {
            background-color: var(--brand-emerald) !important;
            border-color: var(--brand-emerald) !important;
            color: #ffffff !important;
        }

        [data-theme="dark"] code {
            background-color: rgba(16, 185, 129, 0.15) !important;
            color: var(--brand-emerald-light) !important;
        }

        /* Mobile Sidebar Overlay */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .search-trigger-btn {
                width: 180px;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

<div class="app-wrapper">
    <!-- Sidebar Navigation (Donezo Light Style) -->
    <aside class="sidebar" id="appSidebar">
        <div class="sidebar-brand">
            <div class="brand-logo-icon" style="background: linear-gradient(135deg, #10b981, #047857); color: #fff;">
                <i class="bi bi-bag-check-fill"></i>
            </div>
            <div>
                <div class="brand-title" style="letter-spacing: 0.5px; font-weight: 800;">BYZEE</div>
                <div class="brand-subtitle">Hijab & Inventory Engine</div>
            </div>
        </div>

        <div class="sidebar-menu">
            <div class="menu-label">Navigasi Utama</div>

            <a href="{{ route('dashboard') }}" class="nav-link-custom {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Dashboard</span>
            </a>

            @auth
                @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
                    <div class="menu-label mt-3">Data Master</div>

                    <a href="{{ route('items.index') }}" class="nav-link-custom {{ request()->is('items*') ? 'active' : '' }}">
                        <i class="bi bi-box"></i>
                        <span>Data Barang</span>
                    </a>
                    <a href="{{ route('categories.index') }}" class="nav-link-custom {{ request()->is('categories*') ? 'active' : '' }}">
                        <i class="bi bi-tags"></i>
                        <span>Kategori Barang</span>
                    </a>
                    <a href="{{ route('suppliers.index') }}" class="nav-link-custom {{ request()->is('suppliers*') ? 'active' : '' }}">
                        <i class="bi bi-truck"></i>
                        <span>Supplier</span>
                    </a>
                @endif

                @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
                    <div class="menu-label mt-3">Transaksi Stok</div>

                    <a href="{{ route('stock-movements.in.create') }}" class="nav-link-custom {{ request()->routeIs('stock-movements.in.create') ? 'active' : '' }}">
                        <i class="bi bi-arrow-down-left-circle text-success"></i>
                        <span>Barang Masuk</span>
                    </a>
                    <a href="{{ route('stock-movements.out.create') }}" class="nav-link-custom {{ request()->routeIs('stock-movements.out.create') ? 'active' : '' }}">
                        <i class="bi bi-arrow-up-right-circle text-danger"></i>
                        <span>Barang Keluar</span>
                    </a>
                    <a href="{{ route('stock-movements.index') }}" class="nav-link-custom {{ request()->routeIs('stock-movements.index') ? 'active' : '' }}">
                        <i class="bi bi-list-ul"></i>
                        <span>Semua Transaksi</span>
                    </a>
                @endif

                @if(Auth::user()->isAdmin() || Auth::user()->isOwner())
                    <div class="menu-label mt-3">Laporan & Finance</div>

                    <a href="{{ route('reports.stock') }}" class="nav-link-custom {{ request()->routeIs('reports.stock') ? 'active' : '' }}">
                        <i class="bi bi-card-checklist"></i>
                        <span>Laporan Stok & Asset</span>
                    </a>
                    <a href="{{ route('reports.transactions') }}" class="nav-link-custom {{ request()->routeIs('reports.transactions') ? 'active' : '' }}">
                        <i class="bi bi-cash-stack"></i>
                        <span>Cashflow & Histori</span>
                    </a>
                @endif

                @if(Auth::user()->isAdmin())
                    <div class="menu-label mt-3">Pengaturan System</div>

                    <a href="{{ route('users.index') }}" class="nav-link-custom {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <i class="bi bi-shield-lock"></i>
                        <span>Kelola User</span>
                    </a>
                @endif
            @endauth
        </div>

        @auth
            <div class="sidebar-user">
                <div class="user-card">
                    <div class="avatar-box">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-grow-1 overflow-hidden">
                        <div class="fw-semibold text-truncate text-dark font-size-14">{{ Auth::user()->name }}</div>
                        @if(Auth::user()->isAdmin())
                            <span class="role-pill role-admin">Admin</span>
                        @elseif(Auth::user()->isStaff())
                            <span class="role-pill role-staff">Staff</span>
                        @elseif(Auth::user()->isOwner())
                            <span class="role-pill role-owner">Owner</span>
                        @endif
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-link text-muted p-1" title="Logout">
                            <i class="bi bi-box-arrow-right fs-5"></i>
                        </button>
                    </form>
                </div>
            </div>
        @endauth
    </aside>

    <!-- Main Section -->
    <div class="main-content">
        <!-- Top Bar Header -->
        <header class="topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-link text-dark p-0 d-lg-none" type="button" onclick="document.getElementById('appSidebar').classList.toggle('show')">
                    <i class="bi bi-list fs-2"></i>
                </button>
                <div class="search-trigger-btn" onclick="document.getElementById('globalSearchInput')?.focus()">
                    <i class="bi bi-search"></i>
                    <span>Cari barang, SKU, ref...</span>
                    <span class="shortcut-chip">⌘K</span>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <button type="button" class="theme-toggle-btn" id="themeToggleBtn" title="Mode Terang / Gelap" aria-label="Toggle Theme">
                    <i class="bi bi-sun-fill sun-icon"></i>
                    <i class="bi bi-moon-stars-fill moon-icon"></i>
                </button>

                <div class="d-none d-md-flex align-items-center gap-2 bg-light px-3 py-2 rounded-3 border border-secondary border-opacity-10 text-muted font-mono" style="font-size: 0.82rem;">
                    <i class="bi bi-clock text-success"></i>
                    <span>{{ date('d M Y') }}</span>
                </div>

                @auth
                    <div class="dropdown">
                        <button class="btn btn-light btn-sm border d-flex align-items-center gap-2 px-3 py-1 text-dark" type="button" data-bs-toggle="dropdown" style="border-radius: 10px;">
                            <div class="avatar-box" style="width: 28px; height: 28px; font-size: 0.8rem;">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <span class="d-none d-md-inline fw-semibold">{{ Auth::user()->name }}</span>
                            <i class="bi bi-chevron-down small text-muted"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2">
                            <li class="px-3 py-2 border-bottom">
                                <div class="fw-bold text-dark">{{ Auth::user()->name }}</div>
                                <div class="small text-muted">{{ Auth::user()->email }}</div>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger d-flex align-items-center gap-2">
                                        <i class="bi bi-box-arrow-right"></i> Keluar
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @endauth
            </div>
        </header>

        <!-- Main Body View Container -->
        <main class="p-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 text-dark mb-4 d-flex align-items-center" style="background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.3) !important;" role="alert">
                    <i class="bi bi-check-circle-fill text-success fs-4 me-3"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 text-dark mb-4 d-flex align-items-center" style="background: rgba(220, 38, 38, 0.12); border: 1px solid rgba(220, 38, 38, 0.3) !important;" role="alert">
                    <i class="bi bi-exclamation-octagon-fill text-danger fs-4 me-3"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 text-dark mb-4" style="background: rgba(220, 38, 38, 0.12); border: 1px solid rgba(220, 38, 38, 0.3) !important;" role="alert">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-exclamation-triangle-fill text-danger fs-4 me-2"></i>
                        <strong class="fs-6">Terjadi kesalahan input:</strong>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                    </div>
                    <ul class="mb-0 ps-4 small text-muted">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Keyboard Shortcut (⌘K / Ctrl+K)
    document.addEventListener('keydown', function(e) {
        if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
            e.preventDefault();
            const searchField = document.getElementById('globalSearchInput');
            if (searchField) searchField.focus();
        }
    });

    // Light/Dark Theme Toggle Handler
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
@stack('scripts')
</body>
</html>
