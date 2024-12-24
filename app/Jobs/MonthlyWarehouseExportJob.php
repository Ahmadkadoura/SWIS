<?php

namespace App\Jobs;

use App\Http\Controllers\Api\Admin\WarehouseController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MonthlyWarehouseExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private WarehouseController $WarehouseController;

    /**
     * Create a new job instance.
     */
    public function __construct(WarehouseController $warehouseController)
    {
        $this->WarehouseController=$warehouseController;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {

            // Call the donorExport method
            $response = $this->WarehouseController->exportAndSave();

            // You can process the response or log success
            Log::info('warehouse export successful.', ['response' => $response->getContent()]);
        } catch (\Exception $e) {
            Log::error('Error during donor export.', ['error' => $e->getMessage()]);
        }
    }
}
