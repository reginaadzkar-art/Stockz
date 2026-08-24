@extends('layouts.app')

@section('title', 'Data Barang Inventaris')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1 text-dark">Data Barang & Variasi (Hijab & Fashion)</h2>
        <p class="text-muted small mb-0">Manajemen katalog barang, variasi Warna & Ukuran, SKU varian, stok real-time, dan penetapan harga.</p>
    </div>
    @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
        <a href="{{ route('items.create') }}" class="btn btn-emerald fw-bold d-flex align-items-center gap-2 px-3 py-2" style="background: var(--brand-emerald); color: #fff; border-radius: 10px;">
            <i class="bi bi-plus-circle-fill fs-5"></i> Tambah Produk Baru
        </a>
    @endif
</div>

<!-- Filter Panel -->
<div class="glass-card mb-4 p-4">
    <form action="{{ route('items.index') }}" method="GET" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label text-muted small fw-semibold">Pencarian Barang / Varian</label>
            <input type="text" name="search" id="globalSearchInput" class="form-control form-control-custom" placeholder="Cari SKU, Nama Barang, Warna, atau Ukuran..." value="{{ request('search') }}">
        </div>

        <div class="col-md-3">
            <label class="form-label text-muted small fw-semibold">Filter Kategori</label>
            <select name="category_id" class="form-select form-select-custom">
                <option value="">-- Semua Kategori --</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <div class="form-check form-switch pt-2">
                <input class="form-check-input" type="checkbox" role="switch" name="low_stock" value="1" id="lowStockSwitch" {{ request('low_stock') ? 'checked' : '' }}>
                <label class="form-check-label text-coral fw-semibold small ms-2" for="lowStockSwitch">
                    <i class="bi bi-exclamation-triangle me-1"></i>Hanya Stok Menipis
                </label>
            </div>
        </div>

        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-emerald w-100 font-weight-600" style="background: var(--brand-emerald); color: #000; border-radius: 10px;">
                <i class="bi bi-search me-1"></i> Filter
            </button>
            <a href="{{ route('items.index') }}" class="btn btn-outline-secondary" title="Reset" style="border-radius: 10px;">
                <i class="bi bi-arrow-counterclockwise"></i>
            </a>
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="glass-card mb-4">
    <div class="glass-card-header">
        <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-box-seam text-emerald me-2"></i>Katalog Produk & Variasi</h6>
        <span class="badge bg-light border text-muted font-mono">Live Inventory</span>
    </div>

    <div class="table-responsive">
        <table class="table table-custom align-middle">
            <thead>
                <tr>
                    <th class="ps-4">SKU Utama</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Variasi (Warna & Ukuran)</th>
                    <th>Total Stok</th>
                    <th>Harga Jual</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td class="ps-4">
                            <code class="font-mono text-emerald fw-bold px-2 py-1 rounded" style="background: rgba(0, 194, 136, 0.1);">
                                {{ $item->sku }}
                            </code>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $item->name }}</div>
                            @if($item->description)
                                <small class="text-muted text-truncate d-block" style="max-width: 250px;">{{ $item->description }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light border text-dark px-2 py-1">
                                {{ $item->category->name }}
                            </span>
                        </td>
                        <td>
                            @if($item->variants->count() > 0)
                                <div class="d-flex flex-wrap gap-1 align-items-center">
                                    <span class="badge bg-primary rounded-pill px-2 py-1 me-1">
                                        <i class="bi bi-layers me-1"></i>{{ $item->variants->count() }} Varian
                                    </span>
                                    @foreach($item->variants->take(3) as $v)
                                        <span class="badge bg-light text-dark border small">
                                            {{ $v->variant_label }}
                                        </span>
                                    @endforeach
                                    @if($item->variants->count() > 3)
                                        <small class="text-muted ms-1">+{{ $item->variants->count() - 3 }} lainnya</small>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted small">Standard Varian</span>
                            @endif
                        </td>
                        <td>
                            <span class="fw-bold font-mono fs-6 {{ $item->isLowStock() ? 'text-coral' : 'text-emerald' }}">
                                {{ number_format($item->current_stock) }} {{ $item->unit }}
                            </span>
                            @if($item->isLowStock())
                                <span class="badge badge-coral ms-2" title="Ada varian dengan stok menipis!">Menipis</span>
                            @endif
                        </td>
                        <td class="font-mono text-dark fw-semibold">
                            {{ $item->selling_price_formatted }}
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group">
                                <a href="{{ route('items.show', $item) }}" class="btn btn-outline-info btn-sm rounded-circle me-1" title="Detail & Variasi">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
                                    <a href="{{ route('items.edit', $item) }}" class="btn btn-outline-secondary btn-sm rounded-circle me-1" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('items.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus produk ini beserta variansinya?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-circle" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Tidak ada data barang yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($items->hasPages())
        <div class="p-3 border-top border-glass d-flex justify-content-center">
            {{ $items->links() }}
        </div>
    @endif
</div>
@endsection
