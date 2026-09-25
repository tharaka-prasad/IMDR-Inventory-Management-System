<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMaintenanceRequest;
use App\Models\AuditLog;
use App\Models\Inventory;
use App\Models\Maintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Maintenance::class);

        $records = Maintenance::with('inventory:id,asset_code,item_name')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Maintenance/Index', [
            'records' => $records,
            'filters' => $request->only('status'),
        ]);
    }

    public function create()
    {
        $this->authorize('create', Maintenance::class);

        return Inertia::render('Maintenance/Create', [
            'inventories' => Inventory::orderBy('item_name')->get(['id', 'asset_code', 'item_name']),
        ]);
    }

    public function store(StoreMaintenanceRequest $request)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
            $maintenance = Maintenance::create($data);

            // Put the asset into "in_maintenance" while it's sent out.
            if (in_array($data['status'], ['pending', 'in_progress'])) {
                Inventory::where('id', $data['inventory_id'])->update(['status' => 'in_maintenance']);
            }

            AuditLog::write($maintenance, 'maintenance_added', null, $maintenance->getAttributes());
        });

        return redirect()->route('maintenance.index')->with('success', 'Maintenance record created.');
    }

    public function edit(Maintenance $maintenance)
    {
        $this->authorize('update', $maintenance);
        $maintenance->load('inventory:id,asset_code,item_name');

        return Inertia::render('Maintenance/Edit', [
            'maintenance' => $maintenance,
            'inventories' => Inventory::orderBy('item_name')->get(['id', 'asset_code', 'item_name']),
        ]);
    }

    public function update(StoreMaintenanceRequest $request, Maintenance $maintenance)
    {
        $data = $request->validated();
        $old = $maintenance->getAttributes();

        DB::transaction(function () use ($data, $maintenance) {
            $maintenance->update($data);

            if ($data['status'] === 'completed') {
                Inventory::where('id', $maintenance->inventory_id)->update(['status' => 'active']);
            } elseif ($data['status'] === 'unrepairable') {
                Inventory::where('id', $maintenance->inventory_id)->update(['status' => 'disposed', 'condition' => 'disposed']);
            }
        });

        AuditLog::write($maintenance, 'maintenance_updated', $old, $maintenance->getChanges());

        return redirect()->route('maintenance.index')->with('success', 'Maintenance record updated.');
    }

    public function destroy(Maintenance $maintenance)
    {
        $this->authorize('delete', $maintenance);
        $maintenance->delete();

        return redirect()->route('maintenance.index')->with('success', 'Maintenance record removed.');
    }
}
