@extends('layouts.app')

@section('title', 'Laporan Stok Barang')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0">Laporan Stok Barang</h3>
        <p class="text-muted small">Ringkasan ketersediaan seluruh barang inventaris</p>
    </div>
    <div>
        <a href="{{ route('reports.stock.export', request()->query()) }}" class="btn btn-success">
            <i class="bi bi-file-earmark-excel me-1"></i> Export Data CSV
        </a>
    </div>
</div>

<!-- Filter Card -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('reports.stock') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <select name="category_id" class="form-select">
                    <option value="">-- Semua Kategori --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-center">
                <div class="form-check">
                    <input type="checkbox" name="low_stock" value="1" class="form-check-input" id="lowStockCheck" {{ request('low_stock') ? 'checked' : '' }}>
                    <label class="form-check-label text-danger fw-semibold small" for="lowStockCheck">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>Filter Hanya Stok Menipis
                    </label>
                </div>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-dark w-100"><i class="bi bi-filter me-1"></i>Terapkan Filter</button>
                <a href="{{ route('reports.stock') }}" class="btn btn-outline-secondary" title="Reset"><i class="bi bi-arrow-counterclockwise"></i></a>
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
                        <th class="ps-3">#</th>
                        <th>Kode SKU</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Stok Real-Time</th>
                        <th>Min. Stok</th>
                        <th>Harga Beli</th>
                        <th>Harga Jual</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td class="ps-3">{{ $loop->iteration + ($items->currentPage() - 1) * $items->perPage() }}</td>
                            <td><code>{{ $item->sku }}</code></td>
                            <td class="fw-semibold">{{ $item->name }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $item->category->name }}</span></td>
                            <td class="fw-bold fs-6 {{ $item->isLowStock() ? 'text-danger' : 'text-success' }}">
                                {{ number_format($item->current_stock) }} {{ $item->unit }}
                            </td>
                            <td>{{ number_format($item->min_stock) }} {{ $item->unit }}</td>
                            <td>Rp {{ number_format($item->purchase_price, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($item->selling_price, 0, ',', '.') }}</td>
                            <td>
                                @if($item->isLowStock())
                                    <span class="badge bg-danger">Stok Menipis</span>
                                @else
                                    <span class="badge bg-success">Aman</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">Tidak ada data barang yang sesuai filter.</td>
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
