@extends('layouts.app')

@section('title', 'Transaksi Stok')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0">Riwayat Transaksi Stok</h3>
        <p class="text-muted small">Pencatatan barang masuk (pembelian/restok) dan barang keluar (penjualan/pemakaian)</p>
    </div>
    @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
        <div class="d-flex gap-2">
            <a href="{{ route('stock-movements.in.create') }}" class="btn btn-success">
                <i class="bi bi-arrow-down-left-circle me-1"></i> Input Barang Masuk
            </a>
            <a href="{{ route('stock-movements.out.create') }}" class="btn btn-danger">
                <i class="bi bi-arrow-up-right-circle me-1"></i> Input Barang Keluar
            </a>
        </div>
    @endif
</div>

<!-- Filter Card -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('stock-movements.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Cari No Ref, Penerima, Catatan..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="type" class="form-select">
                    <option value="">-- Semua Tipe --</option>
                    <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Barang Masuk</option>
                    <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Barang Keluar</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="date_from" class="form-control" title="Dari Tanggal" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <input type="date" name="date_to" class="form-control" title="Sampai Tanggal" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-dark w-100"><i class="bi bi-search me-1"></i>Filter</button>
                <a href="{{ route('stock-movements.index') }}" class="btn btn-outline-secondary" title="Reset"><i class="bi bi-arrow-counterclockwise"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">No. Referensi</th>
                        <th>Tanggal</th>
                        <th>Tipe</th>
                        <th>Supplier / Tujuan</th>
                        <th>Total Qty</th>
                        <th>Total Nilai</th>
                        <th>Petugas</th>
                        <th class="text-end pe-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $movement)
                        <tr>
                            <td class="ps-3"><code>{{ $movement->reference_number }}</code></td>
                            <td>{{ $movement->date->format('d/m/Y') }}</td>
                            <td>
                                @if($movement->type === 'in')
                                    <span class="badge bg-success"><i class="bi bi-arrow-down-left me-1"></i>Masuk</span>
                                @else
                                    <span class="badge bg-danger"><i class="bi bi-arrow-up-right me-1"></i>Keluar</span>
                                @endif
                            </td>
                            <td>
                                @if($movement->type === 'in')
                                    {{ $movement->supplier->name ?? 'Tanpa Supplier' }}
                                @else
                                    {{ $movement->recipient_or_destination ?? '-' }}
                                @endif
                            </td>
                            <td class="fw-bold">{{ number_format($movement->total_quantity) }}</td>
                            <td>Rp {{ number_format($movement->total_amount, 0, ',', '.') }}</td>
                            <td>{{ $movement->user->name }}</td>
                            <td class="text-end pe-3">
                                <a href="{{ route('stock-movements.show', $movement) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye me-1"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Tidak ada riwayat transaksi stok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($movements->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $movements->links() }}
        </div>
    @endif
</div>
@endsection
