<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Equipment;
use App\Services\EquipmentService;
use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    protected EquipmentService $equipmentService;

    public function __construct(EquipmentService $equipmentService)
    {
        $this->equipmentService = $equipmentService;
    }

    public function index(Request $request)
    {
        $relations = ['category'];
        if (class_exists(\App\Models\Site::class)) {
            $relations[] = 'site';
        }
        $query = Equipment::with($relations);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%")
                    ->orWhere('asset_tag', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('condition')) {
            $query->where('condition', $request->input('condition'));
        }

        $equipments = $query->orderBy('updated_at', 'desc')->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('equipment.index', compact('equipments', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('equipment.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:150'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'brand' => ['nullable', 'string', 'max:100'],
            'model' => ['nullable', 'string', 'max:100'],
            'serial_number' => ['nullable', 'string', 'alpha_dash', 'max:100', 'unique:equipment,serial_number'],
            'asset_tag' => ['nullable', 'string', 'alpha_dash', 'max:100', 'unique:equipment,asset_tag'],
            'purchase_date' => ['nullable', 'date', 'before_or_equal:now'],
            'purchase_price' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'warranty_end_date' => ['nullable', 'date', 'after_or_equal:purchase_date'],
            'status' => ['required', 'string', 'in:Available,Broken,Retired,Lost'],
            'condition' => ['required', 'string', 'in:New,Good,Fair,Damaged'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'quantity' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'min_stock' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'is_consumable' => ['nullable', 'boolean'],
        ]);

        $validated['is_consumable'] = $request->has('is_consumable');

        // Enforce quantity semantics: non-consumables represent single assets
        if (!$validated['is_consumable']) {
            $validated['quantity'] = 1;
        } else {
            $validated['quantity'] = (int) ($validated['quantity'] ?? 0);
        }

        $equipment = Equipment::create($validated);

        return redirect()->route('equipment.show', $equipment)->with('success', 'Équipement créé avec succès.');
    }

    public function show(Equipment $equipment)
    {
        $relations = ['category', 'stockMovements.user'];
        if (class_exists(\App\Models\Site::class)) {
            $relations[] = 'site';
        }
        if (class_exists(\App\Models\EquipmentAssignment::class)) {
            $relations[] = 'assignments';
        }
        if (class_exists(\App\Models\Maintenance::class)) {
            $relations[] = 'maintenances';
        }

        $equipment->load($relations);
        return view('equipment.show', compact('equipment'));
    }

    public function edit(Equipment $equipment)
    {
        $categories = Category::orderBy('name')->get();
        return view('equipment.edit', compact('equipment', 'categories'));
    }

    public function update(Request $request, Equipment $equipment)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:150'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'brand' => ['nullable', 'string', 'max:100'],
            'model' => ['nullable', 'string', 'max:100'],
            'serial_number' => ['nullable', 'string', 'alpha_dash', 'max:100', 'unique:equipment,serial_number,' . $equipment->id],
            'asset_tag' => ['nullable', 'string', 'alpha_dash', 'max:100', 'unique:equipment,asset_tag,' . $equipment->id],
            'purchase_date' => ['nullable', 'date', 'before_or_equal:now'],
            'purchase_price' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'warranty_end_date' => ['nullable', 'date', 'after_or_equal:purchase_date'],
            'status' => ['nullable', 'string', 'in:Available,Broken,Retired,Lost'],
            'condition' => ['required', 'string', 'in:New,Good,Fair,Damaged'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'min_stock' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'is_consumable' => ['nullable', 'boolean'],
        ]);

        $validated['is_consumable'] = $request->has('is_consumable');

        $this->equipmentService->updateEquipment($equipment, $validated);

        return redirect()->route('equipment.show', $equipment)->with('success', 'Équipement mis à jour avec succès.');
    }

    public function destroy(Equipment $equipment)
    {
        $equipment->delete();
        return redirect()->route('equipment.index')->with('success', 'Équipement archivé/supprimé avec succès.');
    }
}
