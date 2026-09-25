<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIssueRequest;
use App\Http\Requests\StoreTransferRequest;
use App\Models\Assignment;
use App\Models\Inventory;
use App\Models\User;
use App\Services\StockService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IssueController extends Controller
{
    public function __construct(protected StockService $stockService)
    {
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Assignment::class);

        $assignments = Assignment::with(['inventory.category', 'assignee', 'issuedBy'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->string('search');
                $q->whereHas('inventory', fn ($iq) => $iq->where('item_name', 'like', "%{$search}%")
                    ->orWhere('asset_code', 'like', "%{$search}%"))
                    ->orWhereHas('assignee', fn ($uq) => $uq->where('full_name', 'like', "%{$search}%"));
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Issue/Index', [
            'assignments' => $assignments,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create()
    {
        $this->authorize('create', Assignment::class);

        return Inertia::render('Issue/Create', [
            'products' => Inventory::where('status', 'active')
                ->where('available_quantity', '>', 0)
                ->get(['id', 'asset_code', 'item_name', 'available_quantity', 'asset_type']),
            'users' => User::where('status', 'active')->orderBy('full_name')->get(['id', 'full_name', 'role']),
        ]);
    }

    public function store(StoreIssueRequest $request)
    {
        $data = $request->validated();
        $inventory = Inventory::findOrFail($data['inventory_id']);

        $assignment = $this->stockService->issue(
            $inventory,
            $data['assigned_to'],
            $data['quantity'],
            $data['issue_date'],
            $data['remarks'] ?? null,
        );

        return redirect()->route('issue.index')
            ->with('success', "Issued {$assignment->quantity} x {$inventory->item_name} successfully.");
    }

    /**
     * Transfer an already-issued asset to another user, preserving history.
     */
    public function transfer(StoreTransferRequest $request)
    {
        $data = $request->validated();
        $fromAssignment = Assignment::findOrFail($data['from_assignment_id']);

        $this->stockService->transfer(
            $fromAssignment,
            $data['to_user_id'],
            $data['quantity'],
            $data['transfer_date'],
            $data['remarks'] ?? null,
        );

        return redirect()->route('issue.index')->with('success', 'Asset transferred successfully.');
    }
}
