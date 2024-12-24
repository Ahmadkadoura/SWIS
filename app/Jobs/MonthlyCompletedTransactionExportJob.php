<?php

namespace App\Jobs;

use App\Http\Controllers\Api\Admin\TransactionController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MonthlyCompletedTransactionExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private TransactionController $TransactionController;

    /**
     * Create a new job instance.
     */
    public function __construct(TransactionController $transactionController)
    {
        $this->TransactionController=$transactionController;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {


            // Call the donorExport method
            $response = $this->TransactionController->transactionCompletedExport();

            // You can process the response or log success
            Log::info('Completed transaction export successful.', ['response' => $response->getContent()]);
        } catch (\Exception $e) {
            Log::error('Error during donor export.', ['error' => $e->getMessage()]);
        }
    }
}
