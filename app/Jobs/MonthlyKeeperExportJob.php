<?php

namespace App\Jobs;

use App\Http\Controllers\Api\Admin\UserController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MonthlyKeeperExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private UserController $userController;

    /**
     * Create a new job instance.
     */
    public function __construct(UserController $userController)
    {
        $this->userController = $userController;
    }


    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Instantiate the controller


            // Call the donorExport method
            $response = $this->userController->keeperExport();

            // You can process the response or log success
            Log::info('Keeper export successful.', ['response' => $response->getContent()]);
        } catch (\Exception $e) {
            Log::error('Error during donor export.', ['error' => $e->getMessage()]);
        }
    }
}
