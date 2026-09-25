<?php

namespace App\Http\Controllers;

use App\Exports\GenericExport;
use App\Models\Assignment;
use App\Models\Inventory;
use App\Models\Maintenance;
use App\Models\ReturnProduct;
use App\Models\SystemSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    protected array $reportTitles = [
        'inventory' => 'Inventory Report',
        'monthly-purchases' => 'Monthly Purchases Report',
        'asset-value' => 'Asset Value Report',
        'assignment-history' => 'Assignment History Report',
        'return-history' => 'Return History Report',
        'low-stock' => 'Low Stock Report',
        'maintenance' => 'Maintenance Cost Report',
        'disposal' => 'Disposal Report',
    ];

    public function index()
    {
        $this->authorize('viewAny', Inventory::class);

        return Inertia::render('Reports/Index', [
            'reportTypes' => $this->reportTitles,
        ]);
    }

    public function show(Request $request, string $type)
    {
        if (! array_key_exists($type, $this->reportTitles)) {
            abort(404);
        }

        [$headings, $rows] = $this->build($type, $request);
        $format = $request->string('format')->toString();

        if ($format === 'excel') {
            return Excel::download(
                new GenericExport($rows, $headings, $this->reportTitles[$type]),
                str($type)->slug().'-'.now()->format('Ymd_His').'.xlsx'
            );
        }

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('reports.pdf', [
                'title' => $this->reportTitles[$type],
                'headings' => $headings,
                'rows' => $rows,
                'instituteName' => SystemSetting::current()->institute_name,
                'dateRange' => $this->dateRangeLabel($request),
            ])->setPaper('a4', 'landscape');

            return $pdf->download(str($type)->slug().'-'.now()->format('Ymd_His').'.pdf');
        }

        return Inertia::render('Reports/Show', [
            'type' => $type,
            'title' => $this->reportTitles[$type],
            'headings' => $headings,
            'rows' => $rows,
            'filters' => $request->only(['from', 'to']),
        ]);
    }

    protected function dateRangeLabel(Request $request): ?string
    {
        if ($request->filled('from') || $request->filled('to')) {
            return ($request->get('from') ?: '...').' to '.($request->get('to') ?: '...');
        }

        return null;
    }

    protected function dateFilter($query, Request $request, string $column)
    {
        if ($request->filled('from')) {
            $query->whereDate($column, '>=', $request->get('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate($column, '<=', $request->get('to'));
        }

        return $query;
    }

    protected function build(string $type, Request $request): array
    {
        return match ($type) {
            'inventory' => $this->inventoryReport($request),
            'monthly-purchases' => $this->monthlyPurchasesReport($request),
            'asset-value' => $this->assetValueReport($request),
            'assignment-history' => $this->assignmentHistoryReport($request),
            'return-history' => $this->returnHistoryReport($request),
            'low-stock' => $this->lowStockReport(),
            'maintenance' => $this->maintenanceReport($request),
            'disposal' => $this->disposalReport($request),
        };
    }

    protected function inventoryReport(Request $request): array
    {
        $query = Inventory::with(['category', 'location']);
        $this->dateFilter($query, $request, 'purchase_date');

        $rows = $query->get()->map(fn ($i) => [
            $i->asset_code, $i->item_name, $i->category?->name, $i->location?->name,
            $i->quantity, $i->available_quantity, number_format((float) $i->unit_cost, 2),
            number_format((float) $i->total_cost, 2), $i->condition, $i->status,
        ])->toArray();

        return [['Asset Code', 'Item', 'Category', 'Location', 'Qty', 'Available', 'Unit Cost', 'Total Cost', 'Condition', 'Status'], $rows];
    }

    protected function monthlyPurchasesReport(Request $request): array
    {
        $query = Inventory::select(
            DB::raw("DATE_FORMAT(purchase_date, '%Y-%m') as month"),
            DB::raw('COUNT(*) as items'),
            DB::raw('SUM(total_cost) as total')
        )->whereNotNull('purchase_date');
        $this->dateFilter($query, $request, 'purchase_date');

        $rows = $query->groupBy('month')->orderBy('month')->get()
            ->map(fn ($r) => [$r->month, $r->items, number_format((float) $r->total, 2)])->toArray();

        return [['Month', 'Items Purchased', 'Total Cost'], $rows];
    }

    protected function assetValueReport(Request $request): array
    {
        $rows = Inventory::with('depreciation')->get()->map(fn ($i) => [
            $i->asset_code, $i->item_name, number_format((float) $i->total_cost, 2),
            number_format((float) ($i->depreciation->accumulated_depreciation ?? 0), 2),
            number_format((float) ($i->depreciation->net_book_value ?? $i->total_cost), 2),
        ])->toArray();

        return [['Asset Code', 'Item', 'Total Cost', 'Accumulated Depreciation', 'Net Book Value'], $rows];
    }

    protected function assignmentHistoryReport(Request $request): array
    {
        $query = Assignment::with(['inventory', 'assignee', 'issuedBy']);
        $this->dateFilter($query, $request, 'issue_date');

        $rows = $query->latest()->get()->map(fn ($a) => [
            $a->inventory?->asset_code, $a->inventory?->item_name, $a->assignee?->full_name,
            $a->quantity, $a->issue_date?->format('Y-m-d'), $a->issuedBy?->full_name, $a->status,
        ])->toArray();

        return [['Asset Code', 'Item', 'Assigned To', 'Qty', 'Issue Date', 'Issued By', 'Status'], $rows];
    }

    protected function returnHistoryReport(Request $request): array
    {
        $query = ReturnProduct::with(['assignment.inventory', 'assignment.assignee', 'receivedBy']);
        $this->dateFilter($query, $request, 'return_date');

        $rows = $query->latest()->get()->map(fn ($r) => [
            $r->assignment?->inventory?->asset_code, $r->assignment?->inventory?->item_name,
            $r->assignment?->assignee?->full_name, $r->quantity, $r->return_date?->format('Y-m-d'),
            $r->receivedBy?->full_name, $r->condition,
        ])->toArray();

        return [['Asset Code', 'Item', 'Returned By', 'Qty', 'Return Date', 'Received By', 'Condition'], $rows];
    }

    protected function lowStockReport(): array
    {
        $rows = Inventory::lowStock()->with('category')->get()->map(fn ($i) => [
            $i->asset_code, $i->item_name, $i->category?->name, $i->available_quantity, $i->quantity,
        ])->toArray();

        return [['Asset Code', 'Item', 'Category', 'Available Qty', 'Total Qty'], $rows];
    }

    protected function maintenanceReport(Request $request): array
    {
        $query = Maintenance::with('inventory');
        $this->dateFilter($query, $request, 'sent_date');

        $rows = $query->latest()->get()->map(fn ($m) => [
            $m->inventory?->asset_code, $m->inventory?->item_name, $m->issue_title, $m->vendor,
            number_format((float) $m->repair_cost, 2), $m->sent_date?->format('Y-m-d'),
            $m->return_date?->format('Y-m-d'), $m->status,
        ])->toArray();

        return [['Asset Code', 'Item', 'Issue', 'Vendor', 'Cost', 'Sent Date', 'Return Date', 'Status'], $rows];
    }

    protected function disposalReport(Request $request): array
    {
        $query = Inventory::with('depreciation')->where('status', 'disposed');
        $rows = $query->get()->map(fn ($i) => [
            $i->asset_code, $i->item_name, $i->depreciation?->disposal_date?->format('Y-m-d'),
            $i->depreciation?->disposal_reason, number_format((float) ($i->depreciation?->disposal_value ?? 0), 2),
        ])->toArray();

        return [['Asset Code', 'Item', 'Disposal Date', 'Reason', 'Disposal Value'], $rows];
    }
}
