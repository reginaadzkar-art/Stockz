@extends('layouts.app')

@section('title', 'Data Barang Inventaris')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1 text-dark">Data Barang Inventaris</h2>
        <p class="text-muted small mb-0">Manajemen katalog barang, penetapan SKU, stok minimum, dan audit harga.</p>
    </div>
    @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
        <a href="{{ route('items.create') }}" class="btn btn-emerald fw-bold d-flex align-items-center gap-2 px-3 py-2" style="background: var(--brand-emerald); color: #fff; border-radius: 10px;">
            <i class="bi bi-plus-circle-fill fs-5"></i> Tambah Barang Baru
        </a>
    @endif
</div>

<!-- Filter Panel -->
<div class="glass-card mb-4 p-4">
    <form action="{{ route('items.index') }}" method="GET" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label text-muted small fw-semibold">Pencarian Barang</label>
            <input type="text" name="search" id="globalSearchInput" class="form-control form-control-custom" placeholder="Cari Kode SKU atau Nama Barang..." value="{{ request('search') }}">
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
        <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-box-seam text-emerald me-2"></i>Katalog Barang</h6>
        <span class="badge bg-light border text-muted font-mono">Live Inventory</span>
    </div>

    <div class="table-responsive">
        <table class="table table-custom align-middle">
            <thead>
                <tr>
                    <th class="ps-4">SKU / Kode</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Stok Real-Time</th>
                    <th>Min. Stok</th>
                    <th>Harga Beli</th>
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
                        <td class="fw-semibold text-dark">{{ $item->name }}</td>
                        <td>
                            <span class="badge bg-light border text-dark px-2 py-1">
                                {{ $item->category->name }}
                            </span>
                        </td>
                        <td>
                            <span class="fw-bold font-mono fs-6 {{ $item->isLowStock() ? 'text-coral' : 'text-emerald' }}">
                                {{ number_format($item->current_stock) }} {{ $item->unit }}
                            </span>
                            @if($item->isLowStock())
                                <span class="badge badge-coral ms-2" title="Stok di bawah ambang batas minimum!">Menipis</span>
                            @endif
                        </td>
                        <td class="font-mono text-muted">{{ number_format($item->min_stock) }} {{ $item->unit }}</td>
                        <td class="font-mono text-muted">Rp {{ number_format($item->purchase_price, 0, ',', '.') }}</td>
                        <td class="font-mono text-dark fw-semibold">Rp {{ number_format($item->selling_price, 0, ',', '.') }}</td>
                        <td class="text-end pe-4">
                            <div class="btn-group">
                                <a href="{{ route('items.show', $item) }}" class="btn btn-outline-info btn-sm rounded-circle me-1" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
                                    <a href="{{ route('items.edit', $item) }}" class="btn btn-outline-secondary btn-sm rounded-circle me-1" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('items.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus barang ini?')">
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
                        <td colspan="8" class="text-center py-5 text-muted">
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
