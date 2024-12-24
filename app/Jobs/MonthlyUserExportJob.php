<?php

namespace App\Jobs;

use App\Http\Controllers\Api\Admin\UserController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MonthlyUserExportJob implements ShouldQueue
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


            // Call the donorExport method
            $response = $this->userController->allUsersExport();

            // You can process the response or log success
            Log::info('User export successful.', ['response' => $response->getContent()]);
        } catch (\Exception $e) {
            Log::error('Error during donor export.', ['error' => $e->getMessage()]);
        }
    }
}
