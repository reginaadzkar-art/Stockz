@extends('layouts.app')

@section('title', 'Transaksi Stok - Jurnal Mutasi')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1 text-white">Jurnal Mutasi Transaksi Stok</h2>
        <p class="text-muted small mb-0">Pencatatan resmi arus barang masuk (pembelian/restok) dan barang keluar (penjualan/pemakaian).</p>
    </div>
    @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
        <div class="d-flex gap-2">
            <a href="{{ route('stock-movements.in.create') }}" class="btn btn-emerald fw-bold d-flex align-items-center gap-2 px-3 py-2" style="background: var(--brand-emerald); color: #000; border-radius: 10px;">
                <i class="bi bi-arrow-down-left-circle-fill fs-5"></i> Input Barang Masuk
            </a>
            <a href="{{ route('stock-movements.out.create') }}" class="btn btn-outline-danger fw-bold d-flex align-items-center gap-2 px-3 py-2" style="border-radius: 10px;">
                <i class="bi bi-arrow-up-right-circle-fill fs-5"></i> Input Barang Keluar
            </a>
        </div>
    @endif
</div>

<!-- Filter Card -->
<div class="glass-card mb-4 p-4">
    <form action="{{ route('stock-movements.index') }}" method="GET" class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label text-muted small fw-semibold">Cari Referensi / Notes</label>
            <input type="text" name="search" class="form-control form-control-custom" placeholder="No Ref, Penerima, Catatan..." value="{{ request('search') }}">
        </div>

        <div class="col-md-2">
            <label class="form-label text-muted small fw-semibold">Tipe Mutasi</label>
            <select name="type" class="form-select form-select-custom">
                <option value="">-- Semua Tipe --</option>
                <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Barang Masuk</option>
                <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Barang Keluar</option>
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label text-muted small fw-semibold">Dari Tanggal</label>
            <input type="date" name="date_from" class="form-control form-control-custom font-mono" value="{{ request('date_from') }}">
        </div>

        <div class="col-md-2">
            <label class="form-label text-muted small fw-semibold">Sampai Tanggal</label>
            <input type="date" name="date_to" class="form-control form-control-custom font-mono" value="{{ request('date_to') }}">
        </div>

        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-emerald w-100 font-weight-600" style="background: var(--brand-emerald); color: #000; border-radius: 10px;">
                <i class="bi bi-search me-1"></i> Filter
            </button>
            <a href="{{ route('stock-movements.index') }}" class="btn btn-outline-secondary" title="Reset" style="border-radius: 10px;">
                <i class="bi bi-arrow-counterclockwise"></i>
            </a>
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="glass-card mb-4">
    <div class="glass-card-header">
        <h6 class="fw-bold mb-0 text-white"><i class="bi bi-arrow-left-right text-emerald me-2"></i>Histori Mutasi Stok ({ $movements->total() } Transaksi)</h6>
        <span class="badge bg-dark border border-secondary text-muted font-mono">Real-Time Log</span>
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
                    @if(!Auth::user()->isStaff())
                        <th>Total Nilai Nominal</th>
                    @endif
                    <th>Petugas Input</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movements as $movement)
                    <tr>
                        <td class="ps-4">
                            <a href="{{ route('stock-movements.show', $movement) }}" class="font-mono text-emerald fw-bold text-decoration-none">
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
                        <td class="text-white">
                            @if($movement->type === 'in')
                                <span class="text-light"><i class="bi bi-truck me-1 text-muted"></i>{{ $movement->supplier->name ?? '-' }}</span>
                            @else
                                <span class="text-light"><i class="bi bi-person me-1 text-muted"></i>{{ $movement->recipient_or_destination ?? '-' }}</span>
                            @endif
                        </td>
                        <td class="fw-bold font-mono text-white">{{ number_format($movement->total_quantity) }} pcs</td>
                        @if(!Auth::user()->isStaff())
                            <td class="font-mono text-white fw-bold">Rp {{ number_format($movement->total_amount, 0, ',', '.') }}</td>
                        @endif
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-box" style="width: 26px; height: 26px; font-size: 0.75rem;">
                                    {{ strtoupper(substr($movement->user->name ?? 'A', 0, 1)) }}
                                </div>
                                <span class="text-muted small">{{ $movement->user->name ?? '-' }}</span>
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
                        <td colspan="{{ Auth::user()->isStaff() ? 7 : 8 }}" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Tidak ada data transaksi yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($movements->hasPages())
        <div class="p-3 border-top border-glass d-flex justify-content-center">
            {{ $movements->links() }}
        </div>
    @endif
</div>
@endsection
