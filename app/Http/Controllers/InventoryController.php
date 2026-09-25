<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInventoryRequest;
use App\Http\Requests\UpdateInventoryRequest;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Location;
use App\Services\AssetCodeService;
use App\Services\DepreciationService;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class InventoryController extends Controller
{
    public function __construct(
        protected AssetCodeService $assetCodeService,
        protected QrCodeService $qrCodeService,
        protected DepreciationService $depreciationService,
    ) {
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Inventory::class);
        $user = $request->user();

        $query = Inventory::query()->with(['category', 'location']);

        if ($user->isAssignee()) {
            // Rule: Assignee can only view products assigned to themselves.
            $query->whereHas('assignments', fn ($q) => $q->where('assigned_to', $user->id));
        }

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                    ->orWhere('asset_code', 'like', "%{$search}%")
                    ->orWhere('qr_code', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->integer('location_id'));
        }

        if ($request->filled('asset_type')) {
            $query->where('asset_type', $request->string('asset_type'));
        }

        if ($request->filled('qr_code')) {
            // QR Search: scanning resolves straight to the asset.
            $query->where('qr_code', $request->string('qr_code'));
        }

        $inventories = $query->latest()->paginate(15)->withQueryString();

        return Inertia::render('Inventory/Index', [
            'inventories' => $inventories,
            'filters' => $request->only(['search', 'category_id', 'location_id', 'asset_type', 'qr_code']),
            'categories' => Category::orderBy('name')->get(['id', 'name', 'code', 'asset_type']),
            'locations' => Location::orderBy('name')->get(['id', 'name', 'building', 'department']),
        ]);
    }

    public function create()
    {
        $this->authorize('create', Inventory::class);

        return Inertia::render('Inventory/Create', [
            'categories' => Category::orderBy('name')->get(['id', 'name', 'code', 'asset_type']),
            'locations' => Location::orderBy('name')->get(['id', 'name', 'building', 'department']),
        ]);
    }

    public function store(StoreInventoryRequest $request)
    {
        $data = $request->validated();

        $inventory = DB::transaction(function () use ($data) {
            $category = Category::findOrFail($data['category_id']);

            $inventory = new Inventory($data);
            $inventory->quantity = $data['quantity'];
            $inventory->available_quantity = $data['quantity'];
            $inventory->total_cost = $data['unit_cost'] * $data['quantity'];
            $inventory->asset_code = $this->assetCodeService->generate($category);
            $inventory->qr_code = $this->qrCodeService->generateValue();
            $inventory->save();

            $this->qrCodeService->generateImage($inventory);

            if ($inventory->asset_type === 'it_asset' && ! empty($data['it_details'])) {
                $inventory->itDetail()->create($data['it_details']);
            }

            if (! empty($data['depreciation'])) {
                $this->depreciationService->sync($inventory, $data['depreciation']);
            }

            return $inventory;
        });

        return redirect()->route('inventory.show', $inventory)
            ->with('success', "Asset {$inventory->asset_code} created successfully.");
    }

    public function show(Inventory $inventory)
    {
        $this->authorize('view', $inventory);

        $inventory->load([
            'category', 'location', 'itDetail', 'depreciation',
            'assignments' => fn ($q) => $q->with(['assignee', 'issuedBy'])->latest(),
            'stockHistories' => fn ($q) => $q->with('user')->latest(),
            'maintenanceRecords' => fn ($q) => $q->latest(),
        ]);

        return Inertia::render('Inventory/Show', [
            'inventory' => $inventory,
        ]);
    }

    public function edit(Inventory $inventory)
    {
        $this->authorize('update', $inventory);
        $inventory->load(['itDetail', 'depreciation']);

        return Inertia::render('Inventory/Edit', [
            'inventory' => $inventory,
            'categories' => Category::orderBy('name')->get(['id', 'name', 'code', 'asset_type']),
            'locations' => Location::orderBy('name')->get(['id', 'name', 'building', 'department']),
        ]);
    }

    public function update(UpdateInventoryRequest $request, Inventory $inventory)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $inventory) {
            // Quantity changes here only affect the total pool; available
            // quantity is adjusted by the same delta so outstanding
            // assignments are not silently invalidated.
            $delta = $data['quantity'] - $inventory->quantity;

            $inventory->fill($data);
            $inventory->quantity = $data['quantity'];
            $inventory->available_quantity = max(0, $inventory->available_quantity + $delta);
            $inventory->total_cost = $data['unit_cost'] * $data['quantity'];
            $inventory->save();

            if ($inventory->asset_type === 'it_asset' && ! empty($data['it_details'])) {
                $inventory->itDetail()->updateOrCreate(['inventory_id' => $inventory->id], $data['it_details']);
            }

            if (! empty($data['depreciation'])) {
                $this->depreciationService->sync($inventory, $data['depreciation']);
            }
        });

        return redirect()->route('inventory.show', $inventory)
            ->with('success', "Asset {$inventory->asset_code} updated successfully.");
    }

    public function destroy(Inventory $inventory)
    {
        $this->authorize('delete', $inventory);

        // Global rule: Delete = Soft Delete only.
        $inventory->delete();

        return redirect()->route('inventory.index')
            ->with('success', "Asset {$inventory->asset_code} moved to trash.");
    }
}
