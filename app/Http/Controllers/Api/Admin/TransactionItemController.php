<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\transactionType;
use App\Exports\InventoryExport;
use App\Http\Controllers\Controller;
use App\Http\Repositories\transactionItemRepository;
use App\Http\Requests\Transaction\storeTransactionWarehouseRequest;
use App\Http\Requests\Transaction\UpdateTransactionWarehouseRequest;
use App\Http\Resources\InventoryResource;
use App\Http\Resources\transactionItemResource;
use App\Models\TransactionItem;
use App\Models\Warehouse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class TransactionItemController extends Controller
{
    private transactionItemRepository $transactionItemRepository;

    public function __construct(transactionItemRepository $transactionItemRepository)
    {
        $this->transactionItemRepository =$transactionItemRepository;
        $this->middleware(['auth:sanctum', 'Localization']);
//        $this->middleware(['permission:Admin']);

    }

    public function index(): JsonResponse
    {

        $data = $this->transactionItemRepository->index();
        return $this->showAll($data['TransactionWarehouseItem'], transactionItemResource::class, __($data['message']));

    }

    public function show(TransactionItem $transactionItem): JsonResponse
    {

        return $this->showOne($transactionItem, transactionItemResource::class);

    }

    public function store(storeTransactionWarehouseRequest $request): JsonResponse
    {
        $dataItem = $request->validated();

        $data = $this->transactionItemRepository->create($dataItem);
        return $this->showOne($data['TransactionWarehouseItem'], transactionItemResource::class, __($data['message']));

    }

    public function update(UpdateTransactionWarehouseRequest $request, TransactionItem $transactionItem): JsonResponse
    {
        $dataItem = $request->validated();

        $data = $this->transactionItemRepository->update($dataItem, $transactionItem);

        return $this->showOne($data['TransactionWarehouseItem'], transactionItemResource::class, __($data['message']));

    }


    public function destroy(TransactionItem $transactionItem)
    {
        $data = $this->transactionItemRepository->destroy($transactionItem);
        return [__($data['message']), $data['code']];

    }

    public function showDeleted(): JsonResponse
    {
        $data=$this->transactionItemRepository->showDeleted();
        return $this->showAll($data['TransactionWarehouseItem'],transactionItemResource::class,__($data['message']));
    }
    public function restore(Request $request){

        $data = $this->transactionItemRepository->restore($request);
        return [__($data['message']),$data['code']];
    }
    public function inventoryForWarehouse(Request $request): JsonResponse
    {
        $request->validate([
            'warehouse_id' => 'required|integer',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);
        $data = [
            'warehouse_id' => $request->input('warehouse_id'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date')
        ];
        $inventory=$this->transactionItemRepository->inventory($data);

        return $this->showOneCollection($inventory, InventoryResource::class);
    }

    public function systemInventory(Request $request): JsonResponse
    {
        $request->validate([

            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $data = [
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ];

        $inventory = $this->transactionItemRepository->systemInventory($data);

        return $this->showOneCollection($inventory, InventoryResource::class);
    }

    public function exportInventory(Request $request)
    {
        $request->validate([
            'warehouse_id' => 'required|integer',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        $keeper = Warehouse::where('id',$request->warehouse_id)->first();
        $warehouseName = Str::slug($keeper->name); // Convert name to a slug-friendly format

        $data = [
            'warehouse_id' => $keeper->id,
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date')
        ];

        $inventory = $this->transactionItemRepository->inventory($data);

        // Generate a unique filename with the current timestamp
        $fileName = 'inventory_' . $warehouseName . '_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
        $filePath = 'public/exports/inventory/' . $fileName;

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
