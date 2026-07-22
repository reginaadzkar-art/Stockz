<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_number',
        'type',
        'supplier_id',
        'recipient_or_destination',
        'date',
        'user_id',
        'total_quantity',
        'total_amount',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'total_quantity' => 'integer',
        'total_amount' => 'decimal:2',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(StockMovementDetail::class);
    }
}
