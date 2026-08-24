<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\ItemVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::with(['category', 'variants']);

        // Search by SKU, Name, Color, or Size
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('sku', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhereHas('variants', function ($vq) use ($search) {
                      $vq->where('sku', 'like', "%{$search}%")
                        ->orWhere('color', 'like', "%{$search}%")
                        ->orWhere('size', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by Category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by Low Stock
        if ($request->boolean('low_stock')) {
            $query->where(function ($q) {
                $q->whereColumn('current_stock', '<=', 'min_stock')
                  ->orWhereHas('variants', function ($vq) {
                      $vq->whereColumn('current_stock', '<=', 'min_stock');
                  });
            });
        }

        $items = $query->latest()->paginate(10)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('items.index', compact('items', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('items.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:100', 'unique:items,sku'],
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'unit' => ['required', 'string', 'max:50'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'current_stock' => ['nullable', 'integer', 'min:0'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'selling_price' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'has_variants' => ['nullable', 'boolean'],
            'variants' => ['nullable', 'array', 'min:1'],
            'variants.*.color' => ['nullable', 'string', 'max:50'],
            'variants.*.size' => ['nullable', 'string', 'max:50'],
            'variants.*.sku' => ['nullable', 'string', 'max:100'],
            'variants.*.purchase_price' => ['required_with:variants', 'numeric', 'min:0'],
            'variants.*.selling_price' => ['required_with:variants', 'numeric', 'min:0'],
            'variants.*.current_stock' => ['required_with:variants', 'integer', 'min:0'],
            'variants.*.min_stock' => ['required_with:variants', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($request, $validated) {
            $item = Item::create([
                'sku' => $validated['sku'],
                'name' => $validated['name'],
                'category_id' => $validated['category_id'],
                'unit' => $validated['unit'],
                'min_stock' => $validated['min_stock'],
                'current_stock' => $validated['current_stock'] ?? 0,
                'purchase_price' => $validated['purchase_price'] ?? 0,
                'selling_price' => $validated['selling_price'] ?? 0,
                'description' => $validated['description'] ?? null,
            ]);

            if ($request->boolean('has_variants') && !empty($request->variants)) {
                foreach ($request->variants as $index => $vData) {
                    $color = trim($vData['color'] ?? '');
                    $size = trim($vData['size'] ?? '');
                    if (empty($size)) {
                        $size = 'All Size';
                    }

                    $vSku = trim($vData['sku'] ?? '');
                    if (empty($vSku)) {
                        $colorCode = $color ? strtoupper(Str::slug($color)) : 'VAR';
                        $sizeCode = strtoupper(Str::slug($size));
                        $vSku = $item->sku . '-' . $colorCode . '-' . $sizeCode;
                        
                        // Check if SKU exists to prevent collision
                        $count = 1;
                        $baseSku = $vSku;
                        while (ItemVariant::where('sku', $vSku)->exists()) {
                            $vSku = $baseSku . '-' . $count;
                            $count++;
                        }
                    }

                    ItemVariant::create([
                        'item_id' => $item->id,
                        'sku' => $vSku,
                        'color' => $color ?: null,
                        'size' => $size,
                        'purchase_price' => $vData['purchase_price'],
                        'selling_price' => $vData['selling_price'],
                        'current_stock' => $vData['current_stock'],
                        'min_stock' => $vData['min_stock'] ?? $item->min_stock,
                    ]);
                }
                $item->recalculateStockAndPrices();
            } else {
                // Default single variant
                ItemVariant::create([
                    'item_id' => $item->id,
                    'sku' => $item->sku,
                    'color' => null,
                    'size' => 'All Size',
                    'purchase_price' => $item->purchase_price,
                    'selling_price' => $item->selling_price,
                    'current_stock' => $item->current_stock,
                    'min_stock' => $item->min_stock,
                ]);
            }
        });

        return redirect()->route('items.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function show(Item $item)
    {
        $item->load([
            'category',
            'variants',
            'movementDetails.stockMovement.user',
            'movementDetails.stockMovement.supplier',
            'movementDetails.variant',
        ]);

        return view('items.show', compact('item'));
    }

    public function edit(Item $item)
    {
        $item->load('variants');
        $categories = Category::orderBy('name')->get();
        return view('items.edit', compact('item', 'categories'));
    }

    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:100', Rule::unique('items', 'sku')->ignore($item->id)],
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'unit' => ['required', 'string', 'max:50'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'selling_price' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'has_variants' => ['nullable', 'boolean'],
            'variants' => ['nullable', 'array'],
            'variants.*.id' => ['nullable', 'exists:item_variants,id'],
            'variants.*.color' => ['nullable', 'string', 'max:50'],
            'variants.*.size' => ['nullable', 'string', 'max:50'],
            'variants.*.sku' => ['nullable', 'string', 'max:100'],
            'variants.*.purchase_price' => ['required_with:variants', 'numeric', 'min:0'],
            'variants.*.selling_price' => ['required_with:variants', 'numeric', 'min:0'],
            'variants.*.current_stock' => ['required_with:variants', 'integer', 'min:0'],
            'variants.*.min_stock' => ['required_with:variants', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($request, $validated, $item) {
            $item->update([
                'sku' => $validated['sku'],
                'name' => $validated['name'],
                'category_id' => $validated['category_id'],
                'unit' => $validated['unit'],
                'min_stock' => $validated['min_stock'],
                'description' => $validated['description'] ?? null,
            ]);

            if ($request->boolean('has_variants') && !empty($request->variants)) {
                $keptVariantIds = [];

                foreach ($request->variants as $index => $vData) {
                    $color = trim($vData['color'] ?? '');
                    $size = trim($vData['size'] ?? '');
                    if (empty($size)) {
                        $size = 'All Size';
                    }

                    $vSku = trim($vData['sku'] ?? '');
                    if (empty($vSku)) {
                        $colorCode = $color ? strtoupper(Str::slug($color)) : 'VAR';
                        $sizeCode = strtoupper(Str::slug($size));
                        $vSku = $item->sku . '-' . $colorCode . '-' . $sizeCode;

                        $count = 1;
                        $baseSku = $vSku;
                        while (ItemVariant::where('sku', $vSku)->where('id', '!=', $vData['id'] ?? 0)->exists()) {
                            $vSku = $baseSku . '-' . $count;
                            $count++;
                        }
                    }

                    if (!empty($vData['id'])) {
                        $variant = ItemVariant::where('item_id', $item->id)->find($vData['id']);
                        if ($variant) {
                            $variant->update([
                                'sku' => $vSku,
                                'color' => $color ?: null,
                                'size' => $size,
                                'purchase_price' => $vData['purchase_price'],
                                'selling_price' => $vData['selling_price'],
                                'current_stock' => $vData['current_stock'],
                                'min_stock' => $vData['min_stock'] ?? $item->min_stock,
                            ]);
                            $keptVariantIds[] = $variant->id;
                        }
                    } else {
                        $newVariant = ItemVariant::create([
                            'item_id' => $item->id,
                            'sku' => $vSku,
                            'color' => $color ?: null,
                            'size' => $size,
                            'purchase_price' => $vData['purchase_price'],
                            'selling_price' => $vData['selling_price'],
                            'current_stock' => $vData['current_stock'],
                            'min_stock' => $vData['min_stock'] ?? $item->min_stock,
                        ]);
                        $keptVariantIds[] = $newVariant->id;
                    }
                }

                ItemVariant::where('item_id', $item->id)
                    ->whereNotIn('id', $keptVariantIds)
                    ->get()
                    ->each(function ($v) {
                        if ($v->movementDetails()->count() == 0) {
                            $v->delete();
                        }
                    });

                $item->recalculateStockAndPrices();
            } else {
                $defaultVariant = $item->variants()->first();
                if ($defaultVariant) {
                    $defaultVariant->update([
                        'sku' => $item->sku,
                        'purchase_price' => $validated['purchase_price'] ?? $defaultVariant->purchase_price,
                        'selling_price' => $validated['selling_price'] ?? $defaultVariant->selling_price,
                        'min_stock' => $item->min_stock,
                    ]);
                }
                if (isset($validated['purchase_price'])) {
                    $item->purchase_price = $validated['purchase_price'];
                }
                if (isset($validated['selling_price'])) {
                    $item->selling_price = $validated['selling_price'];
                }
                $item->save();
            }
        });

        return redirect()->route('items.index')->with('success', 'Data barang berhasil diperbarui.');
    }

    public function destroy(Item $item)
    {
        if ($item->movementDetails()->count() > 0) {
            return back()->with('error', 'Barang tidak dapat dihapus karena pernah digunakan dalam transaksi stok.');
        }

        $item->delete();

        return redirect()->route('items.index')->with('success', 'Barang berhasil dihapus.');
    }
}
