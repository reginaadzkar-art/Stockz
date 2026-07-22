<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockMovement;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalItems = Item::count();
        $lowStockItems = Item::whereColumn('current_stock', '<=', 'min_stock')->get();
        $lowStockCount = $lowStockItems->count();

        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $totalInMonth = StockMovement::where('type', 'in')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('total_quantity');

        $totalOutMonth = StockMovement::where('type', 'out')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('total_quantity');

        $recentMovements = StockMovement::with(['user', 'supplier'])
            ->latest('id')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalItems',
            'lowStockCount',
            'lowStockItems',
            'totalInMonth',
            'totalOutMonth',
            'recentMovements'
        ));
    }
}
