@extends('layouts.app')

@section('title', 'Kategori Barang')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1 text-dark">Kategori Barang</h2>
        <p class="text-muted small mb-0">Kelola kelompok/kategori pengelompokan produk inventaris.</p>
    </div>
    <a href="{{ route('categories.create') }}" class="btn btn-emerald fw-bold d-flex align-items-center gap-2 px-3 py-2" style="background: var(--brand-emerald); color: #fff; border-radius: 10px;">
        <i class="bi bi-plus-circle-fill fs-5"></i> Tambah Kategori Baru
    </a>
</div>

<div class="glass-card mb-4">
    <div class="glass-card-header">
        <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-tags text-emerald me-2"></i>Daftar Kategori Barang</h6>
        <span class="badge bg-light border text-muted font-mono">{{ $categories->total() }} Categories</span>
    </div>

    <div class="table-responsive">
        <table class="table table-custom align-middle">
            <thead>
                <tr>
                    <th class="ps-4">No</th>
                    <th>Nama Kategori</th>
                    <th>Slug</th>
                    <th>Deskripsi</th>
                    <th>Jumlah Barang</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                    <tr>
                        <td class="ps-4 text-muted font-mono">{{ $loop->iteration + ($categories->currentPage() - 1) * $categories->perPage() }}</td>
                        <td class="fw-semibold text-dark">{{ $category->name }}</td>
                        <td><code class="font-mono text-emerald px-2 py-1 rounded" style="background: rgba(0, 194, 136, 0.1);">{{ $category->slug }}</code></td>
                        <td class="text-muted">{{ $category->description ?? '-' }}</td>
                        <td><span class="badge bg-light border text-dark px-3 py-1 font-mono">{{ $category->items_count }} Items</span></td>
                        <td class="text-end pe-4">
                            <a href="{{ route('categories.edit', $category) }}" class="btn btn-outline-secondary btn-sm rounded-circle me-1" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-circle" {{ $category->items_count > 0 ? 'disabled title="Kategori masih memiliki barang"' : '' }}>
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-tags fs-1 d-block mb-2"></i>
                            Belum ada data kategori barang.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($categories->hasPages())
        <div class="p-3 border-top border-glass d-flex justify-content-center">
            {{ $categories->links() }}
        </div>
    @endif
</div>
@endsection
