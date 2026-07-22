@extends('layouts.app')

@section('title', 'Input Barang Keluar')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-danger text-white py-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-arrow-up-right-circle me-2"></i>Input Transaksi Barang Keluar (Penjualan / Pengeluaran)</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('stock-movements.out.store') }}" method="POST" id="formStockOut">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="date" class="form-label fw-semibold">Tanggal Keluar</label>
                            <input type="date" name="date" id="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-8">
                            <label for="recipient_or_destination" class="form-label fw-semibold">Penerima / Tujuan / Customer</label>
                            <input type="text" name="recipient_or_destination" id="recipient_or_destination" class="form-control" value="{{ old('recipient_or_destination') }}" placeholder="Contoh: Toko Maju Jaya / Divisi Operasional" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="notes" class="form-label fw-semibold">Catatan Transaksi (Opsional)</label>
                        <input type="text" name="notes" id="notes" class="form-control" value="{{ old('notes') }}" placeholder="Keterangan keperluan pengeluaran barang">
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0"><i class="bi bi-list-check me-2"></i>Daftar Barang Keluar:</h6>
                        <button type="button" class="btn btn-sm btn-outline-danger" id="btnAddRow">
                            <i class="bi bi-plus-lg me-1"></i> Tambah Baris Barang
                        </button>
                    </div>

                    <div class="table-responsive mb-4">
                        <table class="table table-bordered align-middle" id="itemTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 45%;">Pilih Barang (Stok Tersedia)</th>
                                    <th style="width: 20%;">Jumlah Keluar (Qty)</th>
                                    <th style="width: 25%;">Harga Jual Satuan (Rp)</th>
                                    <th style="width: 10%;" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="itemTableBody">
                                <tr class="item-row">
                                    <td>
                                        <select name="items[0][item_id]" class="form-select select-item" required>
                                            <option value="">-- Pilih Barang --</option>
                                            @foreach($items as $item)
                                                <option value="{{ $item->id }}" data-price="{{ $item->selling_price }}" data-stock="{{ $item->current_stock }}">
                                                    [{{ $item->sku }}] {{ $item->name }} — (Stok Tersedia: {{ $item->current_stock }} {{ $item->unit }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="items[0][quantity]" class="form-control input-qty" min="1" value="1" required>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="items[0][price]" class="form-control input-price" min="0" value="0" required>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row" disabled>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('stock-movements.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-danger px-4"><i class="bi bi-check-circle me-1"></i>Simpan Barang Keluar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let rowIndex = 1;
    const tableBody = document.getElementById('itemTableBody');
    const btnAddRow = document.getElementById('btnAddRow');

    function updateRemoveButtons() {
        const rows = tableBody.querySelectorAll('.item-row');
        rows.forEach(row => {
            const btn = row.querySelector('.btn-remove-row');
            btn.disabled = rows.length <= 1;
        });
    }

    tableBody.addEventListener('change', function(e) {
        if (e.target.classList.contains('select-item')) {
            const selectedOption = e.target.options[e.target.selectedIndex];
            const price = selectedOption.getAttribute('data-price') || 0;
            const stock = parseInt(selectedOption.getAttribute('data-stock') || 0);
            const row = e.target.closest('tr');
            
            const priceInput = row.querySelector('.input-price');
            const qtyInput = row.querySelector('.input-qty');

            priceInput.value = price;
            qtyInput.max = stock;
        }
    });

    btnAddRow.addEventListener('click', function() {
        const firstRow = tableBody.querySelector('.item-row');
        const newRow = firstRow.cloneNode(true);

        newRow.querySelectorAll('input, select').forEach(input => {
            if (input.tagName === 'SELECT') {
                input.name = `items[${rowIndex}][item_id]`;
                input.selectedIndex = 0;
            } else if (input.classList.contains('input-qty')) {
                input.name = `items[${rowIndex}][quantity]`;
                input.value = 1;
                input.removeAttribute('max');
            } else if (input.classList.contains('input-price')) {
                input.name = `items[${rowIndex}][price]`;
                input.value = 0;
            }
        });

        tableBody.appendChild(newRow);
        rowIndex++;
        updateRemoveButtons();
    });

    tableBody.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove-row')) {
            const rows = tableBody.querySelectorAll('.item-row');
            if (rows.length > 1) {
                e.target.closest('tr').remove();
                updateRemoveButtons();
            }
        }
    });
});
</script>
@endpush
