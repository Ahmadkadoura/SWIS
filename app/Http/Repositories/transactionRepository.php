<?php

namespace App\Http\Repositories;

use App\Enums\sourceType;
use App\Enums\transactionType;
use App\Exceptions\InvalidQuantitiesException;
use App\Http\Requests\Donor\storeDonorItemRequest;
use App\Http\Responses\Response;
use App\Models\Ctn;
use App\Models\donorItem;
use App\Models\Driver;
use App\Models\Item;
use App\Models\Transaction;
use App\Models\transactionDriver;
use App\Models\TransactionItem;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseItem;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

class transactionRepository extends baseRepository
{
    public function __construct(Transaction $model)
    {
        parent::__construct($model);
    }
    public function index(): LengthAwarePaginator
    {
        $filters = [
            AllowedFilter::exact('parent_id'),
            AllowedFilter::exact('status'),
            AllowedFilter::exact('convoy','is_convoy'),
            AllowedFilter::exact('type','transaction_type'),
            AllowedFilter::exact('mode','transaction_mode_type'),
            AllowedFilter::scope('date'),
            AllowedFilter::partial('code'),
        ];
        $sorts = [
            AllowedSort::field('code'),
            AllowedSort::field('date'),
            AllowedSort::field('created_at'),
            AllowedSort::field('updated_at'),
        ];


        $query = Transaction::with('childTransactions.destinationable','transactionItem.item','destinationable');
        return $this->filter($query, $filters, $sorts);
    }

    public function create( $dataItem): array
    {
        $transaction = Transaction::create(
            [
            'is_convoy' => $dataItem['is_convoy'],
            'notes' => $dataItem['notes'] ?? null,
            'waybill_num' => $dataItem['waybill_num'],
            'waybill_img' => $dataItem['waybill_img'],
            'status' => $dataItem['status'],
            'date' => $dataItem['date'],
            'sourceable_type' => $dataItem['sourceable_type'],
            'sourceable_id' => $dataItem['sourceable_id'],
            'destinationable_type' => $dataItem['destinationable_type'],
            'destinationable_id' => $dataItem['destinationable_id'],
            'qr_code' => $dataItem['qr_code'] ?? null,
            'transaction_type' => $dataItem['transaction_type'],
            'transaction_mode_type' => $dataItem['transaction_mode_type'] ,
        ]
        );

        if (isset($dataItem['items']) && is_array($dataItem['items'])) {
            $this->createTransactionItems($transaction, $dataItem);
            $this->updateQuantity($transaction,$dataItem);
        }

        if (isset($dataItem['drivers']) && is_array($dataItem['drivers'])) {
            $this->assignDriversToTransaction($transaction, $dataItem['drivers']);
        }

        return ['message' => "Transaction created successfully", 'Transaction' => $transaction];
    }

