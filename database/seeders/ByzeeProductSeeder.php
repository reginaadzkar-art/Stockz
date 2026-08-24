<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use App\Models\ItemVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ByzeeProductSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Categories
        $catKhimar = Category::firstOrCreate(['name' => 'Khimar'], ['description' => 'Koleksi Khimar & Khiban Premium Byzee']);
        $catBergo = Category::firstOrCreate(['name' => 'Bergo'], ['description' => 'Koleksi Bergo Daily & Instant Byzee']);
        $catPashmina = Category::firstOrCreate(['name' => 'Pashmina'], ['description' => 'Koleksi Pashmina Tencel & Viscose Byzee']);

        // Data Produk, Warna & Ukuran
        $products = [
            [
                'name' => 'Khiban Tencel',
                'sku' => 'BYZ-KHB-TNC',
                'category_id' => $catKhimar->id,
                'unit' => 'pcs',
                'purchase_price' => 50000,
                'selling_price' => 89000,
                'min_stock' => 5,
                'description' => 'Khiban Tencel khas Byzee dengan bahan serat alam Tencel yang adem, jatuh, dan mewah.',
                'colors' => [
                    'Black',
                    'Coffee',
                    'Natural',
                    'Taupe',
                    'Sand',
                    'Dusty pink',
                    'Dark Maroon',
                ],
                'sizes' => ['All Size'],
            ],
            [
                'name' => 'Khiban Ceruty',
                'sku' => 'BYZ-KHB-CRT',
                'category_id' => $catKhimar->id,
                'unit' => 'pcs',
                'purchase_price' => 45000,
                'selling_price' => 79000,
                'min_stock' => 5,
                'description' => 'Khiban Ceruty Babydoll premium Byzee dengan tekstur lembut, ringan, dan tidak mudah kusut.',
                'colors' => [
                    'Black',
                    'Nude pink',
                    'Light grey',
                    'Dark grey',
                    'Oatmeal',
                ],
                'sizes' => ['All Size'],
            ],
            [
                'name' => 'Bergo Malay',
                'sku' => 'BYZ-BRG-MLY',
                'category_id' => $catBergo->id,
                'unit' => 'pcs',
                'purchase_price' => 35000,
                'selling_price' => 65000,
                'min_stock' => 5,
                'description' => 'Bergo Malay gaya khas Melayu yang praktis, menutup dada dengan sempurna untuk harian.',
                'colors' => [
                    'Black',
                    'Navy',
                    'Natural',
                    'Light grey',
                    'Khaky',
                ],
                'sizes' => ['S', 'M'],
            ],
            [
                'name' => 'Pashmina Tencel',
                'sku' => 'BYZ-PSH-TNC',
                'category_id' => $catPashmina->id,
                'unit' => 'pcs',
                'purchase_price' => 42000,
                'selling_price' => 75000,
                'min_stock' => 5,
                'description' => 'Pashmina Tencel silk dengan kilau subtle, sangat lembut di kulit dan mudah dibentuk.',
                'colors' => [
                    'Bisquit',
                    'Frapucino',
                    'Mahogany',
                    'Navy',
                    'Black',
                ],
                'sizes' => ['All Size'],
            ],
            [
                'name' => 'Pashmina Viscose',
                'sku' => 'BYZ-PSH-VSC',
                'category_id' => $catPashmina->id,
                'unit' => 'pcs',
                'purchase_price' => 38000,
                'selling_price' => 69000,
                'min_stock' => 5,
                'description' => 'Pashmina Viscose premium yang adem, breathable, dan nyaman digunakan sepanjang hari.',
                'colors' => [
                    'Dark grey',
                    'Ekspreco',
                    'Dusty pink',
                    'Navy',
                    'Black',
                    'Bisquit',
                ],
                'sizes' => ['All Size'],
            ],
        ];

        foreach ($products as $pData) {
            $colors = $pData['colors'];
            $sizes = $pData['sizes'] ?? ['All Size'];
            unset($pData['colors'], $pData['sizes']);

            $item = Item::updateOrCreate(
                ['sku' => $pData['sku']],
                $pData
            );

            // Clean up old 'All Size' variants if item now uses specific sizes (e.g. S & M)
            if ($sizes !== ['All Size']) {
                ItemVariant::where('item_id', $item->id)->where('size', 'All Size')->delete();
            }

            foreach ($colors as $color) {
                foreach ($sizes as $size) {
                    $colorCode = strtoupper(Str::slug($color));
                    $sizeCode = strtoupper(Str::slug($size));
                    $variantSku = ($size === 'All Size')
                        ? $item->sku . '-' . $colorCode
                        : $item->sku . '-' . $colorCode . '-' . $sizeCode;

                    ItemVariant::updateOrCreate(
                        [
                            'item_id' => $item->id,
                            'color' => $color,
                            'size' => $size,
                        ],
                        [
                            'sku' => $variantSku,
                            'purchase_price' => $item->purchase_price,
                            'selling_price' => $item->selling_price,
                            'current_stock' => rand(15, 40),
                            'min_stock' => $item->min_stock,
                        ]
                    );
                }
            }

            $item->recalculateStockAndPrices();
        }
    }
}
