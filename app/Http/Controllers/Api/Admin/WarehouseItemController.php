<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Repositories\warehouseItemRepository;
use App\Http\Resources\WarehouseItemResource;
use App\Models\Warehouse;
use App\Models\WarehouseItem;
use App\Services\warehouseItemService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WarehouseItemController extends Controller
{
    private warehouseItemRepository $warehouseItemRepository;

    public function __construct(warehouseItemRepository $warehouseItemRepository)
    {
        $this->warehouseItemRepository= $warehouseItemRepository;
        $this->middleware(['auth:sanctum', 'Localization']);
//        $this->middleware(['permission:Admin']);

    }

    public function destroy(WarehouseItem $warehouseItem)
    {

        $data = $this->warehouseItemRepository->destroy($warehouseItem);
        return [__($data['message']), $data['code']];

    }

    public function showDeleted(): JsonResponse
    {
        $data=$this->warehouseItemRepository->showDeleted();
        return $this->showAll($data['WarehouseItem'],WarehouseItemResource::class,__($data['message']));
    }
    public function restore(Request $request){

        $data = $this->warehouseItemRepository->restore($request);
        return [__($data['message']),$data['code']];
    }
}
