<?php

namespace App\Http\Controllers\Api\Keeper;

use App\Http\Controllers\Controller;
use App\Http\Repositories\transactionRepository;
use App\Http\Requests\Transaction\StoreTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Http\services\QRCodeService;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\TransactionCreated;
use App\Traits\FileUpload;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    use FileUpload;

    private transactionRepository $transactionRepository;
    private QRCodeService $qrCodeService;

    public function __construct(transactionRepository $transactionRepository,QRCodeService $qrCodeService){
         $this->transactionRepository=$transactionRepository;
         $this->qrCodeService = $qrCodeService;

         $this->middleware(['auth:sanctum', 'Localization']);
//         $this->middleware(['permission:Keeper']);
     }
     public function index(): JsonResponse
     {
         $data=$this->transactionRepository->indexTransactionForKeeper(Auth::user()->id);
         $message = $data->isEmpty() ? __('There are no Item at the moment') : __('Item retrieved successfully');

         return $this->showAll($data, TransactionResource::class, $message);

     }

    public function show($transaction_id): JsonResponse
    {
        $data=$this->transactionRepository->showTransactionForKeeper($transaction_id);
        return $this->showOne($data['Transaction'],TransactionResource::class,__($data['message']));
    }

    public function store(StoreTransactionRequest $request): JsonResponse
    {
        $dataItem = $request->validated();

        // Handle waybill image upload
        if ($request->hasFile('waybill_img')) {
            $file = $request->file('waybill_img');
            $fileName = 'Transaction/waybill_Images/' . $file->hashName();
            $dataItem['waybill_img'] = $this->createFile($file, Transaction::getDisk(), filename: $fileName);
        }

        // Create transaction
        $transactionData = $this->transactionRepository->create($dataItem);

        // Generate QR code
        $imagePath = $this->qrCodeService->generateQRCode($transactionData['Transaction']);
        $dataItem['qr_code'] = $imagePath;

        // Update transaction with QR code
        $transaction = $this->transactionRepository->update($dataItem, $transactionData['Transaction']);

        // Send notification to admins if necessary
            $admin = User::role('admin')->first();
            $admin->notify(new TransactionCreated($transaction['Transaction'], $admin));


        return $this->showOne($transaction['Transaction'], TransactionResource::class, __($transactionData['message']));
    }
}
