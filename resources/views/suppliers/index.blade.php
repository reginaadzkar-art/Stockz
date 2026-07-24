@extends('layouts.app')

@section('title', 'Kelola Supplier')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1 text-dark">Kelola Supplier</h2>
        <p class="text-muted small mb-0">Daftar mitra distributor, vendor, dan pemasok barang inventaris.</p>
    </div>
    <a href="{{ route('suppliers.create') }}" class="btn btn-emerald fw-bold d-flex align-items-center gap-2 px-3 py-2" style="background: var(--brand-emerald); color: #fff; border-radius: 10px;">
        <i class="bi bi-truck-front-fill fs-5"></i> Tambah Supplier Baru
    </a>
</div>

<div class="glass-card mb-4">
    <div class="glass-card-header">
        <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-truck text-emerald me-2"></i>Daftar Supplier / Vendor</h6>
        <span class="badge bg-light border text-muted font-mono">{{ $suppliers->total() }} Vendors</span>
    </div>

    <div class="table-responsive">
        <table class="table table-custom align-middle">
            <thead>
                <tr>
                    <th class="ps-4">No</th>
                    <th>Nama Supplier</th>
                    <th>Telepon / Kontak</th>
                    <th>Email</th>
                    <th>Alamat</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suppliers as $supplier)
                    <tr>
                        <td class="ps-4 text-muted font-mono">{{ $loop->iteration + ($suppliers->currentPage() - 1) * $suppliers->perPage() }}</td>
                        <td class="fw-semibold text-dark">{{ $supplier->name }}</td>
                        <td class="font-mono text-emerald">{{ $supplier->phone ?? '-' }}</td>
                        <td class="text-muted">{{ $supplier->email ?? '-' }}</td>
                        <td class="text-muted">{{ $supplier->address ?? '-' }}</td>
                        <td class="text-end pe-4">
                            <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-outline-secondary btn-sm rounded-circle me-1" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus supplier ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-circle" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-truck fs-1 d-block mb-2"></i>
                            Belum ada data supplier.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($suppliers->hasPages())
        <div class="p-3 border-top border-glass d-flex justify-content-center">
            {{ $suppliers->links() }}
        </div>
    @endif
</div>
@endsection
