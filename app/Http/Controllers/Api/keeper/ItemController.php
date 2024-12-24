<?php

namespace App\Http\Controllers\Api\keeper;

use App\Exports\ItemsExport;
use App\Http\Controllers\Controller;
use App\Http\Repositories\itemRepository;
use App\Http\Resources\ItemInWarehouseResource;
use App\Http\Resources\itemsResource;
use App\Http\Resources\indexKeeperItemResource;
use App\Http\Resources\showKeeperItemResource;
use App\Models\Item;
use App\Models\Warehouse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ItemController extends Controller
{
    private itemRepository $itemRepository;
     public function __construct( itemRepository $itemRepository){
         $this-> itemRepository =$itemRepository;
         $this->middleware(['auth:sanctum', 'Localization']);
//         $this->middleware(['permission:Keeper']);


     }
    public function index()
    {
        $data=$this->itemRepository->indexItemForKeeper(Auth::user()->id);
//        $message = $data->isEmpty() ? __('There are no Item at the moment') : __('Item retrieved successfully');
//        return $this->showAll($data, indexKeeperItemResource::class, $message);
        return $data;

    }
    public function show($item_id): JsonResponse
    {        $keeper=Warehouse::where('user_id',Auth::user()->id)->first();

        $data=$this->itemRepository->showItemForKeeper($item_id,$keeper->id);
        return $this->showOne($data['WarehouseItem'],showKeeperItemResource::class,__($data['message']));

    }
    public function itemExport()
    {
        // Define the file name and path
        $fileName = 'keeper_item_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
        $filePath = 'public/keeper/exports/users/' . $fileName;

        $data =Warehouse::where('user_id',Auth::user()->id)
            ->with('WarehouseItem.item')->get();

        $export = new ItemsExport($data);

        Excel::store($export, $filePath);

        return response()->json([
            'message' => __('File exported and saved successfully!'),
            'file_name' => $fileName,
            'file_url' =>  Storage::disk('public')->url($filePath)
        ]);
    }
}
