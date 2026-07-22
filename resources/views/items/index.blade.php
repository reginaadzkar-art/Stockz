@extends('layouts.app')

@section('title', 'Data Barang')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0">Data Barang</h3>
        <p class="text-muted small">Kelola inventaris dan pemantauan stok real-time</p>
    </div>
    @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
        <a href="{{ route('items.create') }}" class="btn btn-primary">
            <i class="bi bi-box-seam me-1"></i> Tambah Barang
        </a>
    @endif
</div>

<!-- Filter & Search Card -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('items.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Cari Kode SKU atau Nama Barang..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="category_id" class="form-select">
                    <option value="">-- Semua Kategori --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-center">
                <div class="form-check">
                    <input type="checkbox" name="low_stock" value="1" class="form-check-input" id="lowStockCheck" {{ request('low_stock') ? 'checked' : '' }}>
                    <label class="form-check-label text-danger fw-semibold small" for="lowStockCheck">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>Hanya Stok Menipis
                    </label>
                </div>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-dark w-100"><i class="bi bi-search me-1"></i>Filter</button>
                <a href="{{ route('items.index') }}" class="btn btn-outline-secondary" title="Reset"><i class="bi bi-arrow-counterclockwise"></i></a>
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
                        <th class="ps-3">SKU / Kode</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Stok Real-Time</th>
                        <th>Min. Stok</th>
                        <th>Harga Beli</th>
                        <th>Harga Jual</th>
                        <th class="text-end pe-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td class="ps-3"><code>{{ $item->sku }}</code></td>
                            <td class="fw-semibold">{{ $item->name }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $item->category->name }}</span></td>
                            <td>
                                <span class="fw-bold {{ $item->isLowStock() ? 'text-danger' : 'text-success' }}">
                                    {{ number_format($item->current_stock) }} {{ $item->unit }}
                                </span>
                                @if($item->isLowStock())
                                    <span class="badge bg-danger ms-1" title="Stok berada di bawah batas minimum!">Menipis</span>
                                @endif
                            </td>
                            <td>{{ number_format($item->min_stock) }} {{ $item->unit }}</td>
                            <td>Rp {{ number_format($item->purchase_price, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($item->selling_price, 0, ',', '.') }}</td>
                            <td class="text-end pe-3">
                                <a href="{{ route('items.show', $item) }}" class="btn btn-sm btn-outline-info me-1" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
                                    <a href="{{ route('items.edit', $item) }}" class="btn btn-sm btn-outline-secondary me-1" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('items.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus barang ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Tidak ada data barang yang ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($items->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $items->links() }}
        </div>
    @endif
</div>
@endsection
