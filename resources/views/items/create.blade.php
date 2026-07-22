@extends('layouts.app')

@section('title', 'Tambah Barang')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-box-seam me-2"></i>Tambah Barang Baru</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('items.store') }}" method="POST">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="sku" class="form-label fw-semibold">Kode SKU</label>
                            <input type="text" name="sku" id="sku" class="form-control" value="{{ old('sku', 'BRG-' . strtoupper(Str::random(6))) }}" required>
                        </div>
                        <div class="col-md-8">
                            <label for="name" class="form-label fw-semibold">Nama Barang</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" placeholder="Contoh: Monitor LED 24 Inch" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="category_id" class="form-label fw-semibold">Kategori Barang</label>
                            <select name="category_id" id="category_id" class="form-select" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="unit" class="form-label fw-semibold">Satuan Unit</label>
                            <input type="text" name="unit" id="unit" class="form-control" value="{{ old('unit', 'pcs') }}" placeholder="pcs, unit, kg, box, dll." required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="min_stock" class="form-label fw-semibold">Batas Stok Minimum (Alert)</label>
                            <input type="number" name="min_stock" id="min_stock" class="form-control" value="{{ old('min_stock', 5) }}" min="0" required>
                        </div>
                        <div class="col-md-6">
                            <label for="current_stock" class="form-label fw-semibold">Stok Awal Real-Time</label>
                            <input type="number" name="current_stock" id="current_stock" class="form-control" value="{{ old('current_stock', 0) }}" min="0" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="purchase_price" class="form-label fw-semibold">Harga Beli (Rp)</label>
                            <input type="number" step="0.01" name="purchase_price" id="purchase_price" class="form-control" value="{{ old('purchase_price', 0) }}" min="0" required>
                        </div>
                        <div class="col-md-6">
                            <label for="selling_price" class="form-label fw-semibold">Harga Jual (Rp)</label>
                            <input type="number" step="0.01" name="selling_price" id="selling_price" class="form-control" value="{{ old('selling_price', 0) }}" min="0" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label fw-semibold">Deskripsi / Spesifikasi (Opsional)</label>
                        <textarea name="description" id="description" rows="3" class="form-control" placeholder="Spesifikasi barang atau lokasi rak simpan">{{ old('description') }}</textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('items.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan Barang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
