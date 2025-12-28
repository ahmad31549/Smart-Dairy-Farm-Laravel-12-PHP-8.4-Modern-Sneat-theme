<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $totalItems = Inventory::count();
        $lowStockItems = Inventory::whereRaw('quantity <= reorder_level')->count();
        $totalValue = Inventory::selectRaw('SUM(quantity * unit_price) as total')->first()->total ?? 0;
        $expiringSoon = Inventory::whereBetween('expiry_date', [now(), now()->addDays(30)])->count();

        return view('inventory.index', compact(
            'totalItems',
            'lowStockItems',
            'totalValue',
            'expiringSoon'
        ));
    }

    // Feed & Supplies Sub-menu
    public function feedSupplies()
    {
        $totalItems = Inventory::feedSupplies()->count();
        $inStock = Inventory::feedSupplies()->where('quantity', '>', 0)->whereRaw('quantity > reorder_level')->count();
        $lowStock = Inventory::feedSupplies()->lowStock()->count();
        $totalValue = Inventory::feedSupplies()->selectRaw('SUM(quantity * unit_price) as total')->first()->total ?? 0;
        $expiringSoon = Inventory::feedSupplies()->expiringSoon()->count();

        return view('inventory.feed-supplies', compact(
            'totalItems',
            'inStock',
            'lowStock',
            'totalValue',
            'expiringSoon'
        ));
    }

    // Medical Supplies Sub-menu
    public function medicalSupplies()
    {
        $totalItems = Inventory::medicalSupplies()->count();
        $inStock = Inventory::medicalSupplies()->where('quantity', '>', 0)->whereRaw('quantity > reorder_level')->count();
        $lowStock = Inventory::medicalSupplies()->lowStock()->count();
        $totalValue = Inventory::medicalSupplies()->selectRaw('SUM(quantity * unit_price) as total')->first()->total ?? 0;
        $expiringSoon = Inventory::medicalSupplies()->expiringSoon()->count();

        return view('inventory.medical-supplies', compact(
            'totalItems',
            'inStock',
            'lowStock',
            'totalValue',
            'expiringSoon'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_name' => 'required|string',
            'category' => 'required|string',
            'inventory_type' => 'required|in:feed_supplies,medical_supplies',
            'batch_number' => 'nullable|string',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string',
            'unit_price' => 'required|numeric|min:0',
            'reorder_level' => 'required|numeric|min:0',
            'supplier' => 'nullable|string',
            'manufacturer' => 'nullable|string',
            'last_restocked' => 'nullable|date',
            'expiry_date' => 'nullable|date',
            'notes' => 'nullable|string'
        ]);

        $item = Inventory::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Inventory item saved successfully',
            'item' => $item
        ]);
    }

    public function getInventory()
    {
        $inventory = Inventory::latest()->get();
        return response()->json($inventory);
    }

    // Get all items with specific inventory type
    public function getAll(Request $request)
    {
        $query = Inventory::query();

        // Filter by inventory type if provided
        if ($request->has('type') && in_array($request->type, ['feed_supplies', 'medical_supplies'])) {
            $query->where('inventory_type', $request->type);
        }

        $items = $query->latest()->get();
        return response()->json($items);
    }

    public function show($id)
    {
        $item = Inventory::findOrFail($id);
        return response()->json($item);
    }

    public function update(Request $request, $id)
    {
        $item = Inventory::findOrFail($id);

        $request->validate([
            'item_name' => 'required|string',
            'category' => 'required|string',
            'inventory_type' => 'required|in:feed_supplies,medical_supplies',
            'batch_number' => 'nullable|string',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string',
            'unit_price' => 'required|numeric|min:0',
            'reorder_level' => 'required|numeric|min:0',
            'supplier' => 'nullable|string',
            'manufacturer' => 'nullable|string',
            'last_restocked' => 'nullable|date',
            'expiry_date' => 'nullable|date',
            'notes' => 'nullable|string'
        ]);

        $item->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Inventory item updated successfully',
            'item' => $item
        ]);
    }

    public function destroy($id)
    {
        $item = Inventory::findOrFail($id);
        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Inventory item deleted successfully'
        ]);
    }

    protected function filteredQuery(Request $request)
    {
        $query = Inventory::query();

        if ($request->filled('category')) {
            $query->where('category', $request->string('category'));
        }

        if ($request->filled('type')) {
            $query->where('inventory_type', $request->string('type'));
        }

        if ($request->filled('status')) {
            $status = $request->string('status');
            if ($status === 'in_stock') {
                $query->where('quantity', '>', 0)->whereRaw('quantity > reorder_level');
            } elseif ($status === 'low_stock') {
                $query->where('quantity', '>', 0)->whereRaw('quantity <= reorder_level');
            } elseif ($status === 'out_of_stock') {
                $query->where('quantity', '<=', 0);
            }
        }

        if ($request->filled('search')) {
            $term = strtolower($request->string('search'));
            $query->where(function($q) use ($term) {
                $q->whereRaw('LOWER(item_name) LIKE ?', ['%' . $term . '%'])
                  ->orWhereRaw('LOWER(batch_number) LIKE ?', ['%' . $term . '%'])
                  ->orWhereRaw('LOWER(supplier) LIKE ?', ['%' . $term . '%']);
            });
        }

        return $query->latest();
    }

    public function export(Request $request)
    {
        ini_set('memory_limit', '512M');
        $items = $this->filteredQuery($request)->get();
        $html = view('inventory.print', compact('items'))->render();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->setPaper('a4', 'landscape');
        return $pdf->download('inventory_report.pdf');
    }

    public function print(Request $request)
    {
        $items = $this->filteredQuery($request)->get();
        return view('inventory.print', compact('items'));
    }
}
