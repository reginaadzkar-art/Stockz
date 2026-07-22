<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Services\StockService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockMovementController extends Controller
{
    protected StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function index(Request $request)
    {
        $query = StockMovement::with(['user', 'supplier', 'details.item']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhere('recipient_or_destination', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $movements = $query->latest()->paginate(10)->withQueryString();

        return view('stock_movements.index', compact('movements'));
    }

    public function show(StockMovement $stockMovement)
    {
        $stockMovement->load(['user', 'supplier', 'details.item.category']);
        return view('stock_movements.show', compact('stockMovement'));
    }

    public function createIn()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $items = Item::with('category')->orderBy('name')->get();

        return view('stock_movements.create_in', compact('suppliers', 'items'));
    }

    public function storeIn(Request $request)
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'exists:items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
        ]);

        try {
            $movement = $this->stockService->recordStockIn($validated, Auth::id());
            return redirect()->route('stock-movements.show', $movement)
                ->with('success', "Pencatatan Barang Masuk ({$movement->reference_number}) berhasil disimpan.");
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function createOut()
    {
        $items = Item::with('category')->where('current_stock', '>', 0)->orderBy('name')->get();

        return view('stock_movements.create_out', compact('items'));
    }

    public function storeOut(Request $request)
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'recipient_or_destination' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'exists:items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
        ]);

        try {
            $movement = $this->stockService->recordStockOut($validated, Auth::id());
            return redirect()->route('stock-movements.show', $movement)
                ->with('success', "Pencatatan Barang Keluar ({$movement->reference_number}) berhasil disimpan.");
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
