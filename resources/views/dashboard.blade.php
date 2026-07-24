@extends('layouts.app')

@section('title', 'Dashboard Analytics')

@section('content')
<!-- Top Welcome & Role Bar -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2">
            <h2 class="fw-bold mb-0 text-dark">Dashboard Ringkasan</h2>
            @if(Auth::user()->isOwner())
                <span class="role-pill role-owner">Executive Owner View</span>
            @elseif(Auth::user()->isAdmin())
                <span class="role-pill role-admin">System Admin Command</span>
            @else
                <span class="role-pill role-staff">Staff Operator</span>
            @endif
        </div>
        <p class="text-muted small mb-0 mt-1">Selamat datang kembali, <strong>{{ Auth::user()->name }}</strong>. Berikut ringkasan stok & arus barang real-time hari ini.</p>
    </div>

    <div class="d-flex align-items-center gap-2">
        @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
            <a href="{{ route('stock-movements.in.create') }}" class="btn btn-emerald btn-sm px-3 fw-bold d-flex align-items-center gap-2" style="background: var(--brand-emerald); color: #fff; border-radius: 10px;">
                <i class="bi bi-plus-lg"></i> Barang Masuk
            </a>
            <a href="{{ route('stock-movements.out.create') }}" class="btn btn-outline-danger btn-sm px-3 fw-bold d-flex align-items-center gap-2" style="border-radius: 10px;">
                <i class="bi bi-dash-lg"></i> Barang Keluar
            </a>
        @endif
    </div>
</div>

