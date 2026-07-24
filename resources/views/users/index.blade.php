@extends('layouts.app')

@section('title', 'Manajemen User & Auth Permissions')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1 text-dark">Kelola Pengguna & Peran Access</h2>
        <p class="text-muted small mb-0">Manajemen akun otentikasi pengguna, penetapan peran (Admin, Staff, Owner), dan batasan hak akses.</p>
    </div>
    <a href="{{ route('users.create') }}" class="btn btn-emerald fw-bold d-flex align-items-center gap-2 px-3 py-2" style="background: var(--brand-emerald); color: #fff; border-radius: 10px;">
        <i class="bi bi-person-plus-fill fs-5"></i> Tambah User Baru
    </a>
</div>

<div class="glass-card mb-4">
    <div class="glass-card-header">
        <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-shield-lock text-emerald me-2"></i>Daftar Pengguna Sistem</h6>
        <span class="badge bg-light border text-muted font-mono">{{ $users->total() }} Active Accounts</span>
    </div>

    <div class="table-responsive">
        <table class="table table-custom align-middle">
            <thead>
                <tr>
                    <th class="ps-4">No</th>
                    <th>Nama User</th>
                    <th>Email</th>
                    <th>Peran (Role)</th>
                    <th>Dibuat Pada</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td class="ps-4 text-muted font-mono">{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-box">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark">{{ $user->name }}</div>
                                    @if($user->id === Auth::id())
                                        <span class="extra-small text-emerald">(Akun Anda)</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="font-mono text-muted">{{ $user->email }}</td>
                        <td>
                            @if($user->isAdmin())
                                <span class="role-pill role-admin">Admin System</span>
                            @elseif($user->isStaff())
                                <span class="role-pill role-staff">Staff Operator</span>
                            @elseif($user->isOwner())
                                <span class="role-pill role-owner">Executive Owner</span>
                            @endif
                        </td>
                        <td class="font-mono text-muted">{{ $user->created_at->format('d M Y') }}</td>
                        <td class="text-end pe-4">
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-outline-secondary btn-sm rounded-circle me-1" title="Edit User">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @if($user->id !== Auth::id())
                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-circle" title="Hapus User">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-people fs-1 d-block mb-2"></i>
                            Belum ada data user pengguna.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
        <div class="p-3 border-top border-glass d-flex justify-content-center">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection
