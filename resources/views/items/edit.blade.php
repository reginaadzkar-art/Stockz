@extends('layouts.app')

@section('title', 'Edit Data Barang & Variasi')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-pencil-square me-2"></i>Edit Data Barang & Variasi</h5>
                <a href="{{ route('items.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('items.update', $item) }}" method="POST" id="formEditItem">
                    @csrf
                    @method('PUT')

                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-info-circle me-2"></i>Informasi Utama Produk</h6>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="sku" class="form-label fw-semibold">Kode SKU Utama <span class="text-danger">*</span></label>
                            <input type="text" name="sku" id="sku" class="form-control @error('sku') is-invalid @enderror" value="{{ old('sku', $item->sku) }}" required>
                            @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-8">
                            <label for="name" class="form-label fw-semibold">Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $item->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="category_id" class="form-label fw-semibold">Kategori Barang <span class="text-danger">*</span></label>
                            <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $item->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="unit" class="form-label fw-semibold">Satuan Unit <span class="text-danger">*</span></label>
                            <input type="text" name="unit" id="unit" class="form-control @error('unit') is-invalid @enderror" value="{{ old('unit', $item->unit) }}" required>
                            @error('unit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="min_stock" class="form-label fw-semibold">Batas Stok Minimum Default (Alert)</label>
                            <input type="number" name="min_stock" id="min_stock" class="form-control @error('min_stock') is-invalid @enderror" value="{{ old('min_stock', $item->min_stock) }}" min="0" required>
                        </div>
                        <div class="col-md-6">
                            <label for="description" class="form-label fw-semibold">Deskripsi / Detail Bahan (Opsional)</label>
                            <input type="text" name="description" id="description" class="form-control" value="{{ old('description', $item->description) }}">
                        </div>
                    </div>

                    @php
                        $hasMultipleVariants = $item->variants->count() > 1 || ($item->variants->count() == 1 && ($item->variants->first()->color || $item->variants->first()->size));
                    @endphp

                    <!-- TOGGLE VARIASI -->
                    <div class="card border-primary bg-light mb-4">
                        <div class="card-body p-3">
                            <div class="form-check form-switch fs-5">
                                <input class="form-check-input" type="checkbox" role="switch" id="has_variants" name="has_variants" value="1" {{ old('has_variants', $hasMultipleVariants ? '1' : '0') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold text-primary" for="has_variants">
                                    <i class="bi bi-palette me-2"></i>Produk Punya Variasi (Warna & Ukuran)
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- SINGLE ITEM PRICE & STOCK (IF NO VARIANTS) -->
                    <div id="singleItemSection" class="card border-0 bg-white shadow-sm mb-4 {{ old('has_variants', $hasMultipleVariants ? '1' : '0') == '1' ? 'd-none' : '' }}">
                        <div class="card-body p-3">
                            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-tag me-2"></i>Harga Barang Standar</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="purchase_price" class="form-label fw-semibold">Harga Beli (Rp)</label>
                                    <input type="number" step="0.01" name="purchase_price" id="purchase_price" class="form-control" value="{{ old('purchase_price', $item->purchase_price) }}" min="0">
                                </div>
                                <div class="col-md-6">
                                    <label for="selling_price" class="form-label fw-semibold">Harga Jual (Rp)</label>
                                    <input type="number" step="0.01" name="selling_price" id="selling_price" class="form-control" value="{{ old('selling_price', $item->selling_price) }}" min="0">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- MULTI VARIANTS SECTION -->
                    <div id="variantsSection" class="{{ old('has_variants', $hasMultipleVariants ? '1' : '0') == '1' ? '' : 'd-none' }}">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="fw-bold text-dark mb-0"><i class="bi bi-grid-3x3-gap me-2"></i>Kelola Variasi (Warna & Ukuran)</h6>
                                <small class="text-muted">Edit warna, ukuran dropdown, SKU otomatis, harga, dan stok per varian.</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-primary" id="btnAddVariant">
                                <i class="bi bi-plus-lg me-1"></i> Tambah Varian Baru
                            </button>
                        </div>

                        <div class="table-responsive mb-4">
                            <table class="table table-bordered align-middle table-hover" id="variantTable">
                                <thead class="table-dark small">
                                    <tr>
                                        <th style="width: 18%;">Warna</th>
                                        <th style="width: 16%;">Ukuran (Dropdown)</th>
                                        <th style="width: 22%;">Kode SKU Varian</th>
                                        <th style="width: 14%;">Harga Beli (Rp)</th>
                                        <th style="width: 14%;">Harga Jual (Rp)</th>
                                        <th style="width: 10%;">Stok saat ini</th>
                                        <th style="width: 6%;" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="variantTableBody">
                                    @forelse($item->variants as $index => $variant)
                                        <tr class="variant-row">
                                            <td>
                                                <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant->id }}">
                                                <input type="text" name="variants[{{ $index }}][color]" class="form-control form-control-sm input-color" value="{{ $variant->color }}" placeholder="e.g. Mocca">
                                            </td>
                                            <td>
                                                <select name="variants[{{ $index }}][size]" class="form-select form-select-sm input-size">
                                                    @php $currentSize = $variant->size ?: 'All Size'; @endphp
                                                    <option value="All Size" {{ $currentSize === 'All Size' ? 'selected' : '' }}>All Size</option>
                                                    <option value="S" {{ $currentSize === 'S' ? 'selected' : '' }}>S</option>
                                                    <option value="M" {{ $currentSize === 'M' ? 'selected' : '' }}>M</option>
                                                    <option value="L" {{ $currentSize === 'L' ? 'selected' : '' }}>L</option>
                                                    <option value="XL" {{ $currentSize === 'XL' ? 'selected' : '' }}>XL</option>
                                                    <option value="XXL" {{ $currentSize === 'XXL' ? 'selected' : '' }}>XXL</option>
                                                    <option value="3XL" {{ $currentSize === '3XL' ? 'selected' : '' }}>3XL</option>
                                                    <option value="Jumbo" {{ $currentSize === 'Jumbo' ? 'selected' : '' }}>Jumbo</option>
                                                    <option value="Standard" {{ $currentSize === 'Standard' ? 'selected' : '' }}>Standard</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" name="variants[{{ $index }}][sku]" class="form-control form-control-sm input-variant-sku font-mono text-primary fw-bold" value="{{ $variant->sku }}" style="background: #f1f5f9;">
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" name="variants[{{ $index }}][purchase_price]" class="form-control form-control-sm input-purchase" value="{{ $variant->purchase_price }}" min="0" required>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" name="variants[{{ $index }}][selling_price]" class="form-control form-control-sm input-selling" value="{{ $variant->selling_price }}" min="0" required>
                                            </td>
                                            <td>
                                                <input type="number" name="variants[{{ $index }}][current_stock]" class="form-control form-control-sm input-stock" value="{{ $variant->current_stock }}" min="0" required>
                                                <input type="hidden" name="variants[{{ $index }}][min_stock]" class="input-min-stock" value="{{ $variant->min_stock }}">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-variant">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="variant-row">
                                            <td>
                                                <input type="hidden" name="variants[0][id]" value="">
                                                <input type="text" name="variants[0][color]" class="form-control form-control-sm input-color" placeholder="e.g. Mocca">
                                            </td>
                                            <td>
                                                <select name="variants[0][size]" class="form-select form-select-sm input-size">
                                                    <option value="All Size" selected>All Size</option>
                                                    <option value="S">S</option>
                                                    <option value="M">M</option>
                                                    <option value="L">L</option>
                                                    <option value="XL">XL</option>
                                                    <option value="XXL">XXL</option>
                                                    <option value="3XL">3XL</option>
                                                    <option value="Jumbo">Jumbo</option>
                                                    <option value="Standard">Standard</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" name="variants[0][sku]" class="form-control form-control-sm input-variant-sku font-mono text-primary fw-bold" value="{{ $item->sku }}" style="background: #f1f5f9;">
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" name="variants[0][purchase_price]" class="form-control form-control-sm input-purchase" value="{{ $item->purchase_price }}" min="0">
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" name="variants[0][selling_price]" class="form-control form-control-sm input-selling" value="{{ $item->selling_price }}" min="0">
                                            </td>
                                            <td>
                                                <input type="number" name="variants[0][current_stock]" class="form-control form-control-sm input-stock" value="{{ $item->current_stock }}" min="0">
                                                <input type="hidden" name="variants[0][min_stock]" class="input-min-stock" value="{{ $item->min_stock }}">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-variant">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <a href="{{ route('items.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-circle me-1"></i>Simpan Perubahan</button>
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
    const hasVariantsSwitch = document.getElementById('has_variants');
    const singleSection = document.getElementById('singleItemSection');
    const variantsSection = document.getElementById('variantsSection');
    const variantTableBody = document.getElementById('variantTableBody');
    const btnAddVariant = document.getElementById('btnAddVariant');
    const masterSkuInput = document.getElementById('sku');
    const masterMinStockInput = document.getElementById('min_stock');
    let variantIndex = {{ $item->variants->count() ?: 1 }};

    function slugify(text) {
        if (!text) return '';
        return text.toString().toLowerCase().trim()
            .replace(/\s+/g, '-')
            .replace(/[^\w\-]+/g, '')
            .replace(/\-\-+/g, '-')
            .toUpperCase();
    }

    function generateVariantSku(row) {
        const masterSku = masterSkuInput.value.trim();
        const colorVal = row.querySelector('.input-color').value.trim();
        const sizeVal = row.querySelector('.input-size').value.trim() || 'All Size';

        const colorSlug = colorVal ? slugify(colorVal) : 'VAR';
        const sizeSlug = slugify(sizeVal);

        const skuInput = row.querySelector('.input-variant-sku');
        if (masterSku && (!skuInput.value || skuInput.hasAttribute('data-auto-generated'))) {
            skuInput.value = `${masterSku}-${colorSlug}-${sizeSlug}`;
            skuInput.setAttribute('data-auto-generated', 'true');
        }
    }

    function toggleVariantVisibility() {
        if (hasVariantsSwitch.checked) {
            singleSection.classList.add('d-none');
            variantsSection.classList.remove('d-none');
        } else {
            singleSection.classList.remove('d-none');
            variantsSection.classList.add('d-none');
        }
    }

    hasVariantsSwitch.addEventListener('change', toggleVariantVisibility);

    function updateRemoveVariantButtons() {
        const rows = variantTableBody.querySelectorAll('.variant-row');
        rows.forEach(row => {
            const btn = row.querySelector('.btn-remove-variant');
            btn.disabled = rows.length <= 1;
        });
    }

    updateRemoveVariantButtons();

    // Auto SKU update on input change
    variantTableBody.addEventListener('input', function(e) {
        if (e.target.classList.contains('input-color')) {
            const row = e.target.closest('tr');
            generateVariantSku(row);
        }
    });

    variantTableBody.addEventListener('change', function(e) {
        if (e.target.classList.contains('input-size')) {
            const row = e.target.closest('tr');
            generateVariantSku(row);
        }
    });

    btnAddVariant.addEventListener('click', function() {
        const masterSku = masterSkuInput.value.trim();
        const tr = document.createElement('tr');
        tr.className = 'variant-row';
        tr.innerHTML = `
            <td>
                <input type="hidden" name="variants[${variantIndex}][id]" value="">
                <input type="text" name="variants[${variantIndex}][color]" class="form-control form-control-sm input-color" value="" placeholder="e.g. Mocca">
            </td>
            <td>
                <select name="variants[${variantIndex}][size]" class="form-select form-select-sm input-size">
                    <option value="All Size" selected>All Size</option>
                    <option value="S">S</option>
                    <option value="M">M</option>
                    <option value="L">L</option>
                    <option value="XL">XL</option>
                    <option value="XXL">XXL</option>
                    <option value="3XL">3XL</option>
                    <option value="Jumbo">Jumbo</option>
                    <option value="Standard">Standard</option>
                </select>
            </td>
            <td>
                <input type="text" name="variants[${variantIndex}][sku]" class="form-control form-control-sm input-variant-sku font-mono text-primary fw-bold" value="${masterSku ? masterSku + '-VAR-ALL' : ''}" style="background: #f1f5f9;" data-auto-generated="true">
            </td>
            <td>
                <input type="number" step="0.01" name="variants[${variantIndex}][purchase_price]" class="form-control form-control-sm input-purchase" value="0" min="0">
            </td>
            <td>
                <input type="number" step="0.01" name="variants[${variantIndex}][selling_price]" class="form-control form-control-sm input-selling" value="0" min="0">
            </td>
            <td>
                <input type="number" name="variants[${variantIndex}][current_stock]" class="form-control form-control-sm input-stock" value="0" min="0">
                <input type="hidden" name="variants[${variantIndex}][min_stock]" class="input-min-stock" value="${masterMinStockInput.value || 5}">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-variant">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;

        variantTableBody.appendChild(tr);
        variantIndex++;
        updateRemoveVariantButtons();
    });

    variantTableBody.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove-variant')) {
            const rows = variantTableBody.querySelectorAll('.variant-row');
            if (rows.length > 1) {
                e.target.closest('tr').remove();
                updateRemoveVariantButtons();
            }
        }
    });

    masterMinStockInput.addEventListener('input', function() {
        const val = this.value || 5;
        document.querySelectorAll('.input-min-stock').forEach(input => {
            input.value = val;
        });
    });
});
</script>
@endpush