<!-- 1. SEQUENCE STYLE HERO CASHFLOW & BALANCE BANNER (Foto 5) -->
<div class="glass-card mb-4 overflow-hidden position-relative" style="background: linear-gradient(135deg, #0f543f 0%, #064e3b 100%); border: 1px solid rgba(16, 185, 129, 0.3);">
    <div class="p-4 p-md-5">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge px-2 py-1 rounded-2 font-mono" style="background: rgba(255, 255, 255, 0.18); color: #ffffff; font-size: 0.75rem;">
                        <i class="bi bi-shield-check me-1"></i> ESTIMASI VALUASI INVENTORY
                    </span>
                    <span class="text-white opacity-75 small">• Real-time Sync</span>
                </div>
                <h1 class="display-5 fw-bold text-white font-mono mb-2">
                    Rp {{ number_format($totalValuationSelling, 0, ',', '.') }}
                </h1>
                <div class="d-flex flex-wrap align-items-center gap-3 text-white opacity-90 small">
                    <div>
                        <span class="opacity-75">Nilai Beli Modal:</span>
                        <strong class="font-mono text-white">Rp {{ number_format($totalValuationPurchase, 0, ',', '.') }}</strong>
                    </div>
                    <div class="border-start border-white border-opacity-25 pe-2 ps-2">
                        <span class="opacity-75">Margin Potensial:</span>
                        <strong class="font-mono" style="color: #6ee7b7;">
                            +Rp {{ number_format(max(0, $totalValuationSelling - $totalValuationPurchase), 0, ',', '.') }}
                        </strong>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="p-3 rounded-3" style="background: rgba(255, 255, 255, 0.12); border: 1px solid rgba(255, 255, 255, 0.2);">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="text-white opacity-85 small">Barang Masuk (Bln Ini)</span>
                                <i class="bi bi-arrow-down-left-circle fs-5" style="color: #6ee7b7;"></i>
                            </div>
                            <div class="fs-4 fw-bold font-mono text-white">+{{ number_format($totalInMonth) }} <span class="fs-6 font-sans opacity-75">pcs</span></div>
                            <div class="small font-mono mt-1 opacity-90 text-white">Rp {{ number_format($totalIncomeMonth, 0, ',', '.') }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-3" style="background: rgba(255, 255, 255, 0.12); border: 1px solid rgba(255, 255, 255, 0.2);">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="text-white opacity-85 small">Barang Keluar (Bln Ini)</span>
                                <i class="bi bi-arrow-up-right-circle fs-5" style="color: #fca5a5;"></i>
                            </div>
                            <div class="fs-4 fw-bold font-mono text-white">-{{ number_format($totalOutMonth) }} <span class="fs-6 font-sans opacity-75">pcs</span></div>
                            <div class="small font-mono mt-1 opacity-90 text-white">Rp {{ number_format($totalExpenseMonth, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 2. CREXTIO STYLE OWNER HUB & STAT CARDS (Foto 3 & Foto 1) -->
<div class="row g-4 mb-4">
    <!-- Owner / Executive Hub Widget (Foto 3 - Light Crextio style) -->
    <div class="col-xl-4 col-lg-5">
        <div class="glass-card h-100 p-4 d-flex flex-column justify-content-between position-relative overflow-hidden" style="background: linear-gradient(145deg, #ffffff, #fefefe); border: 1px solid #e2e8f0;">
            <div>
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-box" style="width: 48px; height: 48px; font-size: 1.2rem;">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-0">{{ Auth::user()->name }}</h5>
                            <span class="text-muted small">{{ ucfirst(Auth::user()->role) }} Control Panel</span>
                        </div>
                    </div>
                    <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1 small">Active</span>
                </div>

                <div class="my-4 p-3 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small fw-semibold">Indikator Kesehatan Stok</span>
                        <span class="fw-bold text-emerald font-mono">{{ $stockHealthPercentage }}%</span>
                    </div>
                    <div class="progress" style="height: 10px; background: #e2e8f0; border-radius: 20px;">
                        <div class="progress-bar" role="progressbar" style="width: {{ $stockHealthPercentage }}%; background: linear-gradient(90deg, #10b981, #059669); border-radius: 20px;"></div>
                    </div>
                    <div class="d-flex justify-content-between text-muted extra-small mt-2">
                        <span>Low Stock: {{ $lowStockCount }} items</span>
                        <span>Total: {{ number_format($totalItems) }} SKUs</span>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                @if(Auth::user()->isAdmin() || Auth::user()->isOwner())
                    <a href="{{ route('reports.stock') }}" class="btn btn-outline-dark btn-sm w-100 py-2 rounded-3 text-truncate" style="font-size: 0.82rem;">
                        <i class="bi bi-file-earmark-bar-graph me-1"></i> Laporan Stok
                    </a>
                    <a href="{{ route('reports.transactions') }}" class="btn btn-outline-success btn-sm w-100 py-2 rounded-3 text-truncate" style="font-size: 0.82rem;">
                        <i class="bi bi-cash-stack me-1"></i> Laporan Cashflow
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Donezo Stat Cards Grid (Foto 1 - Light) -->
    <div class="col-xl-8 col-lg-7">
        <div class="row g-3 h-100">
            <!-- Card 1: Total Jenis Barang -->
            <div class="col-sm-6">
                <div class="glass-card p-4 h-100 d-flex flex-column justify-content-between">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px;">Total Jenis Barang</span>
                            <h2 class="fw-bold text-dark font-mono mt-2 mb-0">{{ number_format($totalItems) }}</h2>
                        </div>
                        <div class="p-3 rounded-3" style="background: rgba(15, 84, 63, 0.1); color: var(--brand-emerald);">
                            <i class="bi bi-box-seam fs-3"></i>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-top d-flex justify-content-between align-items-center">
                        <span class="badge badge-emerald"><i class="bi bi-check-circle me-1"></i> Active Catalog</span>
                        <a href="{{ route('items.index') }}" class="text-muted small text-decoration-none hover-dark">Kelola <i class="bi bi-chevron-right"></i></a>
                    </div>
                </div>
            </div>

            <!-- Card 2: Low Stock Alert -->
            <div class="col-sm-6">
                <div class="glass-card p-4 h-100 d-flex flex-column justify-content-between" style="border-color: {{ $lowStockCount > 0 ? 'rgba(220, 38, 38, 0.4)' : '#e2e8f0' }};">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px;">Stok Menipis (Alert)</span>
                            <h2 class="fw-bold text-danger font-mono mt-2 mb-0">{{ number_format($lowStockCount) }}</h2>
                        </div>
                        <div class="p-3 rounded-3" style="background: rgba(220, 38, 38, 0.1); color: var(--coral-alert);">
                            <i class="bi bi-exclamation-triangle fs-3"></i>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-top d-flex justify-content-between align-items-center">
                        @if($lowStockCount > 0)
                            <span class="badge badge-coral"><i class="bi bi-bell-fill me-1"></i> Perlu Restock</span>
                        @else
                            <span class="badge badge-emerald"><i class="bi bi-shield-check me-1"></i> Stok Aman</span>
                        @endif
                        <a href="{{ route('reports.stock', ['low_stock' => 1]) }}" class="text-danger small text-decoration-none">Lihat Alert <i class="bi bi-chevron-right"></i></a>
                    </div>
                </div>
            </div>

            <!-- Card 3: Items In Month -->
            <div class="col-sm-6">
                <div class="glass-card p-4 h-100 d-flex flex-column justify-content-between">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px;">Total Masuk (Bulan Ini)</span>
                            <h2 class="fw-bold text-success font-mono mt-2 mb-0">+{{ number_format($totalInMonth) }}</h2>
                        </div>
                        <div class="p-3 rounded-3" style="background: rgba(16, 185, 129, 0.1); color: #059669;">
                            <i class="bi bi-arrow-down-left-circle fs-3"></i>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-top d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Nilai: <strong class="font-mono text-dark">Rp {{ number_format($totalIncomeMonth, 0, ',', '.') }}</strong></span>
                        @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
                            <a href="{{ route('stock-movements.in.create') }}" class="text-success small text-decoration-none">+ Input</a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Card 4: Items Out Month -->
            <div class="col-sm-6">
                <div class="glass-card p-4 h-100 d-flex flex-column justify-content-between">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px;">Total Keluar (Bulan Ini)</span>
                            <h2 class="fw-bold text-warning font-mono mt-2 mb-0">-{{ number_format($totalOutMonth) }}</h2>
                        </div>
                        <div class="p-3 rounded-3" style="background: rgba(217, 119, 6, 0.1); color: var(--amber-warn);">
                            <i class="bi bi-arrow-up-right-circle fs-3"></i>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-top d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Nilai: <strong class="font-mono text-dark">Rp {{ number_format($totalExpenseMonth, 0, ',', '.') }}</strong></span>
                        @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
                            <a href="{{ route('stock-movements.out.create') }}" class="text-warning small text-decoration-none">- Input</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 3. REAL-TIME STOCK RADAR CHART & LOW STOCK ALERT TABLE (Foto 4 & Foto 6) -->
<div class="row g-4 mb-4">
    <!-- Real-time Movement Trend Area Chart (Foto 4) -->
    <div class="col-lg-8">
        <div class="glass-card h-100">
            <div class="glass-card-header">
                <div>
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-activity text-success me-2"></i>Tren Pergerakan Stok (7 Hari Terakhir)</h6>
                    <span class="text-muted extra-small">Perbandingan Qty Barang Masuk (Emerald) vs Barang Keluar (Coral)</span>
                </div>
                <span class="badge bg-light text-dark border font-mono">Live Sync</span>
            </div>
            <div class="p-4">
                <div id="stockTrendChart" style="min-height: 280px;"></div>
            </div>
        </div>
    </div>

    <!-- Peringatan Stok Menipis Radar List (Foto 6 & Foto 4) -->
    <div class="col-lg-4">
        <div class="glass-card h-100 d-flex flex-column">
            <div class="glass-card-header" style="border-bottom-color: rgba(220, 38, 38, 0.15);">
                <h6 class="fw-bold mb-0 text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Peringatan Stok Menipis</h6>
                <a href="{{ route('reports.stock', ['low_stock' => 1]) }}" class="btn btn-outline-danger btn-sm rounded-pill" style="font-size: 0.75rem;">Lihat Semua</a>
            </div>
            <div class="p-0 flex-grow-1 overflow-auto">
                <div class="list-group list-group-flush">
                    @forelse($lowStockItems->take(5) as $item)
                        <div class="list-group-item bg-transparent text-dark border-bottom p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <div class="fw-semibold text-dark mb-1">{{ $item->name }}</div>
                                <div class="text-muted extra-small">SKU: <code class="font-mono text-dark fw-bold">{{ $item->sku }}</code> | Min: {{ $item->min_stock }} {{ $item->unit }}</div>
                            </div>
                            <span class="badge bg-danger-subtle text-danger font-mono fs-6 px-3 py-2 border border-danger border-opacity-25" style="border-radius: 10px;">
                                {{ $item->current_stock }} {{ $item->unit }}
                            </span>
                        </div>
                    @empty
                        <div class="p-4 text-center text-muted">
                            <i class="bi bi-shield-check text-success fs-1 d-block mb-2"></i>
                            <div class="fw-semibold text-dark">Stok Aman & Tercukupi</div>
                            <span class="small">Tidak ada barang di bawah batas minimum stok.</span>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 4. RECENT TRANSACTIONS TABLE (Foto 2 & Foto 6) -->
<div class="glass-card mb-4">
    <div class="glass-card-header">
        <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history text-success me-2"></i>Transaksi Stok Terbaru</h6>
        @if(Auth::user()->isAdmin() || Auth::user()->isOwner())
            <a href="{{ route('reports.transactions') }}" class="btn btn-outline-dark btn-sm rounded-pill" style="font-size: 0.78rem;">Lihat Histori Lengkap</a>
        @endif
    </div>
    <div class="table-responsive">
        <table class="table table-custom align-middle">
            <thead>
                <tr>
                    <th class="ps-4">No. Referensi</th>
                    <th>Tanggal</th>
                    <th>Tipe</th>
                    <th>Total Qty</th>
                    <th>Supplier / Recipient</th>
                    <th>Petugas Input</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentMovements as $movement)
                    <tr>
                        <td class="ps-4">
                            <a href="{{ route('stock-movements.show', $movement) }}" class="font-mono fw-bold text-success text-decoration-none">
                                {{ $movement->reference_number }}
                            </a>
                        </td>
                        <td class="font-mono text-muted">{{ $movement->date->format('d/m/Y') }}</td>
                        <td>
                            @if($movement->type === 'in')
                                <span class="badge badge-emerald"><i class="bi bi-arrow-down-left me-1"></i> Masuk</span>
                            @else
                                <span class="badge badge-coral"><i class="bi bi-arrow-up-right me-1"></i> Keluar</span>
                            @endif
                        </td>
                        <td class="fw-bold font-mono text-dark">{{ number_format($movement->total_quantity) }}</td>
                        <td class="text-muted">
                            {{ $movement->type === 'in' ? ($movement->supplier->name ?? '-') : ($movement->recipient_or_destination ?? '-') }}
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-box" style="width: 24px; height: 24px; font-size: 0.7rem;">
                                    {{ strtoupper(substr($movement->user->name ?? 'A', 0, 1)) }}
                                </div>
                                <span class="text-dark">{{ $movement->user->name ?? 'Admin' }}</span>
                            </div>
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('stock-movements.show', $movement) }}" class="btn btn-outline-secondary btn-sm rounded-circle" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                            Belum ada aktivitas transaksi stok.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const options = {
            series: [{
                name: 'Barang Masuk (Qty)',
                data: @json($trendIn)
            }, {
                name: 'Barang Keluar (Qty)',
                data: @json($trendOut)
            }],
            chart: {
                type: 'area',
                height: 280,
                toolbar: { show: false },
                background: 'transparent'
            },
            colors: ['#10b981', '#dc2626'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.3,
                    opacityTo: 0.05,
                    stops: [0, 90, 100]
                }
            },
            grid: {
                borderColor: '#e2e8f0',
                strokeDashArray: 4
            },
            xaxis: {
                categories: @json($trendDates),
                labels: { style: { colors: '#64748b', fontFamily: 'Plus Jakarta Sans' } },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: { style: { colors: '#64748b', fontFamily: 'JetBrains Mono' } }
            },
            tooltip: {
                theme: 'light',
                y: { formatter: function (val) { return val + " pcs" } }
            },
            legend: {
                labels: { colors: '#334155' },
                position: 'top',
                horizontalAlign: 'right'
            }
        };

        const chart = new ApexCharts(document.querySelector("#stockTrendChart"), options);
        chart.render();
    });
</script>
@endpush
