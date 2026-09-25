<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\AuditLog;
use App\Models\Inventory;
use App\Models\ReturnProduct;
use App\Models\StockHistory;
use App\Models\Transfer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class StockService
{
    /**
     * Issue stock to a user. Decreases available_quantity, creates the
     * Assignment, a StockHistory ledger row, and an AuditLog entry.
     * Enforces "product cannot be assigned unless it exists in inventory"
     * and "stock decreases automatically when issuing".
     */
    public function issue(Inventory $inventory, int $assignedTo, int $quantity, string $issueDate, ?string $remarks = null): Assignment
    {
        return DB::transaction(function () use ($inventory, $assignedTo, $quantity, $issueDate, $remarks) {
            $inventory = Inventory::where('id', $inventory->id)->lockForUpdate()->firstOrFail();

            if ($quantity < 1) {
                throw new RuntimeException('Quantity must be at least 1.');
            }
            if ($inventory->available_quantity < $quantity) {
                throw new RuntimeException('Not enough available stock to issue this quantity.');
            }

            $inventory->available_quantity -= $quantity;
            $inventory->save();

            $assignment = Assignment::create([
                'inventory_id' => $inventory->id,
                'assigned_to' => $assignedTo,
                'quantity' => $quantity,
                'issue_date' => $issueDate,
                'given_by' => Auth::id(),
                'remarks' => $remarks,
                'status' => 'issued',
            ]);

            StockHistory::create([
                'inventory_id' => $inventory->id,
                'action' => 'issue',
                'quantity' => -$quantity,
                'balance' => $inventory->available_quantity,
                'user_id' => Auth::id(),
                'remarks' => "Issued to user #{$assignedTo}",
                'created_at' => now(),
            ]);

            AuditLog::write($assignment, 'issued', null, $assignment->getAttributes());

            return $assignment;
        });
    }

    /**
     * Return previously issued stock. Increases available_quantity, records
     * a ReturnProduct row, updates the Assignment status, writes a ledger
     * row, and an audit log entry. Enforces "stock increases automatically
     * when returning".
     */
    public function returnStock(Assignment $assignment, int $quantity, string $returnDate, string $condition, ?string $remarks = null): ReturnProduct
    {
        return DB::transaction(function () use ($assignment, $quantity, $returnDate, $condition, $remarks) {
            $assignment = Assignment::where('id', $assignment->id)->lockForUpdate()->firstOrFail();
            $inventory = Inventory::where('id', $assignment->inventory_id)->lockForUpdate()->firstOrFail();

            $outstanding = $assignment->outstandingQuantity();
            if ($quantity < 1 || $quantity > $outstanding) {
                throw new RuntimeException("Return quantity must be between 1 and {$outstanding}.");
            }

            $inventory->available_quantity += $quantity;
            // Damaged returns don't come back as "new" condition stock.
            if ($condition === 'damaged' && $inventory->condition !== 'damaged') {
                $inventory->condition = 'fair';
            }
            $inventory->save();

            $return = ReturnProduct::create([
                'assignment_id' => $assignment->id,
                'quantity' => $quantity,
                'return_date' => $returnDate,
                'received_by' => Auth::id(),
                'condition' => $condition,
                'remarks' => $remarks,
            ]);

            $newOutstanding = $assignment->outstandingQuantity();
            $assignment->status = $newOutstanding === 0 ? 'returned' : 'partially_returned';
            $assignment->save();

            StockHistory::create([
                'inventory_id' => $inventory->id,
                'action' => 'return',
                'quantity' => $quantity,
                'balance' => $inventory->available_quantity,
                'user_id' => Auth::id(),
                'remarks' => "Returned from assignment #{$assignment->id}",
                'created_at' => now(),
            ]);

            AuditLog::write($return, 'returned', null, $return->getAttributes());

            return $return;
        });
    }

    /**
     * Transfer an issued asset from one user to another without touching
     * available stock (it stays "out" the whole time), preserving full
     * history via the transfers table and a fresh Assignment for the
     * receiving user. The original assignment is closed as "transferred".
     */
    public function transfer(Assignment $fromAssignment, int $toUserId, int $quantity, string $transferDate, ?string $remarks = null): Transfer
    {
        return DB::transaction(function () use ($fromAssignment, $toUserId, $quantity, $transferDate, $remarks) {
            $fromAssignment = Assignment::where('id', $fromAssignment->id)->lockForUpdate()->firstOrFail();
            $outstanding = $fromAssignment->outstandingQuantity();

            if ($quantity < 1 || $quantity > $outstanding) {
                throw new RuntimeException("Transfer quantity must be between 1 and {$outstanding}.");
            }

            // Close out (fully or partially) the original assignment.
            $fromAssignment->status = $quantity === $outstanding ? 'transferred' : 'partially_returned';
            $fromAssignment->save();

            $toAssignment = Assignment::create([
                'inventory_id' => $fromAssignment->inventory_id,
                'assigned_to' => $toUserId,
                'quantity' => $quantity,
                'issue_date' => $transferDate,
                'given_by' => Auth::id(),
                'remarks' => $remarks ?? "Transferred from user #{$fromAssignment->assigned_to}",
                'status' => 'issued',
            ]);

            $transfer = Transfer::create([
                'from_assignment_id' => $fromAssignment->id,
                'to_assignment_id' => $toAssignment->id,
                'inventory_id' => $fromAssignment->inventory_id,
                'from_user_id' => $fromAssignment->assigned_to,
                'to_user_id' => $toUserId,
                'quantity' => $quantity,
                'transfer_date' => $transferDate,
                'approved_by' => Auth::id(),
                'remarks' => $remarks,
            ]);

            StockHistory::create([
                'inventory_id' => $fromAssignment->inventory_id,
                'action' => 'transfer',
                'quantity' => 0,
                'balance' => $fromAssignment->inventory->available_quantity,
                'user_id' => Auth::id(),
                'remarks' => "Transferred from user #{$fromAssignment->assigned_to} to user #{$toUserId}",
                'created_at' => now(),
            ]);

            AuditLog::write($transfer, 'transferred', null, $transfer->getAttributes());

            return $transfer;
        });
    }

    /**
     * Increase stock (e.g. new purchase / stock top-up) and log it.
     */
    public function addStock(Inventory $inventory, int $quantity, ?string $remarks = null): void
    {
        DB::transaction(function () use ($inventory, $quantity, $remarks) {
            $inventory = Inventory::where('id', $inventory->id)->lockForUpdate()->firstOrFail();
            $inventory->quantity += $quantity;
            $inventory->available_quantity += $quantity;
            $inventory->save();

            StockHistory::create([
                'inventory_id' => $inventory->id,
                'action' => 'add',
                'quantity' => $quantity,
                'balance' => $inventory->available_quantity,
                'user_id' => Auth::id(),
                'remarks' => $remarks ?? 'Stock added',
                'created_at' => now(),
            ]);
        });
    }
}
