@extends('layouts.app')

@section('title', 'Detail Barang - ' . $item->name)

@section('content')
<div class="mb-3">
    <a href="{{ route('items.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Data Barang
    </a>
</div>

<div class="row g-4">
    <div class="col-md-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-box-seam me-2"></i>Informasi Barang</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="text-muted" style="width: 140px;">SKU / Kode</td>
                        <td class="fw-bold"><code>{{ $item->sku }}</code></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Nama Barang</td>
                        <td class="fw-bold">{{ $item->name }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Kategori</td>
                        <td><span class="badge bg-light text-dark border">{{ $item->category->name }}</span></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Stok Real-Time</td>
                        <td>
                            <span class="fs-5 fw-bold {{ $item->isLowStock() ? 'text-danger' : 'text-success' }}">
                                {{ number_format($item->current_stock) }} {{ $item->unit }}
                            </span>
                            @if($item->isLowStock())
                                <span class="badge bg-danger ms-2">Menipis</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Stok Min. Alert</td>
                        <td>{{ number_format($item->min_stock) }} {{ $item->unit }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Harga Beli</td>
                        <td>Rp {{ number_format($item->purchase_price, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Harga Jual</td>
                        <td>Rp {{ number_format($item->selling_price, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Deskripsi</td>
                        <td>{{ $item->description ?? '-' }}</td>
                    </tr>
                </table>
            </div>
            @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
                <div class="card-footer bg-white py-3 text-end">
                    <a href="{{ route('items.edit', $item) }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-pencil me-1"></i> Edit Barang
                    </a>
                </div>
            @endif
        </div>
    </div>

    <div class="col-md-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-clock-history me-2"></i>Riwayat Mutasi Stok</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Tanggal</th>
                                <th>No. Ref</th>
                                <th>Tipe</th>
                                <th>Jumlah</th>
                                <th>Petugas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($item->movementDetails as $detail)
                                <tr>
                                    <td class="ps-3">{{ $detail->stockMovement->date->format('d/m/Y') }}</td>
                                    <td><code>{{ $detail->stockMovement->reference_number }}</code></td>
                                    <td>
                                        @if($detail->stockMovement->type === 'in')
                                            <span class="badge bg-success">Masuk</span>
                                        @else
                                            <span class="badge bg-danger">Keluar</span>
                                        @endif
                                    </td>
                                    <td class="fw-bold">
                                        {{ $detail->stockMovement->type === 'in' ? '+' : '-' }}{{ $detail->quantity }} {{ $item->unit }}
                                    </td>
                                    <td>{{ $detail->stockMovement->user->name }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Belum ada riwayat transaksi stok untuk barang ini.</td>
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
