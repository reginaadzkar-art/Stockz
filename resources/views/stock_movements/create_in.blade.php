@extends('layouts.app')

@section('title', 'Input Barang Masuk')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-emerald py-3">
                <h5 class="text-white fw-bold mb-0"><i class="bi bi-arrow-down-left-circle me-2"></i>Input Transaksi Barang Masuk (Restok / Pembelian)</h5>
            </div>
            <div class="card-body bg-surface text-body">
                <form action="{{ route('stock-movements.in.store') }}" method="POST" id="formStockIn">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="date" class="form-label fw-semibold">Tanggal Masuk</label>
                            <input type="date" name="date" id="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-8">
                            <label for="supplier_id" class="form-label fw-semibold">Supplier / Pemasok</label>
                            <select name="supplier_id" id="supplier_id" class="form-select">
                                <option value="">-- Tanpa Supplier / Pembelian Langsung --</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }} {{ $supplier->phone ? "({$supplier->phone})" : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="notes" class="form-label fw-semibold">Catatan Transaksi (Opsional)</label>
                        <input type="text" name="notes" id="notes" class="form-control" value="{{ old('notes') }}" placeholder="Contoh: Pembelian invoice #INV-9923 / Restok Hijab">
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0"><i class="bi bi-list-check me-2"></i>Daftar Barang & Variasi Masuk:</h6>
                        <button type="button" class="btn btn-sm btn-outline-success" id="btnAddRow">
                            <i class="bi bi-plus-lg me-1"></i> Tambah Baris Barang
                        </button>
                    </div>

                    <div class="table-responsive mb-4">
                        <table class="table table-bordered align-middle" id="itemTable">
                            <thead class="bg-surface text-muted">
                                <tr>
                                    <th style="width: 45%;">Pilih Barang & Variasi</th>
                                    <th style="width: 20%;">Jumlah (Qty)</th>
                                    <th style="width: 25%;">Harga Beli Satuan (Rp)</th>
                                    <th style="width: 10%;" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="itemTableBody">
                                <tr class="item-row">
                                    <td>
                                        <input type="hidden" name="items[0][item_id]" class="input-item-id">
                                        <select name="items[0][item_variant_id]" class="form-select select-variant" required>
                                            <option value="">-- Pilih Barang / Variasi Warna & Ukuran --</option>
                                            @foreach($items as $item)
                                                @if($item->variants->count() > 0)
                                                    <optgroup label="{{ $item->name }} (SKU: {{ $item->sku }})">
                                                        @foreach($item->variants as $variant)
                                                            <option value="{{ $variant->id }}" data-item-id="{{ $item->id }}" data-price="{{ $variant->purchase_price }}">
                                                                [{{ $variant->sku }}] {{ $item->name }} — [{{ $variant->variant_label }}] (Stok: {{ $variant->current_stock }} {{ $item->unit }})
                                                            </option>
                                                        @endforeach
                                                    </optgroup>
                                                @else
                                                    <option value="" data-item-id="{{ $item->id }}" data-price="{{ $item->purchase_price }}">
                                                        [{{ $item->sku }}] {{ $item->name }} (Stok: {{ $item->current_stock }} {{ $item->unit }})
                                                    </option>
                                                @endif
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
                        <button type="submit" class="btn btn-success px-4"><i class="bi bi-check-circle me-1"></i>Simpan Barang Masuk</button>
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
        if (e.target.classList.contains('select-variant')) {
            const selectedOption = e.target.options[e.target.selectedIndex];
            const itemId = selectedOption.getAttribute('data-item-id') || '';
            const price = selectedOption.getAttribute('data-price') || 0;
            const row = e.target.closest('tr');
            
            const itemIdInput = row.querySelector('.input-item-id');
            const priceInput = row.querySelector('.input-price');
            
            itemIdInput.value = itemId;
            priceInput.value = price;
        }
    });

    btnAddRow.addEventListener('click', function() {
        const firstRow = tableBody.querySelector('.item-row');
        const newRow = firstRow.cloneNode(true);

        newRow.querySelectorAll('input, select').forEach(input => {
            if (input.classList.contains('input-item-id')) {
                input.name = `items[${rowIndex}][item_id]`;
                input.value = '';
            } else if (input.tagName === 'SELECT') {
                input.name = `items[${rowIndex}][item_variant_id]`;
                input.selectedIndex = 0;
            } else if (input.classList.contains('input-qty')) {
                input.name = `items[${rowIndex}][quantity]`;
                input.value = 1;
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
