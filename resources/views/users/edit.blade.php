@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-pencil me-2"></i>Edit User</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Nama Lengkap</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Alamat Email</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label fw-semibold">Role / Hak Akses</label>
                        <select name="role" id="role" class="form-select" required>
                            <option value="staff" {{ old('role', $user->role) == 'staff' ? 'selected' : '' }}>Staff (Input Stok & Edit Barang)</option>
                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin (Pengelola Seluruh Data)</option>
                            <option value="owner" {{ old('role', $user->role) == 'owner' ? 'selected' : '' }}>Owner (Melihat Dashboard & Laporan)</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label fw-semibold">Password Baru (Opsional)</label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Kosongkan jika tidak diubah" minlength="6">
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Perbarui User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
