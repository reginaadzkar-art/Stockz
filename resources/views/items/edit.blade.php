@extends('layouts.app')

@section('title', 'Edit Barang')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-pencil me-2"></i>Edit Data Barang</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('items.update', $item) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="sku" class="form-label fw-semibold">Kode SKU</label>
                            <input type="text" name="sku" id="sku" class="form-control" value="{{ old('sku', $item->sku) }}" required>
                        </div>
                        <div class="col-md-8">
                            <label for="name" class="form-label fw-semibold">Nama Barang</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $item->name) }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="category_id" class="form-label fw-semibold">Kategori Barang</label>
                            <select name="category_id" id="category_id" class="form-select" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $item->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="unit" class="form-label fw-semibold">Satuan Unit</label>
                            <input type="text" name="unit" id="unit" class="form-control" value="{{ old('unit', $item->unit) }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="min_stock" class="form-label fw-semibold">Batas Stok Minimum (Alert)</label>
                            <input type="number" name="min_stock" id="min_stock" class="form-control" value="{{ old('min_stock', $item->min_stock) }}" min="0" required>
                        </div>
                        <div class="col-md-6">
                            <label for="current_stock" class="form-label fw-semibold">Stok Real-Time Saat Ini</label>
                            <input type="number" name="current_stock" id="current_stock" class="form-control" value="{{ old('current_stock', $item->current_stock) }}" min="0" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="purchase_price" class="form-label fw-semibold">Harga Beli (Rp)</label>
                            <input type="number" step="0.01" name="purchase_price" id="purchase_price" class="form-control" value="{{ old('purchase_price', $item->purchase_price) }}" min="0" required>
                        </div>
                        <div class="col-md-6">
                            <label for="selling_price" class="form-label fw-semibold">Harga Jual (Rp)</label>
                            <input type="number" step="0.01" name="selling_price" id="selling_price" class="form-control" value="{{ old('selling_price', $item->selling_price) }}" min="0" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label fw-semibold">Deskripsi / Spesifikasi (Opsional)</label>
                        <textarea name="description" id="description" rows="3" class="form-control">{{ old('description', $item->description) }}</textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('items.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Perbarui Barang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
