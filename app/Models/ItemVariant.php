<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ItemVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'sku',
        'color',
        'size',
        'purchase_price',
        'selling_price',
        'current_stock',
        'min_stock',
    ];

    protected $casts = [
        'current_stock' => 'integer',
        'min_stock' => 'integer',
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function movementDetails(): HasMany
    {
        return $this->hasMany(StockMovementDetail::class);
    }

    public function getVariantLabelAttribute(): string
    {
        $parts = array_filter([$this->color, $this->size]);
        return count($parts) > 0 ? implode(' / ', $parts) : 'Default';
    }

    public function getFullNameAttribute(): string
    {
        $itemName = $this->item ? $this->item->name : 'Barang';
        $label = $this->variant_label;
        return "{$itemName} [{$label}]";
    }

    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->min_stock;
    }
}
