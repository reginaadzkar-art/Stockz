@extends('layouts.app')

@section('title', 'Detail Transaksi - ' . $stockMovement->reference_number)

@section('content')
<div class="mb-3 d-flex justify-content-between align-items-center">
    <a href="{{ route('stock-movements.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Riwayat Transaksi
    </a>
    <button onclick="window.print()" class="btn btn-outline-dark btn-sm d-print-none">
        <i class="bi bi-printer me-1"></i> Cetak Bukti Transaksi
    </button>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0">
            Bukti Transaksi: <code>{{ $stockMovement->reference_number }}</code>
        </h5>
        @if($stockMovement->type === 'in')
            <span class="badge bg-success fs-6"><i class="bi bi-arrow-down-left me-1"></i>BARANG MASUK</span>
        @else
            <span class="badge bg-danger fs-6"><i class="bi bi-arrow-up-right me-1"></i>BARANG KELUAR</span>
        @endif
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <table class="table table-borderless table-sm mb-0">
                    <tr>
                        <td class="text-muted" style="width: 140px;">Tanggal Transaksi</td>
                        <td class="fw-bold">: {{ $stockMovement->date->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Petugas Input</td>
                        <td class="fw-bold">: {{ $stockMovement->user->name }} ({{ ucfirst($stockMovement->user->role) }})</td>
                    </tr>
                    @if($stockMovement->type === 'in')
                        <tr>
                            <td class="text-muted">Supplier / Pemasok</td>
                            <td class="fw-bold">: {{ $stockMovement->supplier->name ?? '-' }}</td>
                        </tr>
                    @else
                        <tr>
                            <td class="text-muted">Penerima / Tujuan</td>
                            <td class="fw-bold">: {{ $stockMovement->recipient_or_destination ?? '-' }}</td>
                        </tr>
                    @endif
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless table-sm mb-0">
                    <tr>
                        <td class="text-muted" style="width: 140px;">Total Kuantitas</td>
                        <td class="fw-bold">: {{ number_format($stockMovement->total_quantity) }} unit/item</td>
                    </tr>
                    @if(!Auth::user()->isStaff())
                        <tr>
                            <td class="text-muted">Total Nilai</td>
                            <td class="fw-bold text-primary">: Rp {{ number_format($stockMovement->total_amount, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="text-muted">Catatan</td>
                        <td>: {{ $stockMovement->notes ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <h6 class="fw-bold mb-3">Rincian Barang:</h6>
        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Kode SKU</th>
                        <th>Nama Barang & Varian</th>
                        <th>Kategori</th>
                        @if(!Auth::user()->isStaff())
                            <th class="text-end">Harga Satuan</th>
                        @endif
                        <th class="text-center">Jumlah</th>
                        @if(!Auth::user()->isStaff())
                            <th class="text-end">Subtotal</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($stockMovement->details as $detail)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><code>{{ $detail->variant->sku ?? $detail->item->sku }}</code></td>
                            <td>
                                <div class="fw-semibold">{{ $detail->item->name }}</div>
                                @if($detail->variant)
                                    <span class="badge bg-light text-dark border small me-1">
                                        <i class="bi bi-tag me-1"></i>{{ $detail->variant->variant_label }}
                                    </span>
                                @endif
                            </td>
                            <td>{{ $detail->item->category->name ?? '-' }}</td>
                            @if(!Auth::user()->isStaff())
                                <td class="text-end">Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                            @endif
                            <td class="text-center fw-bold">{{ number_format($detail->quantity) }} {{ $detail->item->unit }}</td>
                            @if(!Auth::user()->isStaff())
                                <td class="text-end fw-bold">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="{{ Auth::user()->isStaff() ? 4 : 5 }}" class="text-end">TOTAL</td>
                        <td class="text-center">{{ number_format($stockMovement->total_quantity) }}</td>
                        @if(!Auth::user()->isStaff())
                            <td class="text-end text-primary">Rp {{ number_format($stockMovement->total_amount, 0, ',', '.') }}</td>
                        @endif
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
