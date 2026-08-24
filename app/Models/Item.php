<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'category_id',
        'unit',
        'min_stock',
        'current_stock',
        'purchase_price',
        'selling_price',
        'description',
    ];

    protected $casts = [
        'min_stock' => 'integer',
        'current_stock' => 'integer',
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ItemVariant::class);
    }

    public function movementDetails(): HasMany
    {
        return $this->hasMany(StockMovementDetail::class);
    }

    public function isLowStock(): bool
    {
        if ($this->variants()->exists()) {
            return $this->variants()->whereColumn('current_stock', '<=', 'min_stock')->exists();
        }
        return $this->current_stock <= $this->min_stock;
    }

    public function recalculateStockAndPrices(): void
    {
        $variants = $this->variants;
        if ($variants->count() > 0) {
            $totalStock = $variants->sum('current_stock');
            $minPurchase = $variants->min('purchase_price') ?? 0;
            $minSelling = $variants->min('selling_price') ?? 0;

            $this->update([
                'current_stock' => $totalStock,
                'purchase_price' => $minPurchase,
                'selling_price' => $minSelling,
            ]);
        }
    }

    public function getSellingPriceFormattedAttribute(): string
    {
        $variants = $this->variants;
        if ($variants->count() > 1) {
            $min = $variants->min('selling_price');
            $max = $variants->max('selling_price');
            if ($min != $max) {
                return 'Rp ' . number_format($min, 0, ',', '.') . ' - Rp ' . number_format($max, 0, ',', '.');
            }
        }
        return 'Rp ' . number_format($this->selling_price, 0, ',', '.');
    }

    public function getPurchasePriceFormattedAttribute(): string
    {
        $variants = $this->variants;
        if ($variants->count() > 1) {
            $min = $variants->min('purchase_price');
            $max = $variants->max('purchase_price');
            if ($min != $max) {
                return 'Rp ' . number_format($min, 0, ',', '.') . ' - Rp ' . number_format($max, 0, ',', '.');
            }
        }
        return 'Rp ' . number_format($this->purchase_price, 0, ',', '.');
    }
}
