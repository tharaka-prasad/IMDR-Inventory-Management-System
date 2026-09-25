<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLocationRequest;
use App\Models\Location;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Location::class);

        $locations = Location::withCount('inventories')
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%'.$request->string('search').'%'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Settings/Locations/Index', [
            'locations' => $locations,
            'filters' => $request->only('search'),
        ]);
    }

    public function store(StoreLocationRequest $request)
    {
        Location::create($request->validated());

        return back()->with('success', 'Location created.');
    }

    public function update(StoreLocationRequest $request, Location $location)
    {
        $location->update($request->validated());

        return back()->with('success', 'Location updated.');
    }

    public function destroy(Location $location)
    {
        $this->authorize('delete', $location);

        if ($location->inventories()->exists()) {
            return back()->with('error', 'Cannot delete a location that still has assets.');
        }

        $location->delete();

        return back()->with('success', 'Location removed.');
    }
}
