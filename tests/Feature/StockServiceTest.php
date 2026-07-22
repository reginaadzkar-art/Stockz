<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\User;
use App\Services\StockService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockServiceTest extends TestCase
{
    use RefreshDatabase;

    protected StockService $stockService;
    protected User $user;
    protected Category $category;
    protected Item $item;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stockService = new StockService();
        $this->user = User::factory()->create(['role' => 'staff']);
        $this->category = Category::create(['name' => 'Elektronik', 'slug' => 'elektronik']);
        $this->item = Item::create([
            'sku' => 'TEST-001',
            'name' => 'Mouse Wireless',
            'category_id' => $this->category->id,
            'unit' => 'pcs',
            'min_stock' => 5,
            'current_stock' => 10,
            'purchase_price' => 50000,
            'selling_price' => 75000,
        ]);
    }

    public function test_can_record_stock_in_and_increment_item_stock(): void
    {
        $supplier = Supplier::create(['name' => 'PT Supplier Utama']);

        $movement = $this->stockService->recordStockIn([
            'date' => '2026-07-22',
            'supplier_id' => $supplier->id,
            'notes' => 'Restok mouse',
            'items' => [
                [
                    'item_id' => $this->item->id,
                    'quantity' => 15,
                    'price' => 50000,
                ]
            ]
        ], $this->user->id);

        $this->assertDatabaseHas('stock_movements', [
            'id' => $movement->id,
            'type' => 'in',
            'total_quantity' => 15,
            'total_amount' => 750000,
        ]);

        $this->item->refresh();
        $this->assertEquals(25, $this->item->current_stock);
    }

    public function test_can_record_stock_out_and_decrement_item_stock(): void
    {
        $movement = $this->stockService->recordStockOut([
            'date' => '2026-07-22',
            'recipient_or_destination' => 'Customer A',
            'notes' => 'Penjualan ritel',
            'items' => [
                [
                    'item_id' => $this->item->id,
                    'quantity' => 4,
                    'price' => 75000,
                ]
            ]
        ], $this->user->id);

        $this->assertDatabaseHas('stock_movements', [
            'id' => $movement->id,
            'type' => 'out',
            'total_quantity' => 4,
            'total_amount' => 300000,
        ]);

        $this->item->refresh();
        $this->assertEquals(6, $this->item->current_stock);
    }

    public function test_throws_exception_when_stock_out_exceeds_available_stock(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Stok barang 'Mouse Wireless' tidak mencukupi");

        $this->stockService->recordStockOut([
            'date' => '2026-07-22',
            'recipient_or_destination' => 'Customer B',
            'items' => [
                [
                    'item_id' => $this->item->id,
                    'quantity' => 50, // Available is only 10!
                    'price' => 75000,
                ]
            ]
        ], $this->user->id);
    }
}
