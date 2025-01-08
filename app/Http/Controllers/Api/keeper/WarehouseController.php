<?php

namespace App\Http\Controllers\Api\Keeper;

use App\Http\Controllers\Controller;
use App\Http\Repositories\transactionItemRepository;
use App\Http\Repositories\warehouseRepository;
use App\Http\Resources\InventoryResource;
use App\Http\Resources\showKeeperItemResource;
use App\Http\Resources\WarehouseResource;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InventoryExport;
use Illuminate\Support\Facades\Storage;


class WarehouseController extends Controller
{
    private warehouseRepository $warehouseRepository;
    private transactionItemRepository $transactionItemRepository;


    public function __construct(warehouseRepository $warehouseRepository,transactionItemRepository $transactionItemRepository)
    {
        $this->warehouseRepository=$warehouseRepository;
        $this->transactionItemRepository =$transactionItemRepository;
        $this->middleware(['auth:sanctum', 'Localization']);
//        $this->middleware(['permission:Keeper']);
    }
    public function show()
    {
        $user = Auth::user();
        $data = $this->warehouseRepository->showWarehouseForKeeper(Auth::user()->id);
        return $this->showOne($data['Warehouse'],WarehouseResource::class,__($data['message']));
    }
    public function Inventory(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);
        $keeper=Warehouse::where('user_id',Auth::user()->id)->first();
        $data = [
            'warehouse_id' => $keeper->id,
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date')
        ];
        $inventory=$this->transactionItemRepository->inventory($data);

        return $this->showOneCollection($inventory, InventoryResource::class);
    }
    public function exportInventory(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        $keeper = Warehouse::where('user_id', Auth::user()->id)->first();
        $warehouseName = Str::slug($keeper->name); // Convert name to a slug-friendly format

        $data = [
            'warehouse_id' => $keeper->id,
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date')
        ];

        $inventory = $this->transactionItemRepository->inventory($data);

        // Generate a unique filename with the current timestamp
        $fileName = 'inventory_' . $warehouseName . '_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
        $filePath = 'public/keeper/exports/inventory/' . $fileName;

        $export=new InventoryExport($inventory);
        // Store the file
        Excel::store($export, $filePath);

        return response()->json([
            'message' => __('File exported and saved successfully!'),
            'file_name' => $fileName,
            'file_url' => Storage::disk('public')->url($filePath)
        ]);
    }

}
