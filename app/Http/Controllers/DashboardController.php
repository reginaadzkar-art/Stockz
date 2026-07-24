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
        $stockHealthPercentage = $totalItems > 0 ? round((($totalItems - $lowStockCount) / $totalItems) * 100) : 100;

        $totalValuationPurchase = Item::selectRaw('SUM(current_stock * purchase_price) as total')->value('total') ?? 0;
        $totalValuationSelling = Item::selectRaw('SUM(current_stock * selling_price) as total')->value('total') ?? 0;

        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $totalInMonth = StockMovement::where('type', 'in')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('total_quantity');

        $totalOutMonth = StockMovement::where('type', 'out')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('total_quantity');

        $totalIncomeMonth = StockMovement::where('type', 'in')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('total_amount');

        $totalExpenseMonth = StockMovement::where('type', 'out')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('total_amount');

        $recentMovements = StockMovement::with(['user', 'supplier'])
            ->latest('id')
            ->take(5)
            ->get();

        // 7-day trends for ApexCharts
        $trendDates = [];
        $trendIn = [];
        $trendOut = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $displayDate = Carbon::now()->subDays($i)->format('d M');
            $trendDates[] = $displayDate;

            $inQty = StockMovement::where('type', 'in')->whereDate('date', $date)->sum('total_quantity');
            $outQty = StockMovement::where('type', 'out')->whereDate('date', $date)->sum('total_quantity');

            $trendIn[] = (int) $inQty;
            $trendOut[] = (int) $outQty;
        }

        return view('dashboard', compact(
            'totalItems',
            'lowStockCount',
            'lowStockItems',
            'stockHealthPercentage',
            'totalValuationPurchase',
            'totalValuationSelling',
            'totalInMonth',
            'totalOutMonth',
            'totalIncomeMonth',
            'totalExpenseMonth',
            'recentMovements',
            'trendDates',
            'trendIn',
            'trendOut'
        ));
    }
}
