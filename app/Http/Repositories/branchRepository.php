<?php

namespace App\Http\Repositories;

use App\Models\Branch;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

class branchRepository extends baseRepository
{
    public function __construct(Branch $model)
    {
        parent::__construct($model);
    }
    public function index(): LengthAwarePaginator
    {
        $filters = [
            AllowedFilter::exact('parent_id'),
            AllowedFilter::partial('name'),
            AllowedFilter::partial('code'),
        ];
        $sorts = [
            AllowedSort::field('name'),
            AllowedSort::field('code'),
            AllowedSort::field('created_at'),
            AllowedSort::field('updated_at'),
        ];

        return $this->filter(Branch::with('parentBranch'), $filters, $sorts);
    }
}

