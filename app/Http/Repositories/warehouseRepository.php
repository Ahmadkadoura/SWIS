<?php

namespace App\Http\Repositories;

use App\Models\Warehouse;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

class warehouseRepository extends baseRepository
{
    public function __construct(Warehouse $model)
    {
        parent::__construct($model);
    }
    public function index(): LengthAwarePaginator
    {
        $filters = [
            AllowedFilter::exact('main','parent_id'),
            AllowedFilter::exact('branch','branch_id'),
            AllowedFilter::exact('keeper','user_id'),
            AllowedFilter::exact('Distribution_point','is_Distribution_point'),
            AllowedFilter::partial('name'),
            AllowedFilter::partial('code'),
            AllowedFilter::scope('capacity'),

        ];
        $sorts = [
            AllowedSort::field('name'),
            AllowedSort::field('code'),
            AllowedSort::field('capacity'),
            AllowedSort::field('location'),
            AllowedSort::field('parent_id'),
            AllowedSort::field('branch_id'),
            AllowedSort::field('user_id'),
            AllowedSort::field('created_at'),
            AllowedSort::field('updated_at'),
        ];

        return $this->filter(Warehouse::with('user', 'branch', 'parentW rehouse'), $filters, $sorts);
    }

    public function showWarehouseForKeeper($keeper){

        $data = Warehouse::where('user_id',$keeper)
            ->with('WarehouseItem.item','parentWarehouse')
            ->first();
            $message="Warehouse showed successfully";

        return ['message'=>$message,"Warehouse"=>$data];
    }
    public function indexWarehouseWithItems():array
    {

        $data = Warehouse::with('warehouseItem.item')
            ->paginate(10);
        if ($data->isEmpty()){
            $message="There are no Warehouse at the moment";
        }else
        {
            $message="Warehouse indexed successfully";
        }
        return ['message'=>$message,"Warehouse"=>$data];
    }

    public function showWarehouseWithItems(Warehouse $warehouse):array
    {
        $data =$warehouse->load('warehouseItem.item');
        $message="Warehouse showed successfully";

        return ['message'=>$message,"Warehouse"=>$data];
    }

}
