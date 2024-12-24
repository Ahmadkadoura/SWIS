<?php

namespace App\Console;

use App\Jobs\MonthlyAdminInventoryExportJob;
use App\Jobs\MonthlyCompletedTransactionExportJob;
use App\Jobs\MonthlyDonorExportJob;
use App\Jobs\MonthlyInDeliveryTransactionExportJob;
use App\Jobs\MonthlyKeeperExportJob;
use App\Jobs\MonthlyUserExportJob;
use App\Jobs\MonthlyWarehouseExportJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->job(MonthlyDonorExportJob::class)->everyMinute();
        $schedule->job(MonthlyKeeperExportJob::class)->everyMinute();
        $schedule->job(MonthlyUserExportJob::class)->everyMinute();
        $schedule->job(MonthlyWarehouseExportJob::class)->everyMinute();
        $schedule->job(MonthlyCompletedTransactionExportJob::class)->everyMinute();
        $schedule->job(MonthlyInDeliveryTransactionExportJob::class)->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
