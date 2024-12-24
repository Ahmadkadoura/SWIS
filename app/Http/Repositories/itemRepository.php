<?php

namespace App\Http\Repositories;

use App\Enums\sectorType;
use App\Filters\FiltersRelationshipIncludes;
use App\Models\Item;
use App\Models\Warehouse;
use App\Models\WarehouseItem;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

class itemRepository extends baseRepository
{
    public function __construct(Item $model)
    {
        parent::__construct($model);
    }

    public function index(): LengthAwarePaginator
    {
        $filters = [
            AllowedFilter::exact('sector', 'sectorType'),
            AllowedFilter::exact('unit', 'unitType'),
            AllowedFilter::partial('name'),
            AllowedFilter::partial('code'),
            AllowedFilter::scope('size'),
            AllowedFilter::scope('weight'),
            AllowedFilter::scope('quantity'),
        ];
        $sorts = [
            AllowedSort::field('name'),
            AllowedSort::field('code'),
            AllowedSort::field('sector'),
            AllowedSort::field('unit'),
            AllowedSort::field('size'),
            AllowedSort::field('weight'),
            AllowedSort::field('quantity'),
            AllowedSort::field('created_at'),
            AllowedSort::field('updated_at'),
        ];

        return $this->filter(Item::query(), $filters, $sorts);
    }
    public function indexItemForKeeper($user_id)
    {
        $query = Warehouse::where('user_id', $user_id)
            ->whereHas('WarehouseItem', function ($query) {
                $query->whereNull('deleted_at')
                    ->whereHas('item', function ($qery) {
                        $qery->where('sectorType', request()->input('sector'))
                            ->whereNull('deleted_at');
                    });
            })
            ->whereNull('deleted_at')->with('warehouseItem.item')->get();
        // Define filters
//        $filters = [
//            AllowedFilter::exact('unit', 'warehouseItem.item.unitType'),
//            AllowedFilter::partial('name', 'warehouseItem.item.name'),
//            AllowedFilter::partial('code', 'warehouseItem.item.code'),
//            AllowedFilter::scope('size', 'warehouseItem.item.size'),
//            AllowedFilter::scope('weight', 'warehouseItem.item.weight'),
//            AllowedFilter::scope('quantity', 'warehouseItem.item.quantity'),
//        ];
//
//        // Define sorting options
//        $sorts = [
//            AllowedSort::field('name'),
//            AllowedSort::field('code'),
//            AllowedSort::field('sector'),
//            AllowedSort::field('unit'),
//            AllowedSort::field('size'),
//            AllowedSort::field('weight'),
//            AllowedSort::field('quantity'),
//            AllowedSort::field('created_at'),
//            AllowedSort::field('updated_at'),
//        ];

        // Apply filters and sorts
//        return $this->filter($query, $filters, $sorts);
return $query;
    }
    public function showItemForKeeper($item_id,$warehouse_id){

        $data = WarehouseItem::where('item_id', $item_id)
            ->where('warehouse_id', $warehouse_id)
            ->with('item')
            ->first();

            $message="Item showed successfully";

        return ['message'=>$message,"WarehouseItem"=>$data];
    }
}
