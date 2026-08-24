<?php

namespace App\Services;

use App\Models\Item;
use App\Models\ItemVariant;
use App\Models\StockMovement;
use App\Models\StockMovementDetail;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockService
{
    /**
     * Record Incoming Stock (Barang Masuk)
     *
     * @param array $data ['date', 'supplier_id', 'notes', 'items' => [['item_id', 'item_variant_id', 'quantity', 'price']]]
     * @param int $userId
     * @return StockMovement
     * @throws Exception
     */
    public function recordStockIn(array $data, int $userId): StockMovement
    {
        return DB::transaction(function () use ($data, $userId) {
            $refNumber = 'TRX-IN-' . date('Ymd') . '-' . strtoupper(Str::random(4));

            $movement = StockMovement::create([
                'reference_number' => $refNumber,
                'type' => 'in',
                'supplier_id' => $data['supplier_id'] ?? null,
                'recipient_or_destination' => null,
                'date' => $data['date'],
                'user_id' => $userId,
                'notes' => $data['notes'] ?? null,
                'total_quantity' => 0,
                'total_amount' => 0,
            ]);

            $totalQty = 0;
            $totalAmount = 0;

            foreach ($data['items'] as $row) {
                $item = Item::findOrFail($row['item_id']);
                $qty = (int) $row['quantity'];
                
                if ($qty <= 0) {
                    throw new Exception("Jumlah barang {$item->name} harus lebih dari 0.");
                }

                $variantId = $row['item_variant_id'] ?? null;
                $variant = null;

                if ($variantId) {
                    $variant = ItemVariant::where('item_id', $item->id)->find($variantId);
                }

                if (!$variant && $item->variants()->exists()) {
                    $variant = $item->variants()->first();
                }

                if ($variant) {
                    $price = (float) ($row['price'] ?? $variant->purchase_price);
                    $subtotal = $qty * $price;

                    $variant->increment('current_stock', $qty);
                    $item->recalculateStockAndPrices();

                    StockMovementDetail::create([
                        'stock_movement_id' => $movement->id,
                        'item_id' => $item->id,
                        'item_variant_id' => $variant->id,
                        'quantity' => $qty,
                        'price' => $price,
                        'subtotal' => $subtotal,
                    ]);
                } else {
                    $price = (float) ($row['price'] ?? $item->purchase_price);
                    $subtotal = $qty * $price;

                    $item->increment('current_stock', $qty);

                    StockMovementDetail::create([
                        'stock_movement_id' => $movement->id,
                        'item_id' => $item->id,
                        'item_variant_id' => null,
                        'quantity' => $qty,
                        'price' => $price,
                        'subtotal' => $subtotal,
                    ]);
                }

                $totalQty += $qty;
                $totalAmount += $subtotal;
            }

            $movement->update([
                'total_quantity' => $totalQty,
                'total_amount' => $totalAmount,
            ]);

            return $movement;
        });
    }

    /**
     * Record Outgoing Stock (Barang Keluar)
     *
     * @param array $data ['date', 'recipient_or_destination', 'notes', 'items' => [['item_id', 'item_variant_id', 'quantity', 'price']]]
     * @param int $userId
     * @return StockMovement
     * @throws Exception
     */
    public function recordStockOut(array $data, int $userId): StockMovement
    {
        return DB::transaction(function () use ($data, $userId) {
            $refNumber = 'TRX-OUT-' . date('Ymd') . '-' . strtoupper(Str::random(4));

            $movement = StockMovement::create([
                'reference_number' => $refNumber,
                'type' => 'out',
                'supplier_id' => null,
                'recipient_or_destination' => $data['recipient_or_destination'] ?? null,
                'date' => $data['date'],
                'user_id' => $userId,
                'notes' => $data['notes'] ?? null,
                'total_quantity' => 0,
                'total_amount' => 0,
            ]);

            $totalQty = 0;
            $totalAmount = 0;

            foreach ($data['items'] as $row) {
                $item = Item::findOrFail($row['item_id']);
                $qty = (int) $row['quantity'];
                
                if ($qty <= 0) {
                    throw new Exception("Jumlah barang {$item->name} harus lebih dari 0.");
                }

                $variantId = $row['item_variant_id'] ?? null;
                $variant = null;

                if ($variantId) {
                    $variant = ItemVariant::where('item_id', $item->id)->find($variantId);
                }

                if (!$variant && $item->variants()->exists()) {
                    $variant = $item->variants()->first();
                }

                if ($variant) {
                    if ($variant->current_stock < $qty) {
                        throw new Exception("Stok varian '{$variant->full_name}' tidak mencukupi. Stok saat ini: {$variant->current_stock} {$item->unit}, dibutuhkan: {$qty} {$item->unit}.");
                    }

                    $price = (float) ($row['price'] ?? $variant->selling_price);
                    $subtotal = $qty * $price;

                    $variant->decrement('current_stock', $qty);
                    $item->recalculateStockAndPrices();

                    StockMovementDetail::create([
                        'stock_movement_id' => $movement->id,
                        'item_id' => $item->id,
                        'item_variant_id' => $variant->id,
                        'quantity' => $qty,
                        'price' => $price,
                        'subtotal' => $subtotal,
                    ]);
                } else {
                    if ($item->current_stock < $qty) {
                        throw new Exception("Stok barang '{$item->name}' tidak mencukupi. Stok saat ini: {$item->current_stock} {$item->unit}, dibutuhkan: {$qty} {$item->unit}.");
                    }

                    $price = (float) ($row['price'] ?? $item->selling_price);
                    $subtotal = $qty * $price;

                    $item->decrement('current_stock', $qty);

                    StockMovementDetail::create([
                        'stock_movement_id' => $movement->id,
                        'item_id' => $item->id,
                        'item_variant_id' => null,
                        'quantity' => $qty,
                        'price' => $price,
                        'subtotal' => $subtotal,
                    ]);
                }

                $totalQty += $qty;
                $totalAmount += $subtotal;
            }

            $movement->update([
                'total_quantity' => $totalQty,
                'total_amount' => $totalAmount,
            ]);

            return $movement;
        });
    }
}
