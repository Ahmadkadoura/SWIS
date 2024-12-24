<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Repositories\baseRepository;
use App\Http\Repositories\branchRepository;
use App\Http\Requests\Branch\StoreBranchRequest;
use App\Http\Requests\Branch\UpdateBranchRequest;
use App\Http\Resources\BranchResource;
use App\Http\Resources\indexMainBranchResource;
use App\Models\Branch;
use Illuminate\Http\Request;

use Illuminate\Http\JsonResponse;

class BranchController extends Controller
{
    private branchRepository $branchRepository;

    public function __construct(branchRepository $branchRepository)
    {
        $this->branchRepository =$branchRepository;
        $this->middleware(['auth:sanctum', 'Localization']);
//        $this->middleware(['permission:Admin']);

    }
    public function index(): JsonResponse
    {
        $data = $this->branchRepository->index();
        $message = $data->isEmpty() ? __('There are no branches at the moment') : __('Branches retrieved successfully');

        return $this->showAll($data, BranchResource::class, $message);
    }


    public function show(Branch $branch): JsonResponse
    {
        return $this->showOne($branch,BranchResource::class);
    }


    public function store(StoreBranchRequest $request): JsonResponse
    {
        $newData=$request->validated();
            $data=$this->branchRepository->create($newData);
        return $this->showOne($data['Branch'],BranchResource::class,__($data['message']));

    }

    public function update(UpdateBranchRequest $request, Branch $branch): JsonResponse
    {
        $newData=$request->validated();

            $data = $this->branchRepository->update($newData, $branch);
        return $this->showOne($data['Branch'],BranchResource::class,__($data['message']));

    }

    public function destroy(Branch $branch)
    {

            $data = $this->branchRepository->destroy($branch);
        return [__($data['message']),$data['code']];

    }

    public function showDeleted()
    {
        $data=$this->branchRepository->showDeleted();
        return  BranchResource::collection($data['Branch']);

    }
    public function restore(Request $request){

        $data = $this->branchRepository->restore($request);
        return [__($data['message']),$data['code']];
    }
}
