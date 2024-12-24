<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\transactionStatusType;
use App\Enums\userType;
use App\Exports\TransactionsExport;
use App\Http\Controllers\Controller;
use App\Http\Repositories\QRImageWithLogo;
use App\Http\Repositories\transactionRepository;
use App\Http\Requests\Transaction\StoreTransactionRequest;
use App\Http\Requests\Transaction\UpdateTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\DonorTransactionResource;
use App\Http\Responses\Response;
use App\Http\services\FilterService;
use App\Http\services\QRCodeService;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\TransactionCreated;
use App\Services\TransactionService;
use App\Traits\FileUpload;
use App\Traits\QrCodeHelper;
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Data\QRMatrix;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class TransactionController extends Controller
{
    use FileUpload;
    private transactionRepository $transactionRepository;
    private QRCodeService $qrCodeService;

    public function __construct(transactionRepository $transactionRepository,QRCodeService $qrCodeService)
    {
        $this->transactionRepository = $transactionRepository;
        $this->qrCodeService = $qrCodeService;
        $this->middleware(['auth:sanctum', 'Localization']);
//        $this->middleware(['permission:Admin']);
//        $this->middleware(['permission:Keeper'])->only(['store']);
//        $this->middleware(['permission:Donor'])->only(['store']);

    }
    public function index(): JsonResponse
    {
        $data = $this->transactionRepository->index();
        $message = $data->isEmpty() ? __('There are no Item at the moment') : __('Item retrieved successfully');

        return $this->showAll($data, TransactionResource::class, $message);
    }

    public function show(Transaction $transaction): JsonResponse
    {

        return $this->showOne($transaction,TransactionResource::class);

    }
//    public function store(StoreTransactionRequest $request): JsonResponse
//    {
//        $dataItem = $request->validated();
//
//        // Handle waybill image upload
//        if ($request->hasFile('waybill_img')) {
//            $file = $request->file('waybill_img');
//            $fileName = 'Transaction/waybill_Images/' . $file->hashName();
//            $dataItem['waybill_img'] = $this->createFile($file, Transaction::getDisk(), filename: $fileName);
//        }
//
//        // Create transaction
//        $transactionData = $this->transactionRepository->create($dataItem);
//
//        // Generate QR code
//        $imagePath = $this->qrCodeService->generateQRCode($transactionData['Transaction']);
//        $dataItem['qr_code'] = $imagePath;
//
//        // Update transaction with QR code
//        $transaction = $this->transactionRepository->update($dataItem, $transactionData['Transaction']);
//
//        // Send notification to admins if necessary
//        if (!Auth::user()->hasRole('admin')) {
//            $admin = User::role('admin')->first();
//            $admin->notify(new TransactionCreated($transaction['Transaction'], $admin));
//        }
//
//        return $this->showOne($transaction['Transaction'], TransactionResource::class, $transactionData['message']);
//    }


    public function update(UpdateTransactionRequest $request,Transaction $transaction): JsonResponse
    {
        $dataItem=$request->validated();
        if ($request->hasFile('waybill_img')) {
            $file = $request->file('waybill_img');
            $name ='Transaction/'.'waybill_Images/' . $file->hashName() ;
            $imagePath = $this->createFile($request->file('waybill_img'), Transaction::getDisk(),filename:  $name);
            $dataItem['waybill_img'] = $imagePath;
        }
        $data = $this->transactionRepository->update($dataItem, $transaction);
        return $this->showOne($data['Transaction'],TransactionResource::class,__($data['message']));

    }


    public function destroy(Transaction $transaction)
    {
        $data = $this->transactionRepository->destroy($transaction);
        return [__($data['message']),$data['code']];

    }

    public function showDeleted(): JsonResponse
    {
        $data=$this->transactionRepository->showDeleted();
        return $this->showAll($data['Transaction'],TransactionResource::class,__($data['message']));
    }
    public function restore(Request $request){

        $data = $this->transactionRepository->restore($request);
        return [__($data['message']),$data['code']];
    }
    public function transactionCompletedExport()
    {
        // Define the file name and path
        $fileName = 'Completed_transaction_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
        $filePath = 'public/exports/transactions/' . $fileName;

        $users = Transaction::where('status', transactionStatusType::COMPLETED->value)->get();

        $export = new TransactionsExport($users);

        Excel::store($export, $filePath);

        return response()->json([
            'message' => __('File exported and saved successfully!'),
            'file_name' => $fileName,
            'file_url' =>  Storage::disk('public')->url($filePath)
        ]);
    }
    public function transactionInDeliveryExport()
    {
        // Define the file name and path
        $fileName = 'InDelivery_transaction_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
        $filePath = 'public/exports/transactions/' . $fileName;

        $users = Transaction::where('status', transactionStatusType::InDelivery->value)->get();

        $export = new TransactionsExport($users);

        Excel::store($export, $filePath);

        return response()->json([
            'message' => __('File exported and saved successfully!'),
            'file_name' => $fileName,
            'file_url' =>  Storage::disk('public')->url($filePath)
        ]);
    }


}