    private function createTransactionItems(Transaction $transaction, array $dataItem): void
    {
        if ($dataItem['sourceable_type'] === sourceType::keeper->value &&
         $dataItem['transaction_type']===transactionType::transactionOut->value) {
            $warehouse = Warehouse::find($dataItem['sourceable_id']);
            foreach ($dataItem['items'] as $itemData) {
                $this->createWarehouseItemTransaction($warehouse, $transaction, $itemData);
            }
        } else {
            foreach ($dataItem['items'] as $itemData) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'item_id' => $itemData['item_id'],
                    'CTN' => $itemData['CTN'] ,
                    'quantity' => $itemData['quantity'],
                ]);
            }
        }
    }

    private function createWarehouseItemTransaction(Warehouse $warehouse, Transaction $transaction, array $itemData): void
    {
        $warehouseItem = WarehouseItem::where('item_id', $itemData['item_id'])
            ->where('warehouse_id', $warehouse->id)
            ->first();
        if (!$warehouseItem || $warehouseItem->quantity < $itemData['quantity']) {
            throw new InvalidQuantitiesException(
                null,
                __("The item quantity is not enough or you don't have this item")
            );
        }
        $remainingQuantity = $itemData['quantity'];
        $ctns = $warehouseItem->ctns()->orderBy('created_at', 'asc')->get();
        foreach ($ctns as $ctn) {
            if ($remainingQuantity <= 0) break;

            if ($ctn->quantity <= $remainingQuantity) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'item_id' => $itemData['item_id'],
                    'CTN' => $ctn->CTN, // تخزين CTN المستخدم
                    'quantity' => $ctn->quantity,
                ]);

                $remainingQuantity -= $ctn->quantity;
                $ctn->delete();
            } else {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'item_id' => $itemData['item_id'],
                    'CTN' => $ctn->CTN,
                    'quantity' => $remainingQuantity,
                ]);
                $ctn->quantity -= $remainingQuantity;
                $ctn->save();

                $remainingQuantity = 0;
            }
        }
        if ($remainingQuantity > 0) {
            throw new InvalidQuantitiesException(
                null,
                __("The item quantity is not enough or you don't have this item")
            );
        }
    }


    private function createOrFindDriver(array $driverData): int
    {
        // Check if the driver exists by national_id or vehicle_number
        $existingDriver = Driver::where('national_id', $driverData['national_id'])
            ->where('name', $driverData['name'])
            ->first();

        if ($existingDriver) {
            // Driver exists, return the existing driver ID
            return $existingDriver->id;
        }

        // Driver doesn't exist, create a new one
        $newDriver = Driver::create([
            'name' => $driverData['name'],
            'vehicle_number' => $driverData['vehicle_number'],
            'national_id' => $driverData['national_id'],
            'phone' => $driverData['phone'] ?? null,
            'transportation_comp' => $driverData['transportation_comp'] ?? null,
        ]);

        return $newDriver->id;
    }

    private function assignDriversToTransaction(Transaction $transaction, array $drivers): void
    {
        foreach ($drivers as $driver) {
            // Get driver ID by either finding or creating the driver
            $driverId = $this->createOrFindDriver($driver);

            // Associate the driver with the transaction
            TransactionDriver::create([
                'transaction_id' => $transaction->id,
                'driver_id' => $driverId,
            ]);
        }
    }

    public function updateQuantity(Transaction $transaction, array $dataItem):void
    {

        if($transaction->transaction_type->value===transactionType::transactionOut->value)
        {
            if($transaction->sourceable_type->value==sourceType::keeper->value && $transaction->destinationable_type->value==sourceType::keeper->value)
            {
                $source=Warehouse::where('id',$transaction->sourceable_id)->first();
                $destination=Warehouse::where('id',$transaction->destinationable_id)->first();
                if($source->is_Distribution_point!=1 &&$destination->is_Distribution_point==1)
                {
                    foreach ($dataItem['items'] as $item)
                    {
                        //update quantity in warehouse
                        $warehouseItem=WarehouseItem::where('warehouse_id',$source->id)->where('item_id',$item['item_id'])->first();
                        $warehouseItem->quantity=$warehouseItem->quantity-$item['quantity'];
                        $warehouseItem->update();
                        //update quantity in system
                        $systemItem=Item::where('id',$item['item_id'])->first();
                        $systemItem->quantity=$systemItem->quantity-$item['quantity'];
                        $systemItem->update();
                        //update quantity in donor
                        $itemQuantity=$item['item_id'];
                        $donorItem=DonorItem::where('item_id',$item['item_id'])
                            ->where('branch_id', $source->branch_id)
                            ->first();
                        if ($donorItem->quantity > $itemQuantity) {
                            // If the current donor item has more quantity than needed, subtract and update
                            $donorItem->quantity -= $itemQuantity;
                            $donorItem->save();
                        } else {
                            throw new InvalidQuantitiesException(
                                null,
                                __("The donor item quantity is not enough or the donor don't have this item")
                            );
                        }
                        $ctn = Ctn::where('warehouse_item_id',$warehouseItem->id)->where('CTN',$item['CTN'])->first();
                        $ctn->quantity -= $item['quantity'];
                        $ctn->update();



                    }
                }else{
                    foreach ($dataItem['items'] as $item)
                    {
                        //update quantity in warehouse
                        $warehouseItem = WarehouseItem::where('warehouse_id', $source->id)->where('item_id', $item['item_id'])->first();
                        $warehouseItem->quantity = $warehouseItem->quantity - $item['quantity'];
                        $warehouseItem->update();
                        $ctn = Ctn::where('warehouse_item_id',$warehouseItem->id)->where('CTN',$item['CTN'])->first();
                        $ctn->quantity -= $item['quantity'];
                        $ctn->update();
                    }
                }
            } else if($transaction->sourceable_type->value==sourceType::keeper->value && $transaction->destinationable_type->value==sourceType::donor->value)
                {
                    $source=Warehouse::where('id',$transaction->sourceable_id)->first();
                    $destination=User::where('id',$transaction->destinationable_id)->first();
                    foreach ($dataItem['items'] as $item)
                    {

                        //update quantity in warehouse
                        $warehouseItem=WarehouseItem::where('warehouse_id',$source->id)->where('item_id',$item['item_id'])->first();
                        $warehouseItem->quantity=$warehouseItem->quantity-$item['quantity'];
                        $warehouseItem->update();
                        //update quantity in system
                        $systemItem=Item::where('id',$item['item_id'])->first();
                        $systemItem->quantity=$systemItem->quantity-$item['quantity'];
                        $systemItem->update();
                        //update quantity in donor
                        $donorItem = DonorItem::where('user_id', $destination->id)
                            ->where('item_id', $item['item_id'])
                            ->where('branch_id', $source->branch_id)
                            ->first();
                            $donorItem->quantity = $donorItem->quantity-$item['quantity'];
                        $ctn = Ctn::where('warehouse_item_id',$warehouseItem->id)->where('CTN',$item['CTN'])->first();
                            $ctn->quantity -= $item['quantity'];
                            $ctn->update();
                    }
                }


        }else
        {

            if($transaction->sourceable_type->value==sourceType::keeper->value && $transaction->destinationable_type->value==sourceType::keeper->value)
            {
                $source=Warehouse::where('id',$transaction->sourceable_id)->first();
                $destination=Warehouse::where('id',$transaction->destinationable_id)->first();
                 if($source->is_Distribution_point==1 && $destination->is_Distribution_point!=1)
                {
                    foreach ($dataItem['items'] as $item)
                    {
                        //update quantity in warehouse
                        $warehouseItem=WarehouseItem::where('warehouse_id',$destination->id)->where('item_id',$item['item_id'])->first();
                        $warehouseItem->quantity=$warehouseItem->quantity + $item['quantity'];
                        $warehouseItem->update();
                        //update quantity in system
                        $systemItem=Item::where('id',$item['item_id'])->first();
                        $systemItem->quantity=$systemItem->quantity + $item['quantity'];
                        $systemItem->update();
                        $donorItem = DonorItem::where('item_id', $item['item_id'])
                            ->where('branch_id', $destination->branch_id)
                            ->first();
                        $donorItem->quantity =$donorItem->quantity+ $item['quantity'];
                        $donorItem->save();
                        $ctn = Ctn::where('warehouse_item_id',$warehouseItem->id)->where('CTN',$item['CTN'])->first();
                        if ($ctn) {
                            $ctn->quantity += $item['quantity'];
                            $ctn->update();
                        }else {
                            Ctn::create([
                                'warehouse_item_id' => $warehouseItem->id,
                                'item_id' => $item['item_id'],
                                'ctn' => $item['CTN'],
                                'quantity' => $item['quantity'],
                            ]);}
                    }
                }else
                {
                    foreach ($dataItem['items'] as $item)
                    {
                        $warehouseItem = WarehouseItem::where('warehouse_id', $destination->id)->where('item_id', $item['item_id'])->first();
                        $warehouseItem->quantity = $warehouseItem->quantity + $item['quantity'];
                        $warehouseItem->update();
                        $ctn = Ctn::where('warehouse_item_id',$warehouseItem->id)->where('CTN',$item['CTN'])->first();
                        if ($ctn) {
                            $ctn->quantity += $item['quantity'];
                            $ctn->update();
                        }else {
                            Ctn::create([
                                'warehouse_item_id' => $warehouseItem->id,
                                'item_id' => $item['item_id'],
                                'ctn' => $item['CTN'],
                                'quantity' => $item['quantity'],
                            ]);}
                    }
                }
            }else if($transaction->sourceable_type->value==sourceType::donor->value && $transaction->destinationable_type->value==sourceType::keeper->value)
            {
                $source=User::where('id',$transaction->sourceable_id)->first();
                $destination =Warehouse::where('id',$transaction->destinationable_id)->first();
                foreach ($dataItem['items'] as $item) {
                    //update quantity in warehouse
                    $warehouseItem = WarehouseItem::where('warehouse_id', $destination->id)->where('item_id', $item['item_id'])->first();
                    $warehouseItem->quantity = $warehouseItem->quantity + $item['quantity'];
                    $warehouseItem->update();
                    $ctn = Ctn::where('warehouse_item_id',$warehouseItem->id)->where('CTN',$item['CTN'])->first();
                    if ($ctn) {
                        $ctn->quantity += $item['quantity'];
                        $ctn->update();
                    }else {
                        Ctn::create([
                            'warehouse_item_id' => $warehouseItem->id,
                            'item_id' => $item['item_id'],
                            'ctn' => $item['CTN'],
                            'quantity' => $item['quantity'],
                        ]);}
                    //update quantity in system
                    $systemItem = Item::where('id', $item['item_id'])->first();
                    $systemItem->quantity = $systemItem->quantity + $item['quantity'];
                    $systemItem->update();
                    //update quantity in donor
                    $donorItem = DonorItem::where('user_id', $source->id)
                        ->where('item_id', $item['item_id'])
                        ->where('branch_id', $destination->branch_id)
                        ->first();

                    if ($donorItem) {
                        // If the donor item exists, update the quantity
                        $donorItem->quantity += $item['quantity'];
                        $donorItem->update();
                    } else {
                        // If the donor item does not exist, create a new one
                        DonorItem::create([
                            'user_id' => $source->id,
                            'item_id' => $item['item_id'],
                            'branch_id' => $destination->branch_id,
                            'quantity' => $item['quantity'],
                        ]);
                    }
                }
            }

        }

    }


    public function indexTransactionForKeeper($user_id):LengthAwarePaginator
    {
        $filters = [
            AllowedFilter::exact('parent_id'),
            AllowedFilter::exact('status'),
            AllowedFilter::exact('convoy','is_convoy'),
            AllowedFilter::exact('type','transaction_type'),
            AllowedFilter::exact('mode','transaction_mode_type'),
            AllowedFilter::scope('date'),
            AllowedFilter::partial('code'),
        ];
        $sorts = [
            AllowedSort::field('code'),
            AllowedSort::field('date'),
            AllowedSort::field('created_at'),
            AllowedSort::field('updated_at'),
        ];

            $query = Transaction::where('sourceable_type',sourceType::keeper->value)->where('sourceable_id',$user_id)
                ->orWhere('destinationable_type',sourceType::keeper->value)->where('destinationable_id',$user_id)
                ->with([
                    'childTransactions.destinationable',
                    'transactionItem.item',
                    'destinationable'
                ]);
        return $this->filter($query, $filters, $sorts);
    }

    public function showTransactionForKeeper($transaction_id){

        $data = Transaction::where('id', $transaction_id)
            ->with([
                'childTransactions.destinationable',
                'transactionItem.item',
                'destinationable'
            ])
            ->first();

            $message="Transaction showed successfully";

        return ['message'=>$message,"Transaction"=>$data];
    }

    public function indexTransactionForDonor($donor_id):LengthAwarePaginator
    {
        $filters = [
            AllowedFilter::exact('parent_id'),
            AllowedFilter::exact('status'),
            AllowedFilter::exact('convoy','is_convoy'),
            AllowedFilter::exact('type','transaction_type'),
            AllowedFilter::exact('mode','transaction_mode_type'),
            AllowedFilter::scope('date'),
            AllowedFilter::partial('code'),
        ];
        $sorts = [
            AllowedSort::field('code'),
            AllowedSort::field('date'),
            AllowedSort::field('created_at'),
            AllowedSort::field('updated_at'),
        ];

        $query = Transaction::where('sourceable_type',sourceType::donor->value)->where('sourceable_id',$donor_id)
            ->orWhere('destinationable_type',sourceType::donor->value)->where('destinationable_id',$donor_id)
            ->with([
                'childTransactions.destinationable',
                'transactionItem.item',
                'destinationable'
            ]);
        return $this->filter($query, $filters, $sorts);
    }

    public function showTransactionForDonor($donor_id,$transactuon_id){
        $data = Transaction::where('sourceable_type',sourceType::donor->value)->where('sourceable_id',$donor_id)
            ->orWhere('destinationable_type',sourceType::donor->value)->where('destinationable_id',$donor_id)
            ->where('id',$transactuon_id)
            ->with('transactionItem.item','driverTransaction.driver')
            ->first();

            $message="Transactions indexed successfully";

        return ['message'=>$message,"Transaction"=>$data];
    }

}

