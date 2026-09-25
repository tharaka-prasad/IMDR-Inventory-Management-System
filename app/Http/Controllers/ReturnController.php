<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReturnRequest;
use App\Models\Assignment;
use App\Models\ReturnProduct;
use App\Services\StockService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReturnController extends Controller
{
    public function __construct(protected StockService $stockService)
    {
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Assignment::class);

        $returns = ReturnProduct::with(['assignment.inventory', 'assignment.assignee', 'receivedBy'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Return/Index', [
            'returns' => $returns,
        ]);
    }

    public function create()
    {
        $this->authorize('return', Assignment::class);

        return Inertia::render('Return/Create', [
            'assignments' => Assignment::whereIn('status', ['issued', 'partially_returned'])
                ->with(['inventory:id,asset_code,item_name', 'assignee:id,full_name'])
                ->get()
                ->map(fn ($a) => [
                    'id' => $a->id,
                    'inventory' => $a->inventory,
                    'assignee' => $a->assignee,
                    'outstanding_quantity' => $a->outstandingQuantity(),
                ]),
        ]);
    }

    public function store(StoreReturnRequest $request)
    {
        $data = $request->validated();
        $assignment = Assignment::findOrFail($data['assignment_id']);

        $this->stockService->returnStock(
            $assignment,
            $data['quantity'],
            $data['return_date'],
            $data['condition'],
            $data['remarks'] ?? null,
        );

        return redirect()->route('return.index')->with('success', 'Return recorded successfully.');
    }
}
