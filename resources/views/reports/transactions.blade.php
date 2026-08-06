@extends('layouts.app')

@section('title', 'Laporan Cashflow & Histori Transaksi')

@section('content')
<!-- Header & Export Action Bar -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1 text-dark">Laporan Cashflow & Histori Transaksi</h2>
        <p class="text-muted small mb-0">Audit mutasi arus barang masuk (Pembelian/Penerimaan) & barang keluar (Penjualan/Pengeluaran) (Sequence Financial Hub).</p>
    </div>
    <div>
        <a href="{{ route('reports.transactions.export', request()->query()) }}" class="btn btn-emerald fw-bold d-flex align-items-center gap-2 px-3 py-2" style="background: var(--brand-emerald); color: #fff; border-radius: 10px;">
            <i class="bi bi-file-earmark-arrow-down-fill fs-5"></i> Export Data CSV
        </a>
    </div>
</div>

<!-- SEQUENCE CASHFLOW METRIC CARDS (Foto 5 Light) -->
<div class="row g-3 mb-4">
    <!-- Inflow Card -->
    <div class="col-md-4">
        <div class="glass-card p-4 h-100 position-relative overflow-hidden" style="background: var(--surface-white); border-color: rgba(16, 185, 129, 0.3);">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.8px;">Total Inflow (Barang Masuk)</span>
                <div class="p-2 rounded-circle" style="background: rgba(16, 185, 129, 0.15); color: #059669;">
                    <i class="bi bi-arrow-down-left fs-4"></i>
                </div>
            </div>
            <h3 class="fw-bold text-success font-mono mb-1">Rp {{ number_format($totalIncome, 0, ',', '.') }}</h3>
            <span class="text-muted extra-small font-mono">+{{ number_format($totalInQty) }} pcs total volume masuk</span>
        </div>
    </div>

    <!-- Outflow Card -->
    <div class="col-md-4">
        <div class="glass-card p-4 h-100 position-relative overflow-hidden" style="background: var(--surface-white); border-color: rgba(220, 38, 38, 0.3);">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.8px;">Total Outflow (Barang Keluar)</span>
                <div class="p-2 rounded-circle" style="background: rgba(220, 38, 38, 0.15); color: #dc2626;">
                    <i class="bi bi-arrow-up-right fs-4"></i>
                </div>
            </div>
            <h3 class="fw-bold text-danger font-mono mb-1">Rp {{ number_format($totalExpense, 0, ',', '.') }}</h3>
            <span class="text-muted extra-small font-mono">-{{ number_format($totalOutQty) }} pcs total volume keluar</span>
        </div>
    </div>

    <!-- Net Flow Differential -->
    <div class="col-md-4">
        <div class="glass-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.8px;">Net Flow Value Margin</span>
                <i class="bi bi-arrow-left-right text-dark fs-4"></i>
            </div>
            @php $netValue = $totalIncome - $totalExpense; @endphp
            <h3 class="fw-bold font-mono mb-1 {{ $netValue >= 0 ? 'text-success' : 'text-danger' }}">
                {{ $netValue >= 0 ? '+' : '' }}Rp {{ number_format($netValue, 0, ',', '.') }}
            </h3>
            <span class="text-muted extra-small">Selisih mutasi nilai nominal stok terhitung</span>
        </div>
    </div>
</div>

<!-- Filter Panel (Foto 5 Light) -->
<div class="glass-card mb-4 p-4">
    <form action="{{ route('reports.transactions') }}" method="GET" class="row g-3 align-items-end">
        <div class="col-md-3">
            <label for="type" class="form-label text-muted small fw-semibold">Tipe Transaksi</label>
            <select name="type" id="type" class="form-select form-select-custom">
                <option value="">-- Semua Mutasi --</option>
                <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Barang Masuk (Inflow)</option>
                <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Barang Keluar (Outflow)</option>
            </select>
        </div>

        <div class="col-md-3">
            <label for="date_from" class="form-label text-muted small fw-semibold">Dari Tanggal</label>
            <input type="date" name="date_from" id="date_from" class="form-control form-control-custom font-mono" value="{{ request('date_from') }}">
        </div>

        <div class="col-md-3">
            <label for="date_to" class="form-label text-muted small fw-semibold">Sampai Tanggal</label>
            <input type="date" name="date_to" id="date_to" class="form-control form-control-custom font-mono" value="{{ request('date_to') }}">
        </div>

        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-emerald w-100 font-weight-600" style="background: var(--brand-emerald); color: #fff; border-radius: 10px;">
                <i class="bi bi-funnel me-1"></i> Apply Filter
            </button>
            <a href="{{ route('reports.transactions') }}" class="btn btn-outline-secondary" title="Reset Filter" style="border-radius: 10px;">
                <i class="bi bi-arrow-counterclockwise"></i>
            </a>
        </div>
    </form>
</div>

<!-- Transaction History Table -->
<div class="glass-card mb-4">
    <div class="glass-card-header">
        <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-list-stars text-success me-2"></i>Log Jurnal Transaksi Stok</h6>
        <span class="badge bg-light text-dark border font-mono">{{ $movements->total() }} Record(s)</span>
    </div>

    <div class="table-responsive">
        <table class="table table-custom align-middle">
            <thead>
                <tr>
                    <th class="ps-4">No. Referensi</th>
                    <th>Tanggal</th>
                    <th>Tipe</th>
                    <th>Supplier / Recipient</th>
                    <th>Total Qty</th>
                    <th>Total Nilai Nominal</th>
                    <th>Petugas Input</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movements as $movement)
                    <tr>
                        <td class="ps-4">
                            <a href="{{ route('stock-movements.show', $movement) }}" class="font-mono text-success fw-bold text-decoration-none">
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
                        <td class="text-dark">
                            @if($movement->type === 'in')
                                <span><i class="bi bi-truck me-1 text-muted"></i>{{ $movement->supplier->name ?? '-' }}</span>
                            @else
                                <span><i class="bi bi-person me-1 text-muted"></i>{{ $movement->recipient_or_destination ?? '-' }}</span>
                            @endif
                        </td>
                        <td class="fw-bold font-mono text-dark">{{ number_format($movement->total_quantity) }} pcs</td>
                        <td class="font-mono text-dark fw-bold">Rp {{ number_format($movement->total_amount, 0, ',', '.') }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-box" style="width: 26px; height: 26px; font-size: 0.75rem;">
                                    {{ strtoupper(substr($movement->user->name ?? 'A', 0, 1)) }}
                                </div>
                                <span class="text-muted small">{{ $movement->user->name ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('stock-movements.show', $movement) }}" class="btn btn-outline-secondary btn-sm rounded-circle" title="View Transaction Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-journal-x fs-1 d-block mb-2"></i>
                            Tidak ada riwayat transaksi stok yang sesuai kriteria filter.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($movements->hasPages())
        <div class="p-3 border-top d-flex justify-content-center">
            {{ $movements->links() }}
        </div>
    @endif
</div>
@endsection
