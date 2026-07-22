@extends('layouts.app')

@section('title', 'Tambah Kategori')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-tag me-2"></i>Tambah Kategori Barang</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('categories.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Nama Kategori</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" placeholder="Contoh: Elektronik" required>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label fw-semibold">Deskripsi (Opsional)</label>
                        <textarea name="description" id="description" rows="3" class="form-control" placeholder="Keterangan singkat tentang kategori">{{ old('description') }}</textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('categories.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan Kategori</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
