@extends('layouts.app')

@section('title', 'Laporan Stok & Valuation Assets')

@section('content')
<!-- Header & Export Action Bar -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1 text-dark">Laporan Stok & Valuasi Aset</h2>
        <p class="text-muted small mb-0">Analisis ketersediaan stok produk & variasi (Warna & Ukuran), ambang minimum, dan total nilai aset modal.</p>
    </div>
    <div>
        <a href="{{ route('reports.stock.export', request()->query()) }}" class="btn btn-emerald fw-bold d-flex align-items-center gap-2 px-3 py-2" style="background: var(--brand-emerald); color: #fff; border-radius: 10px;">
            <i class="bi bi-file-earmark-arrow-down-fill fs-5"></i> Export Data CSV Per Varian
        </a>
    </div>
</div>

<!-- METRIC CARDS -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="glass-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.8px;">Valuasi Harga Beli (Modal)</span>
                <i class="bi bi-wallet2 text-success fs-4"></i>
            </div>
            <h3 class="fw-bold text-dark font-mono mb-1">Rp {{ number_format($totalValuationPurchase, 0, ',', '.') }}</h3>
            <span class="text-muted extra-small">Total nilai modal beli dari seluruh stok varian</span>
        </div>
    </div>
    <div class="col-md-4">
        <div class="glass-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.8px;">Valuasi Harga Jual (Omset)</span>
                <i class="bi bi-graph-up-arrow text-success fs-4"></i>
            </div>
            <h3 class="fw-bold text-success font-mono mb-1">Rp {{ number_format($totalValuationSelling, 0, ',', '.') }}</h3>
            <span class="text-muted extra-small">Potensi total pendapatan hasil penjualan stok varian</span>
        </div>
    </div>
    <div class="col-md-4">
        <div class="glass-card p-4 h-100" style="border-color: {{ $lowStockCount > 0 ? 'rgba(220, 38, 38, 0.4)' : 'var(--border-light)' }};">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.8px;">Produk / Varian Menipis (Alert)</span>
                <i class="bi bi-exclamation-octagon text-danger fs-4"></i>
            </div>
            <h3 class="fw-bold text-danger font-mono mb-1">{{ number_format($lowStockCount) }} <span class="fs-6 text-muted font-sans">Produk</span></h3>
            <span class="text-muted extra-small">Produk dengan varian berkategori stok menipis.</span>
        </div>
    </div>
</div>

<!-- Filter Panel -->
<div class="glass-card mb-4 p-4">
    <form action="{{ route('reports.stock') }}" method="GET" class="row g-3 align-items-end">
        <div class="col-md-5">
            <label class="form-label text-muted small fw-semibold">Filter Kategori Barang</label>
            <select name="category_id" class="form-select form-select-custom">
                <option value="">-- Semua Kategori Barang --</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <div class="form-check form-switch pt-2">
                <input class="form-check-input" type="checkbox" role="switch" name="low_stock" value="1" id="lowStockSwitch" {{ request('low_stock') ? 'checked' : '' }}>
                <label class="form-check-label text-danger fw-semibold small ms-2" for="lowStockSwitch">
                    <i class="bi bi-filter-square me-1"></i>Hanya Tampilkan Stok Menipis (Alert)
                </label>
            </div>
        </div>

        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-emerald w-100 font-weight-600" style="background: var(--brand-emerald); color: #fff; border-radius: 10px;">
                <i class="bi bi-funnel me-1"></i> Apply Filter
            </button>
            <a href="{{ route('reports.stock') }}" class="btn btn-outline-secondary" title="Reset Filter" style="border-radius: 10px;">
                <i class="bi bi-arrow-counterclockwise"></i>
            </a>
        </div>
    </form>
</div>

<!-- Asset Table -->
<div class="glass-card mb-4">
    <div class="glass-card-header">
        <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-boxes text-success me-2"></i>Daftar Ketersediaan Stok Real-Time Per Produk & Varian</h6>
        <span class="badge bg-light text-dark border font-mono">{{ $items->total() }} Products</span>
    </div>

    <div class="table-responsive">
        <table class="table table-custom align-middle">
            <thead>
                <tr>
                    <th class="ps-4">No</th>
                    <th>SKU Utama</th>
                    <th>Nama Barang & Rincian Variasi (Warna / Ukuran)</th>
                    <th>Kategori</th>
                    <th>Total Stok</th>
                    <th>Harga Jual</th>
                    <th class="text-end pe-4">Status Stok</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td class="ps-4 text-muted font-mono">{{ $loop->iteration + ($items->currentPage() - 1) * $items->perPage() }}</td>
                        <td>
                            <code class="font-mono text-success fw-bold px-2 py-1 rounded" style="background: rgba(16, 185, 129, 0.1);">
                                {{ $item->sku }}
                            </code>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $item->name }}</div>
                            @if($item->variants->count() > 0)
                                <div class="mt-1 d-flex flex-wrap gap-1">
                                    @foreach($item->variants as $variant)
                                        <span class="badge bg-light text-dark border extra-small">
                                            {{ $variant->variant_label }}: <strong>{{ $variant->current_stock }}</strong> {{ $item->unit }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border px-2 py-1">
                                {{ $item->category->name }}
                            </span>
                        </td>
                        <td>
                            <span class="fw-bold font-mono fs-6 {{ $item->isLowStock() ? 'text-danger' : 'text-success' }}">
                                {{ number_format($item->current_stock) }} {{ $item->unit }}
                            </span>
                        </td>
                        <td class="font-mono text-dark fw-semibold">
                            {{ $item->selling_price_formatted }}
                        </td>
                        <td class="text-end pe-4">
                            @if($item->isLowStock())
                                <span class="badge badge-coral"><i class="bi bi-exclamation-triangle me-1"></i> Menipis</span>
                            @else
                                <span class="badge badge-emerald"><i class="bi bi-shield-check me-1"></i> Aman</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-search fs-1 d-block mb-2"></i>
                            Tidak ada data barang yang sesuai kriteria filter.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($items->hasPages())
        <div class="p-3 border-top d-flex justify-content-center">
            {{ $items->links() }}
        </div>
    @endif
</div>
@endsection
