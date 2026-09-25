<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Inventory;
use App\Models\Maintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isAssignee()) {
            return $this->assigneeDashboard($user);
        }

        return $this->adminDashboard();
    }

    protected function adminDashboard()
    {
        $totalAssets = Inventory::sum('quantity');
        $availableStock = Inventory::sum('available_quantity');
        $assignedItems = Assignment::whereIn('status', ['issued', 'partially_returned'])->sum('quantity');
        $thisMonthCost = Inventory::whereMonth('purchase_date', now()->month)
            ->whereYear('purchase_date', now()->year)
            ->sum('total_cost');
        $totalAssetValue = Inventory::sum('total_cost');

        $lowStock = Inventory::lowStock()->with('category')->limit(10)->get();
        $recentAssignments = Assignment::with(['inventory', 'assignee'])->latest()->limit(10)->get();
        $pendingReturns = Assignment::whereIn('status', ['issued', 'partially_returned'])
            ->with(['inventory', 'assignee'])
            ->latest()
            ->limit(10)
            ->get();
        $warrantyExpiring = Inventory::warrantyExpiringSoon()->with('category')->limit(10)->get();

        $monthlyPurchases = Inventory::select(
            DB::raw("DATE_FORMAT(purchase_date, '%Y-%m') as month"),
            DB::raw('SUM(total_cost) as total')
        )
            ->whereNotNull('purchase_date')
            ->where('purchase_date', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $categoryDistribution = Inventory::select('category_id', DB::raw('COUNT(*) as total'))
            ->with('category:id,name')
            ->groupBy('category_id')
            ->get();

        return Inertia::render('Dashboard/Index', [
            'cards' => [
                'totalAssets' => $totalAssets,
                'availableStock' => $availableStock,
                'assignedItems' => $assignedItems,
                'thisMonthCost' => $thisMonthCost,
                'totalAssetValue' => $totalAssetValue,
            ],
            'widgets' => [
                'lowStock' => $lowStock,
                'recentAssignments' => $recentAssignments,
                'pendingReturns' => $pendingReturns,
                'warrantyExpiring' => $warrantyExpiring,
            ],
            'charts' => [
                'monthlyPurchases' => $monthlyPurchases,
                'categoryDistribution' => $categoryDistribution,
                'availableVsAssigned' => [
                    'available' => $availableStock,
                    'assigned' => $assignedItems,
                ],
            ],
        ]);
    }

    protected function assigneeDashboard($user)
    {
        $assignments = Assignment::where('assigned_to', $user->id)
            ->whereIn('status', ['issued', 'partially_returned'])
            ->with('inventory.category')
            ->latest()
            ->get();

        return Inertia::render('Dashboard/Index', [
            'assigneeAssignments' => $assignments,
        ]);
    }
}
