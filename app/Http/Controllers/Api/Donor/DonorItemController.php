<?php

namespace App\Http\Controllers\Api\Donor;

use App\Http\Controllers\Controller;
use App\Http\Repositories\donorItemRepository;
use App\Http\Repositories\itemRepository;
use App\Http\Requests\Items\storeItemsRequests;
use App\Http\Resources\indexItemForDonerResource;
use App\Http\Resources\itemsResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DonorItemController extends Controller
{
    private donorItemRepository $donorItemRepository;
    private itemRepository $itemRepository;

    public function __construct(donorItemRepository $donorItemRepository,itemRepository $itemRepository){
        $this->donorItemRepository=$donorItemRepository;
        $this->itemRepository = $itemRepository;

        $this->middleware(['auth:sanctum']);
//        $this->middleware(['permission:Donor'])->only(['index','show']);
    }
    public function index(): JsonResponse
    {
        $data = $this->donorItemRepository->indexItemForDonor(Auth::user()->id);
        return $this->showAll($data['donorItem'],indexItemForDonerResource::class,__($data['message']));
    }
    public function show($item_id): JsonResponse
    {
        $data = $this->donorItemRepository->showItemForDonor(Auth::user()->id,$item_id);
        return $this->showOne($data['donorItem'],indexItemForDonerResource::class,__($data['message']));
    }
    public function store(storeItemsRequests $request): JsonResponse
    {
        $dataItem=$request->validated();

        $data=$this->itemRepository->create($dataItem);
        return $this->showOne($data['Item'],itemsResource::class,__($data['message']));

    }

}
