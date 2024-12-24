<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\Response;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Driver;
use App\Models\Item;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Searchable\ModelSearchAspect;
use Spatie\Searchable\Search;
use Throwable;

class SearchController extends Controller
{public function __construct()
{
    $this->middleware(['auth:sanctum']);
}

    public function searchItems(Request $request)
    {
        $query = $request->query('query');

        try {
            $searchResults = (new Search())
                ->registerModel(Item::class, ['name->en', 'name->ar' , 'code'])
                ->search($query);
        } catch (Throwable $th) {
            return Response::Error(null, $th->getMessage());
        }

        if ($searchResults->isEmpty()) return Response::Error(null, __('There is no such items'));
        return Response::Success($searchResults, __('Items retrieved successfully'));
    }

    public function searchDrivers(Request $request)
    {
        $query = $request->query('query');

        try {
            $searchResults = (new Search())
                ->registerModel(Driver::class, [
                    'name->en',
                    'name->ar',
                    // 'vehicle_number',
                    // 'national_id',
                    // 'transportation_company_name->en',
                    // 'phone',
                ])
                ->search($query);
        } catch (Throwable $th) {
            return Response::Error(null, $th->getMessage());
        }

        if ($searchResults->isEmpty()) return Response::Error(null, __('There is no such drivers'));
        return Response::Success($searchResults, __('Drivers retrieved successfully'));
    }

    public function searchTransactions(Request $request)
    {
        $query = $request->query('query');

        try {
            $searchResults = (new Search())
                ->registerModel(Transaction::class, ['code'])
                ->search($query);
        } catch (Throwable $th) {
            return Response::Error(null, $th->getMessage());
        }

        if ($searchResults->isEmpty()) return Response::Error(null, __('There is no such transactions'));
        return Response::Success($searchResults, __('Transactions retrieved successfully'));
    }

    public function searchWarehouses(Request $request)
    {
        $query = $request->query('query');
        try {
            $searchResults = (new Search())
                ->registerModel(
                    Warehouse::class,
                    function (ModelSearchAspect $modelSearchAspect) {
                        $modelSearchAspect
                            ->addSearchableAttribute('name->en')
                            ->addSearchableAttribute('name->ar')
                            ->addSearchableAttribute('code')
                            ->with(['branch', 'parentWarehouse', 'user']);
                    }
                )
                ->search($query);
        } catch (Throwable $th) {
            return Response::Error(null, $th->getMessage());
        }

        if ($searchResults->isEmpty()) return Response::Error(null, __('There is no such warehouses'));
        return Response::Success( $searchResults, __('Warehouses retrieved successfully'));
    }

    public function searchWarehousesItems(Request $request)
    {
        $query = $request->query('query');
        $warehouse = Warehouse::where('user_id',$request->user()->id)->first();

        try {
            $searchResults = (new Search())
                ->registerModel(
                    Item::class,
                    function (ModelSearchAspect $modelSearchAspect) use ($warehouse){
                        $modelSearchAspect
                            ->addSearchableAttribute('name->en')
                            ->addSearchableAttribute('name->ar')
                            ->addSearchableAttribute('code')
                            ->has('warehouseItem','>=',1,'and',function (Builder $q) use ($warehouse) {
                                $q->where('warehouse_id',$warehouse['id']);
                            });
                    }
                )
                ->search($query);
        } catch (Throwable $th) {
            return Response::Error(null, $th->getMessage());
        }

        if ($searchResults->isEmpty()) return Response::Error(null, __('There is no such items'));
        $final_result = $searchResults->map(function ($item) use ($warehouse) {
            $warehouseItems=WarehouseItem::where('warehouse_id',$warehouse['id'])->where('item_id',$item->searchable->id)->first();
            return [
                "id" => $item->searchable->id,
                "name"=> $item->searchable->name,
                "code" => $item->searchable->code,
                "sectorType" => $item->searchable->sectorType,
                "unitType" => $item->searchable->unitType,
                "size" => $item->searchable->size,
                "weight" => $item->searchable->weight,
                'quantity_in_warehouse' => $warehouseItems->quantity,
                "created_at" => $item->searchable->created_at->format('Y-m-d'),
            ];
        });
        return Response::Success($final_result, __('Items retrieved successfully'));
    }

    public function searchBranches(Request $request)
    {
        $query = $request->query('query');

        try {
            $searchResults = (new Search())
                ->registerModel(Branch::class, ['name->en', 'name->ar' , 'code'])
                ->search($query);
        } catch (Throwable $th) {
            return Response::Error(null, $th->getMessage());
        }

        if ($searchResults->isEmpty()) return Response::Error(null, __('There is no such branches'));
        return Response::Success($searchResults, __('Branches retrieved successfully'));
    }

    public function searchUsers(Request $request)
    {
        $query = $request->query('query');

        try {
            $searchResults = (new Search())
                ->registerModel(User::class, [
                    'name->en',
                    'name->ar',
                    'code',
                    // 'phone',
                    // 'email',
                ])
                ->search($query);
        } catch (Throwable $th) {
            return Response::Error(null, $th->getMessage());
        }

        if ($searchResults->isEmpty()) return Response::Error(null, __('There is no such users'));
        return Response::Success($searchResults, __('Users retrieved successfully'));
    }

}
