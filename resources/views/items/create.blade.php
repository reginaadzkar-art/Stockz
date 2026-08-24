@extends('layouts.app')

@section('title', 'Tambah Barang')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-bag-plus me-2"></i>Tambah Barang Baru (Hijab & Fashion)</h5>
                <a href="{{ route('items.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('items.store') }}" method="POST" id="formItem">
                    @csrf

                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-info-circle me-2"></i>Informasi Utama Produk</h6>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="sku" class="form-label fw-semibold">Kode SKU Utama <span class="text-danger">*</span></label>
                            <input type="text" name="sku" id="sku" class="form-control @error('sku') is-invalid @enderror" value="{{ old('sku', 'BRG-' . strtoupper(Str::random(6))) }}" required>
                            @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-8">
                            <label for="name" class="form-label fw-semibold">Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Contoh: Pashmina Silk Premium / Gamis Syari Satin" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="category_id" class="form-label fw-semibold">Kategori Barang <span class="text-danger">*</span></label>
                            <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="unit" class="form-label fw-semibold">Satuan Unit <span class="text-danger">*</span></label>
                            <input type="text" name="unit" id="unit" class="form-control @error('unit') is-invalid @enderror" value="{{ old('unit', 'pcs') }}" placeholder="pcs, set, kodi, dsb." required>
                            @error('unit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="min_stock" class="form-label fw-semibold">Batas Stok Minimum Default (Alert)</label>
                            <input type="number" name="min_stock" id="min_stock" class="form-control @error('min_stock') is-invalid @enderror" value="{{ old('min_stock', 5) }}" min="0" required>
                            <small class="text-muted">Peringatan aktif jika stok varian kurang dari atau sama dengan angka ini.</small>
                        </div>
                        <div class="col-md-6">
                            <label for="description" class="form-label fw-semibold">Deskripsi / Detail Bahan (Opsional)</label>
                            <input type="text" name="description" id="description" class="form-control" value="{{ old('description') }}" placeholder="Contoh: Bahan Ceruty Baby Doll, Adem & Lembut">
                        </div>
                    </div>

                    <!-- TOGGLE VARIASI -->
                    <div class="card border-primary bg-light mb-4">
                        <div class="card-body p-3">
                            <div class="form-check form-switch fs-5">
                                <input class="form-check-input" type="checkbox" role="switch" id="has_variants" name="has_variants" value="1" {{ old('has_variants', '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold text-primary" for="has_variants">
                                    <i class="bi bi-palette me-2"></i>Produk Punya Variasi (Warna & Ukuran)
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- SINGLE ITEM PRICE & STOCK (IF NO VARIANTS) -->
                    <div id="singleItemSection" class="card border-0 bg-white shadow-sm mb-4 {{ old('has_variants', '1') == '1' ? 'd-none' : '' }}">
                        <div class="card-body p-3">
                            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-tag me-2"></i>Harga & Stok Barang Standar</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="current_stock" class="form-label fw-semibold">Stok Awal Real-Time</label>
                                    <input type="number" name="current_stock" id="current_stock" class="form-control" value="{{ old('current_stock', 0) }}" min="0">
                                </div>
                                <div class="col-md-4">
                                    <label for="purchase_price" class="form-label fw-semibold">Harga Beli (Rp)</label>
                                    <input type="number" step="0.01" name="purchase_price" id="purchase_price" class="form-control" value="{{ old('purchase_price', 0) }}" min="0">
                                </div>
                                <div class="col-md-4">
                                    <label for="selling_price" class="form-label fw-semibold">Harga Jual (Rp)</label>
                                    <input type="number" step="0.01" name="selling_price" id="selling_price" class="form-control" value="{{ old('selling_price', 0) }}" min="0">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- MULTI VARIANTS SECTION -->
                    <div id="variantsSection" class="{{ old('has_variants', '1') == '1' ? '' : 'd-none' }}">

                        <!-- AUTOMATIC MATRIX GENERATOR BOX -->
                        <div class="card border-primary mb-4 shadow-sm" style="background: #f8fafc;">
                            <div class="card-header bg-primary text-white py-2 font-weight-600 d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-cpu me-2"></i>Generator Kombinasi Otomatis (Warna & Ukuran)</span>
                                <span class="badge bg-white text-primary">Matriks Varian</span>
                            </div>
                            <div class="card-body p-3">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold mb-1">Pilihan Warna (Pisahkan dengan koma atau klik preset)</label>
                                        <input type="text" id="gen_colors" class="form-control form-control-sm mb-2" placeholder="Contoh: Hitam, Mocca, Sage Green, Navy, Dusty Pink">
                                        
                                        <!-- Quick Color Preset Pills -->
                                        <div class="d-flex flex-wrap gap-1 align-items-center">
                                            <span class="small text-muted me-1">Preset:</span>
                                            <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill btn-gen-color" data-color="Hitam">+ Hitam</button>
                                            <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill btn-gen-color" data-color="Mocca">+ Mocca</button>
                                            <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill btn-gen-color" data-color="Sage Green">+ Sage Green</button>
                                            <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill btn-gen-color" data-color="Navy">+ Navy</button>
                                            <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill btn-gen-color" data-color="Dusty Pink">+ Dusty Pink</button>
                                            <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill btn-gen-color" data-color="Broken White">+ Broken White</button>
                                            <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill btn-gen-color" data-color="Maroon">+ Maroon</button>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold mb-1">Pilihan Ukuran (Centang ukuran yang ada)</label>
                                        <div class="d-flex flex-wrap gap-2 border p-2 rounded bg-white" id="gen_sizes_box">
                                            <div class="form-check">
                                                <input class="form-check-input chk-gen-size" type="checkbox" value="All Size" id="sz_all" checked>
                                                <label class="form-check-label small fw-bold text-primary" for="sz_all">All Size</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input chk-gen-size" type="checkbox" value="S" id="sz_s">
                                                <label class="form-check-label small" for="sz_s">S</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input chk-gen-size" type="checkbox" value="M" id="sz_m">
                                                <label class="form-check-label small" for="sz_m">M</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input chk-gen-size" type="checkbox" value="L" id="sz_l">
                                                <label class="form-check-label small" for="sz_l">L</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input chk-gen-size" type="checkbox" value="XL" id="sz_xl">
                                                <label class="form-check-label small" for="sz_xl">XL</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input chk-gen-size" type="checkbox" value="XXL" id="sz_xxl">
                                                <label class="form-check-label small" for="sz_xxl">XXL</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input chk-gen-size" type="checkbox" value="Jumbo" id="sz_jumbo">
                                                <label class="form-check-label small" for="sz_jumbo">Jumbo</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label small fw-semibold">Default Harga Beli (Rp)</label>
                                        <input type="number" step="0.01" id="gen_purchase" class="form-control form-control-sm" value="0" min="0">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-semibold">Default Harga Jual (Rp)</label>
                                        <input type="number" step="0.01" id="gen_selling" class="form-control form-control-sm" value="0" min="0">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-semibold">Default Stok Awal</label>
                                        <input type="number" id="gen_stock" class="form-control form-control-sm" value="0" min="0">
                                    </div>
                                    <div class="col-md-3 d-flex align-items-end">
                                        <button type="button" class="btn btn-primary btn-sm w-100 fw-bold" id="btnGenerateMatrix">
                                            <i class="bi bi-lightning-fill me-1"></i> Generate Varian
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- VARIANTS TABLE -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="fw-bold text-dark mb-0"><i class="bi bi-grid-3x3-gap me-2"></i>Daftar Hasil Variasi (SKU Ter-Generate Otomatis)</h6>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddVariant">
                                <i class="bi bi-plus-lg me-1"></i> Tambah Baris Manual
                            </button>
                        </div>

                        <div class="table-responsive mb-4">
                            <table class="table table-bordered align-middle table-hover" id="variantTable">
                                <thead class="table-dark small">
                                    <tr>
                                        <th style="width: 18%;">Warna</th>
                                        <th style="width: 16%;">Ukuran (Dropdown)</th>
                                        <th style="width: 22%;">Kode SKU Varian (Otomatis)</th>
                                        <th style="width: 14%;">Harga Beli (Rp)</th>
                                        <th style="width: 14%;">Harga Jual (Rp)</th>
                                        <th style="width: 10%;">Stok Awal</th>
                                        <th style="width: 6%;" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="variantTableBody">
                                    <tr class="variant-row">
                                        <td>
                                            <input type="text" name="variants[0][color]" class="form-control form-control-sm input-color" placeholder="e.g. Hitam">
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
                                            <input type="text" name="variants[0][sku]" class="form-control form-control-sm input-variant-sku font-mono text-primary fw-bold" placeholder="Auto Generate" readonly style="background: #f1f5f9;">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="variants[0][purchase_price]" class="form-control form-control-sm input-purchase" value="0" min="0">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="variants[0][selling_price]" class="form-control form-control-sm input-selling" value="0" min="0">
                                        </td>
                                        <td>
                                            <input type="number" name="variants[0][current_stock]" class="form-control form-control-sm input-stock" value="0" min="0">
                                            <input type="hidden" name="variants[0][min_stock]" class="input-min-stock" value="5">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-variant" disabled>
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <a href="{{ route('items.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-circle me-1"></i>Simpan Produk & Variasi</button>
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
    
    const genColorsInput = document.getElementById('gen_colors');
    const btnGenerateMatrix = document.getElementById('btnGenerateMatrix');
    
    let variantIndex = 1;

    function slugify(text) {
        if (!text) return '';
        return text.toString().toLowerCase().trim()
            .replace(/\s+/g, '-')           // Replace spaces with -
            .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
            .replace(/\-\-+/g, '-')         // Replace multiple - with single -
            .toUpperCase();
    }

    function generateVariantSku(row) {
        const masterSku = masterSkuInput.value.trim();
        const colorVal = row.querySelector('.input-color').value.trim();
        const sizeVal = row.querySelector('.input-size').value.trim() || 'All Size';

        const colorSlug = colorVal ? slugify(colorVal) : 'VAR';
        const sizeSlug = slugify(sizeVal);

        const skuInput = row.querySelector('.input-variant-sku');
        if (masterSku) {
            skuInput.value = `${masterSku}-${colorSlug}-${sizeSlug}`;
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

    function createVariantRowData(color = '', size = 'All Size', purchasePrice = 0, sellingPrice = 0, stock = 0) {
        const masterSku = masterSkuInput.value.trim();
        const colorSlug = color ? slugify(color) : 'VAR';
        const sizeSlug = slugify(size || 'All Size');
        const vSku = masterSku ? `${masterSku}-${colorSlug}-${sizeSlug}` : '';

        const tr = document.createElement('tr');
        tr.className = 'variant-row';
        tr.innerHTML = `
            <td>
                <input type="text" name="variants[${variantIndex}][color]" class="form-control form-control-sm input-color" value="${color}" placeholder="e.g. Hitam">
            </td>
            <td>
                <select name="variants[${variantIndex}][size]" class="form-select form-select-sm input-size">
                    <option value="All Size" ${size === 'All Size' ? 'selected' : ''}>All Size</option>
                    <option value="S" ${size === 'S' ? 'selected' : ''}>S</option>
                    <option value="M" ${size === 'M' ? 'selected' : ''}>M</option>
                    <option value="L" ${size === 'L' ? 'selected' : ''}>L</option>
                    <option value="XL" ${size === 'XL' ? 'selected' : ''}>XL</option>
                    <option value="XXL" ${size === 'XXL' ? 'selected' : ''}>XXL</option>
                    <option value="3XL" ${size === '3XL' ? 'selected' : ''}>3XL</option>
                    <option value="Jumbo" ${size === 'Jumbo' ? 'selected' : ''}>Jumbo</option>
                    <option value="Standard" ${size === 'Standard' ? 'selected' : ''}>Standard</option>
                </select>
            </td>
            <td>
                <input type="text" name="variants[${variantIndex}][sku]" class="form-control form-control-sm input-variant-sku font-mono text-primary fw-bold" value="${vSku}" readonly style="background: #f1f5f9;">
            </td>
            <td>
                <input type="number" step="0.01" name="variants[${variantIndex}][purchase_price]" class="form-control form-control-sm input-purchase" value="${purchasePrice}" min="0">
            </td>
            <td>
                <input type="number" step="0.01" name="variants[${variantIndex}][selling_price]" class="form-control form-control-sm input-selling" value="${sellingPrice}" min="0">
            </td>
            <td>
                <input type="number" name="variants[${variantIndex}][current_stock]" class="form-control form-control-sm input-stock" value="${stock}" min="0">
                <input type="hidden" name="variants[${variantIndex}][min_stock]" class="input-min-stock" value="${masterMinStockInput.value || 5}">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-variant">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;

        variantIndex++;
        return tr;
    }

    // Auto SKU update on input changes
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

    masterSkuInput.addEventListener('input', function() {
        document.querySelectorAll('.variant-row').forEach(row => {
            generateVariantSku(row);
        });
    });

    btnAddVariant.addEventListener('click', function() {
        const row = createVariantRowData('', 'All Size', 0, 0, 0);
        variantTableBody.appendChild(row);
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

    // Preset Buttons for Generator
    document.querySelectorAll('.btn-gen-color').forEach(btn => {
        btn.addEventListener('click', function() {
            const color = this.getAttribute('data-color');
            let current = genColorsInput.value.trim();
            if (current) {
                const colors = current.split(',').map(c => c.trim());
                if (!colors.includes(color)) {
                    genColorsInput.value = current + ', ' + color;
                }
            } else {
                genColorsInput.value = color;
            }
        });
    });

    // GENERATE MATRIX BUTTON
    btnGenerateMatrix.addEventListener('click', function() {
        const rawColors = genColorsInput.value.trim();
        const colors = rawColors ? rawColors.split(',').map(c => c.trim()).filter(c => c.length > 0) : [''];
        
        // Get selected sizes
        const selectedSizes = [];
        document.querySelectorAll('.chk-gen-size:checked').forEach(chk => {
            selectedSizes.push(chk.value);
        });

        if (selectedSizes.length === 0) {
            selectedSizes.push('All Size');
        }

        const purchasePrice = parseFloat(document.getElementById('gen_purchase').value) || 0;
        const sellingPrice = parseFloat(document.getElementById('gen_selling').value) || 0;
        const stock = parseInt(document.getElementById('gen_stock').value) || 0;

        // Clear existing table body
        variantTableBody.innerHTML = '';
        variantIndex = 0;

        // Generate combinations Colors x Sizes
        colors.forEach(color => {
            selectedSizes.forEach(size => {
                const tr = createVariantRowData(color, size, purchasePrice, sellingPrice, stock);
                variantTableBody.appendChild(tr);
            });
        });

        updateRemoveVariantButtons();
    });

    // Trigger initial SKU format for first row
    const firstRow = variantTableBody.querySelector('.variant-row');
    if (firstRow) {
        generateVariantSku(firstRow);
    }
});
</script>
@endpush
