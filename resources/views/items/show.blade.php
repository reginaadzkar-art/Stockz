@extends('layouts.app')

@section('title', 'Detail Produk & Variasi - ' . $item->name)

@section('content')
<div class="mb-3 d-flex justify-content-between align-items-center">
    <a href="{{ route('items.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Katalog Barang
    </a>
    @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
        <a href="{{ route('items.edit', $item) }}" class="btn btn-primary btn-sm px-3">
            <i class="bi bi-pencil me-1"></i> Kelola & Edit Variasi
        </a>
    @endif
</div>

<div class="row g-4 mb-4">
    <!-- INFO MASTER BARANG -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-box-seam me-2"></i>Informasi Master Produk</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0 align-middle">
                    <tr>
                        <td class="text-muted" style="width: 130px;">SKU Utama</td>
                        <td class="fw-bold"><code>{{ $item->sku }}</code></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Nama Barang</td>
                        <td class="fw-bold text-dark fs-6">{{ $item->name }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Kategori</td>
                        <td><span class="badge bg-light text-dark border">{{ $item->category->name }}</span></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Satuan Unit</td>
                        <td><span class="badge bg-secondary">{{ $item->unit }}</span></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Total Stok Real-Time</td>
                        <td>
                            <span class="fs-4 fw-bold {{ $item->isLowStock() ? 'text-danger' : 'text-success' }}">
                                {{ number_format($item->current_stock) }} {{ $item->unit }}
                            </span>
                            @if($item->isLowStock())
                                <span class="badge bg-danger ms-2">Menipis</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Batas Stok Min.</td>
                        <td>{{ number_format($item->min_stock) }} {{ $item->unit }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Rentang Harga Jual</td>
                        <td class="fw-bold text-success">{{ $item->selling_price_formatted }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Deskripsi / Detail</td>
                        <td>{{ $item->description ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- RINCIAN VARIASI (WARNA & UKURAN) -->
    <div class="col-md-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-palette me-2"></i>Daftar Variasi (Warna & Ukuran)</h5>
                <span class="badge bg-primary rounded-pill">{{ $item->variants->count() }} Varian Tersedia</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark small">
                            <tr>
                                <th class="ps-3">SKU Varian</th>
                                <th>Warna</th>
                                <th>Ukuran</th>
                                <th>Harga Beli</th>
                                <th>Harga Jual</th>
                                <th>Stok Saat Ini</th>
                                <th>Status Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($item->variants as $variant)
                                <tr>
                                    <td class="ps-3"><code>{{ $variant->sku }}</code></td>
                                    <td>
                                        @if($variant->color)
                                            <span class="badge bg-info text-dark border"><i class="bi bi-circle-fill me-1 small"></i>{{ $variant->color }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($variant->size)
                                            <span class="badge bg-secondary">{{ $variant->size }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="font-mono text-muted">Rp {{ number_format($variant->purchase_price, 0, ',', '.') }}</td>
                                    <td class="font-mono text-dark fw-bold">Rp {{ number_format($variant->selling_price, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="fw-bold {{ $variant->isLowStock() ? 'text-danger' : 'text-success' }}">
                                            {{ number_format($variant->current_stock) }} {{ $item->unit }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($variant->isLowStock())
                                            <span class="badge bg-danger">Menipis</span>
                                        @else
                                            <span class="badge bg-success">Aman</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Belum ada data variasi spesifik.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- RIWAYAT MUTASI STOK -->
<div class="row">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history me-2"></i>Riwayat Mutasi Stok Transaksi</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Tanggal</th>
                                <th>No. Ref</th>
                                <th>Varian Barang</th>
                                <th>Tipe</th>
                                <th>Jumlah</th>
                                <th>Harga Satuan (Rp)</th>
                                <th>Total (Rp)</th>
                                <th>Petugas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($item->movementDetails as $detail)
                                <tr>
                                    <td class="ps-3">{{ $detail->stockMovement->date->format('d/m/Y') }}</td>
                                    <td><code>{{ $detail->stockMovement->reference_number }}</code></td>
                                    <td>
                                        @if($detail->variant)
                                            <span class="fw-semibold">{{ $detail->variant->variant_label }}</span>
                                            <small class="text-muted d-block">({{ $detail->variant->sku }})</small>
                                        @else
                                            <span class="text-muted">Standard Varian</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($detail->stockMovement->type === 'in')
                                            <span class="badge bg-success">Masuk</span>
                                        @else
                                            <span class="badge bg-danger">Keluar</span>
                                        @endif
                                    </td>
                                    <td class="fw-bold">
                                        {{ $detail->stockMovement->type === 'in' ? '+' : '-' }}{{ number_format($detail->quantity) }} {{ $item->unit }}
                                    </td>
                                    <td class="font-mono text-muted">Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                                    <td class="font-mono text-dark fw-bold">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                    <td>{{ $detail->stockMovement->user->name ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">Belum ada riwayat transaksi stok untuk produk ini.</td>
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
