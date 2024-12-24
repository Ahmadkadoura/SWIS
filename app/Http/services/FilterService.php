<?php

namespace App\Http\services;

use App\Models\Item;
use App\Models\Transaction;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class FilterService
{
    public static function item()
    {
        $data = QueryBuilder::for(Item::class)
            ->allowedFilters([
                AllowedFilter::exact('sector', 'sectorType'),
                AllowedFilter::exact('unit', 'unitType'),
//                AllowedFilter::scope('size'),
//                AllowedFilter::scope('weight'),
//                AllowedFilter::scope('quantity'),
                AllowedFilter::scope('between','created_between'),
            ])
            ->defaultSort('name->en')
            ->allowedSorts([
                AllowedSort::field('name','name->en'),
                'size',
                'weight',
                'quantity',
                'created_at'
            ])
            ->paginate(10);
        return ['Item'=>$data , 'message'=>'Items filtered successfully'];
    }

    public static function transaction()
    {
        $data = QueryBuilder::for(Transaction::class)
            ->allowedFilters([
                AllowedFilter::exact('user','user_id'), //should be hashed or replaced with slug
                AllowedFilter::exact('warehouse','warehouse_id'), //should be hashed or replaced with slug
                AllowedFilter::exact('convoy','is_convoy'),
                AllowedFilter::exact('status'),
                AllowedFilter::exact('type','transaction_type'),
                AllowedFilter::exact('mode','transaction_mode_type'),
                AllowedFilter::scope('date'),
                AllowedFilter::scope('between','created_between'),
            ])
            ->allowedSorts([
                'date',
                'created_at'
            ])
            ->paginate(10);
        return ['Transaction'=>$data , 'message'=>'Transactions filtered successfully'];
    }
}
