<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@stockz.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        $staff = User::firstOrCreate(
            ['email' => 'staff@stockz.com'],
            [
                'name' => 'Staff Gudang',
                'password' => Hash::make('password'),
                'role' => 'staff',
            ]
        );

        $owner = User::firstOrCreate(
            ['email' => 'owner@stockz.com'],
            [
                'name' => 'Owner Stockz',
                'password' => Hash::make('password'),
                'role' => 'owner',
            ]
        );

        // 2. Seed Categories
        $catElektronik = Category::firstOrCreate(['slug' => 'elektronik'], [
            'name' => 'Elektronik',
            'description' => 'Perangkat dan komponen elektronik',
        ]);

        $catPakaian = Category::firstOrCreate(['slug' => 'pakaian'], [
            'name' => 'Pakaian & Tekstil',
            'description' => 'Pakaian jadi, kain, dan perlengkapan tekstil',
        ]);

        $catMakanan = Category::firstOrCreate(['slug' => 'makanan-minuman'], [
            'name' => 'Makanan & Minuman',
            'description' => 'Bahan makanan dan produk konsumsi',
        ]);

        // 3. Seed Suppliers
        $sup1 = Supplier::firstOrCreate(['name' => 'PT Jaya Sentosa Tech'], [
            'phone' => '081234567890',
            'email' => 'info@jayasentosa.co.id',
            'address' => 'Jl. Industri No. 45, Jakarta',
            'notes' => 'Supplier resmi perlengkapan IT',
        ]);

        $sup2 = Supplier::firstOrCreate(['name' => 'CV Sumber Makmur'], [
            'phone' => '089876543210',
            'email' => 'sales@sumbermakmur.com',
            'address' => 'Jl. Raya Garut No. 12, Bandung',
            'notes' => 'Supplier bahan pangan & tekstil',
        ]);

        // 4. Seed Items
        Item::firstOrCreate(['sku' => 'ELK-001'], [
            'name' => 'Monitor LED 24 Inch',
            'category_id' => $catElektronik->id,
            'unit' => 'unit',
            'min_stock' => 5,
            'current_stock' => 15,
            'purchase_price' => 1500000,
            'selling_price' => 1850000,
            'description' => 'Monitor IPS Full HD 75Hz',
        ]);

        Item::firstOrCreate(['sku' => 'ELK-002'], [
            'name' => 'Keyboard Mechanical RGB',
            'category_id' => $catElektronik->id,
            'unit' => 'pcs',
            'min_stock' => 10,
            'current_stock' => 3, // Low stock example!
            'purchase_price' => 350000,
            'selling_price' => 450000,
            'description' => 'Switch Blue Mechanical Keyboard',
        ]);

        Item::firstOrCreate(['sku' => 'MKN-001'], [
            'name' => 'Kopi Arabika Premium 1kg',
            'category_id' => $catMakanan->id,
            'unit' => 'pack',
            'min_stock' => 10,
            'current_stock' => 25,
            'purchase_price' => 120000,
            'selling_price' => 160000,
            'description' => 'Biji kopi sangrai 100% Arabika',
        ]);

        Item::firstOrCreate(['sku' => 'TEX-001'], [
            'name' => 'Kaos Polos Cotton Combed 30s',
            'category_id' => $catPakaian->id,
            'unit' => 'pcs',
            'min_stock' => 20,
            'current_stock' => 50,
            'purchase_price' => 35000,
            'selling_price' => 55000,
            'description' => 'Bahan adem dan menyerap keringat',
        ]);
    }
}
