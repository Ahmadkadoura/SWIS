<?php

namespace App\Http\Repositories;

use App\Enums\sourceType;
use App\Enums\transactionType;
use App\Models\TransactionItem;
use App\Models\Warehouse;
use App\Models\WarehouseItem;
use Illuminate\Support\Facades\DB;

class transactionItemRepository extends baseRepository
{
    public function __construct(transactionItem $model)
    {
        parent::__construct($model);
    }

    public function index(): array
    {

        $data = transactionItem::with( 'item')->paginate(10);
        if ($data->isEmpty()) {
            $message = "There are no transaction in this warehouse at the moment";
        } else {
            $message = "Transaction warehouse indexed successfully";
        }
        return ['message' => $message, "TransactionWarehouseItem" => $data];
    }

    public function inventory(array $data)
    {
        $warehouseId = $data['warehouse_id'];
        $startDate = $data['start_date'];
        $endDate = $data['end_date'];

        // Fetch opening balances from periodic_balances
        $openingBalances = DB::table('periodic_balances')
            ->select('item_id', DB::raw('SUM(balance) as opening_balance'))
            ->where('warehouse_id', $warehouseId)
            ->where('balance_date', '<', $startDate)
            ->groupBy('item_id')
            ->get()
            ->keyBy('item_id');

        // Fetch inventory details based on transaction type
        $inventory = TransactionItem::select(
            'transaction_items.item_id',
            DB::raw('SUM(CASE WHEN transactions.transaction_type = ' . transactionType::transactionIn->value . ' THEN transaction_items.quantity ELSE 0 END) as total_quantity_in'),
            DB::raw('SUM(CASE WHEN transactions.transaction_type = ' . transactionType::transactionOut->value . ' THEN transaction_items.quantity ELSE 0 END) as total_quantity_out')
        )
            ->join('transactions', 'transactions.id', '=', 'transaction_items.transaction_id')
            ->where(function ($query) use ($warehouseId) {
                $query->where(function ($subQuery) use ($warehouseId) {
                    $subQuery->where('transactions.sourceable_type', sourceType::keeper->value)
                        ->where('transactions.sourceable_id', $warehouseId)
                        ->where('transactions.transaction_type', transactionType::transactionOut->value);
                })
                    ->orWhere(function ($subQuery) use ($warehouseId) {
                        $subQuery->where('transactions.destinationable_type', sourceType::keeper->value)
                            ->where('transactions.destinationable_id', $warehouseId)
                            ->where('transactions.transaction_type', transactionType::transactionIn->value);
                    });
            })
            ->whereBetween('transactions.created_at', [$startDate, $endDate])
            ->groupBy('transaction_items.item_id')
            ->get()
            ->keyBy('item_id');

        // Fetch quantities directly from WarehouseItem
        $warehouseItems = WarehouseItem::select('item_id', 'quantity')
            ->where('warehouse_id', $warehouseId)
            ->get()
            ->keyBy('item_id');

        // Merge data
        $mergedInventory = $inventory->map(function ($item) use ($openingBalances, $warehouseItems) {
            $openingBalance = $openingBalances->get($item->item_id)?->opening_balance ?? 0;
            $itemQuantityInWarehouse = $warehouseItems->get($item->item_id)?->quantity ?? 0;

            return [
                'item_id' => $item->item_id,
                'total_quantity_in' => (int) $item->total_quantity_in,
                'total_quantity_out' => (int) $item->total_quantity_out,
                'opening_balance' => (int) $openingBalance,
                'quantity_in_warehouse' => (int) $itemQuantityInWarehouse,
                'final_balance' => (int) ($openingBalance + $item->total_quantity_in - $item->total_quantity_out),
            ];
        });

        return $mergedInventory->isEmpty() ? collect([]) : $mergedInventory;
    }


    public function systemInventory($data)
    {
        $startDate = $data['start_date'];
        $endDate = $data['end_date'];

        // Get IDs of warehouses that are not distribution points
        $nonDistributionWarehouseIds = Warehouse::where('is_Distribution_point', false)
            ->pluck('id');

        // Fetch inventory details based on transaction type and polymorphic relations
        $inventory = TransactionItem::select(
            'transaction_items.item_id',
            DB::raw('SUM(CASE WHEN transactions.transaction_type = ' . transactionType::transactionIn->value . ' THEN transaction_items.quantity ELSE 0 END) as total_quantity_in'),
            DB::raw('SUM(CASE WHEN transactions.transaction_type = ' . transactionType::transactionOut->value . ' THEN transaction_items.quantity ELSE 0 END) as total_quantity_out')
        )
            ->join('transactions', 'transactions.id', '=', 'transaction_items.transaction_id')
            ->where(function ($query) use ($nonDistributionWarehouseIds) {
                // Transactions that involve non-distribution point warehouses
                $query->where(function ($subQuery) use ($nonDistributionWarehouseIds) {
                    $subQuery->where('transactions.sourceable_type', sourceType::keeper->value)
                        ->whereIn('transactions.sourceable_id', $nonDistributionWarehouseIds)
                        ->where('transactions.transaction_type', transactionType::transactionOut->value);
                })
                    ->orWhere(function ($subQuery) use ($nonDistributionWarehouseIds) {
                        $subQuery->where('transactions.destinationable_type', sourceType::keeper->value)
                            ->whereIn('transactions.destinationable_id', $nonDistributionWarehouseIds)
                            ->where('transactions.transaction_type', transactionType::transactionIn->value);
                    });
            })
            ->whereBetween('transactions.date', [$startDate, $endDate])
            ->groupBy('transaction_items.item_id')
            ->with('item')
            ->get();

        if ($inventory->isEmpty()) {
            return collect([]);
        }

        // Get the overall quantity of items from WarehouseItem across all warehouses
        $warehouseItems = WarehouseItem::whereIn('warehouse_id', $nonDistributionWarehouseIds)->select('item_id', DB::raw('SUM(quantity) as total_quantity_in_warehouse'))
            ->groupBy('item_id')
            ->get()
            ->keyBy('item_id');

        // Merge the inventory data with available quantities
        $mergedInventory = $inventory->map(function ($item) use ($warehouseItems) {
            $itemQuantityInWarehouse = $warehouseItems->has($item->item_id) ? $warehouseItems[$item->item_id]->total_quantity_in_warehouse : 0;

            return [
                'item_id' => $item->item_id,
                'item_name' => $item->item->name,
                'total_quantity_in' => (string)$item->total_quantity_in,
                'total_quantity_out' => (string)$item->total_quantity_out,
                'quantity_in_warehouse' => (string)$itemQuantityInWarehouse,
            ];
        });

        return $mergedInventory;
    }






}
