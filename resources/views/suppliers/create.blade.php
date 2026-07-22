@extends('layouts.app')

@section('title', 'Tambah Supplier')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-truck me-2"></i>Tambah Supplier Baru</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('suppliers.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Nama Supplier / Perusahaan</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" placeholder="PT Sumber Jaya" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="phone" class="form-label fw-semibold">No. Telepon / WA</label>
                            <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone') }}" placeholder="08123456789">
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" placeholder="supplier@example.com">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label fw-semibold">Alamat Lengkap</label>
                        <textarea name="address" id="address" rows="3" class="form-control" placeholder="Alamat fisik supplier">{{ old('address') }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label for="notes" class="form-label fw-semibold">Catatan (Opsional)</label>
                        <textarea name="notes" id="notes" rows="2" class="form-control" placeholder="Informasi kontak tambahan/syarat pembayaran">{{ old('notes') }}</textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan Supplier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
