@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0">Dashboard Ringkasan Stok</h3>
        <p class="text-muted small">Selamat datang kembali, <strong>{{ Auth::user()->name }}</strong> ({{ ucfirst(Auth::user()->role) }})</p>
    </div>
    <div>
        <span class="badge bg-dark py-2 px-3"><i class="bi bi-clock me-1"></i> {{ date('d F Y') }}</span>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card card-stat bg-white p-3 border-start border-4 border-primary">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold">Total Jenis Barang</div>
                    <div class="fs-3 fw-bold text-dark mt-1">{{ number_format($totalItems) }}</div>
                </div>
                <div class="bg-primary-subtle text-primary p-3 rounded-3">
                    <i class="bi bi-box-seam fs-2"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stat bg-white p-3 border-start border-4 border-danger">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold">Stok Menipis (Alert)</div>
                    <div class="fs-3 fw-bold text-danger mt-1">{{ number_format($lowStockCount) }}</div>
                </div>
                <div class="bg-danger-subtle text-danger p-3 rounded-3">
                    <i class="bi bi-exclamation-triangle fs-2"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stat bg-white p-3 border-start border-4 border-success">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold">Barang Masuk (Bulan Ini)</div>
                    <div class="fs-3 fw-bold text-success mt-1">+{{ number_format($totalInMonth) }}</div>
                </div>
                <div class="bg-success-subtle text-success p-3 rounded-3">
                    <i class="bi bi-arrow-down-left-circle fs-2"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stat bg-white p-3 border-start border-4 border-warning">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold">Barang Keluar (Bulan Ini)</div>
                    <div class="fs-3 fw-bold text-warning mt-1">-{{ number_format($totalOutMonth) }}</div>
                </div>
                <div class="bg-warning-subtle text-warning p-3 rounded-3">
                    <i class="bi bi-arrow-up-right-circle fs-2"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Low Stock Alert Box -->
    <div class="col-md-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Peringatan Stok Menipis</h6>
                <a href="{{ route('items.index', ['low_stock' => 1]) }}" class="btn btn-outline-danger btn-sm">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($lowStockItems->take(5) as $item)
                        <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <div>
                                <div class="fw-semibold text-dark">{{ $item->name }}</div>
                                <div class="text-muted small">SKU: <code>{{ $item->sku }}</code> | Min: {{ $item->min_stock }} {{ $item->unit }}</div>
                            </div>
                            <span class="badge bg-danger fs-6">{{ $item->current_stock }} {{ $item->unit }}</span>
                        </li>
                    @empty
                        <li class="list-group-item text-center py-4 text-muted">
                            <i class="bi bi-check-circle text-success me-1"></i> Semua stok barang aman dan tercukupi.
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="col-md-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0"><i class="bi bi-clock-history me-2"></i>Transaksi Stok Terbaru</h6>
                @if(Auth::user()->isAdmin() || Auth::user()->isOwner())
                    <a href="{{ route('reports.transactions') }}" class="btn btn-outline-primary btn-sm">Lihat Histori</a>
                @endif
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">No. Ref</th>
                                <th>Tanggal</th>
                                <th>Tipe</th>
                                <th>Total Qty</th>
                                <th>Petugas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentMovements as $movement)
                                <tr>
                                    <td class="ps-3"><a href="{{ route('stock-movements.show', $movement) }}" class="fw-bold text-decoration-none"><code>{{ $movement->reference_number }}</code></a></td>
                                    <td>{{ $movement->date->format('d/m/Y') }}</td>
                                    <td>
                                        @if($movement->type === 'in')
                                            <span class="badge bg-success">Masuk</span>
                                        @else
                                            <span class="badge bg-danger">Keluar</span>
                                        @endif
                                    </td>
                                    <td class="fw-bold">{{ number_format($movement->total_quantity) }}</td>
                                    <td>{{ $movement->user->name }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Belum ada aktivitas transaksi stok.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
