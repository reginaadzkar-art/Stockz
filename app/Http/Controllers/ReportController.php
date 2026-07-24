<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function stockReport(Request $request)
    {
        $query = Item::with('category');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->boolean('low_stock')) {
            $query->whereColumn('current_stock', '<=', 'min_stock');
        }

        $items = $query->orderBy('name')->paginate(20)->withQueryString();
        $categories = Category::orderBy('name')->get();

        $totalValuationPurchase = Item::selectRaw('SUM(current_stock * purchase_price) as total')->value('total') ?? 0;
        $totalValuationSelling = Item::selectRaw('SUM(current_stock * selling_price) as total')->value('total') ?? 0;
        $lowStockCount = Item::whereColumn('current_stock', '<=', 'min_stock')->count();

        return view('reports.stock', compact('items', 'categories', 'totalValuationPurchase', 'totalValuationSelling', 'lowStockCount'));
    }

    public function exportStockCsv(Request $request): StreamedResponse
    {
        $query = Item::with('category');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->boolean('low_stock')) {
            $query->whereColumn('current_stock', '<=', 'min_stock');
        }

        $items = $query->orderBy('name')->get();

        $filename = 'Laporan_Stok_Barang_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($items) {
            $file = fopen('php://output', 'w');
            // Add UTF-8 BOM for Excel compatibility
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, ['No', 'Kode SKU', 'Nama Barang', 'Kategori', 'Stok Saat Ini', 'Min. Stok', 'Satuan', 'Harga Beli (Rp)', 'Harga Jual (Rp)', 'Status Stok']);

            foreach ($items as $index => $item) {
                fputcsv($file, [
                    $index + 1,
                    $item->sku,
                    $item->name,
                    $item->category->name ?? '-',
                    $item->current_stock,
                    $item->min_stock,
                    $item->unit,
                    $item->purchase_price,
                    $item->selling_price,
                    $item->isLowStock() ? 'Stok Menipis' : 'Aman',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function transactionReport(Request $request)
    {
        $query = StockMovement::with(['user', 'supplier', 'details.item']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        // Clone query for stats calculations
        $statsQuery = clone $query;

        $movements = $query->latest('date')->latest('id')->paginate(20)->withQueryString();

        $totalIncome = (clone $statsQuery)->where('type', 'in')->sum('total_amount');
        $totalExpense = (clone $statsQuery)->where('type', 'out')->sum('total_amount');
        $totalInQty = (clone $statsQuery)->where('type', 'in')->sum('total_quantity');
        $totalOutQty = (clone $statsQuery)->where('type', 'out')->sum('total_quantity');

        return view('reports.transactions', compact('movements', 'totalIncome', 'totalExpense', 'totalInQty', 'totalOutQty'));
    }

    public function exportTransactionsCsv(Request $request): StreamedResponse
    {
        $query = StockMovement::with(['user', 'supplier', 'details.item']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $movements = $query->latest('date')->latest('id')->get();

        $filename = 'Histori_Transaksi_Stok_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($movements) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, ['No', 'No. Referensi', 'Tanggal', 'Tipe Transaksi', 'Supplier / Penerima', 'Total Qty', 'Total Nilai (Rp)', 'Petugas Input', 'Catatan']);

            foreach ($movements as $index => $movement) {
                fputcsv($file, [
                    $index + 1,
                    $movement->reference_number,
                    $movement->date->format('Y-m-d'),
                    $movement->type === 'in' ? 'Barang Masuk' : 'Barang Keluar',
                    $movement->type === 'in' ? ($movement->supplier->name ?? '-') : ($movement->recipient_or_destination ?? '-'),
                    $movement->total_quantity,
                    $movement->total_amount,
                    $movement->user->name ?? '-',
                    $movement->notes ?? '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
