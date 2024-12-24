<?php

namespace App\Providers;

use App\Enums\userType;
use App\Notifications\JobCompletedNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use App\Models\Branch;
use App\Models\Item;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Warehouse;
use App\Observers\BranchObserver;
use App\Observers\ItemObserver;
use App\Observers\TransactionObserver;
use App\Observers\UserObserver;
use App\Observers\WarehouseObserver;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        User::observe(UserObserver::class);
        Warehouse::observe(WarehouseObserver::class);
        Branch::observe(BranchObserver::class);
        Item::observe(ItemObserver::class);
        Transaction::observe(TransactionObserver::class);
        Event::listen(JobProcessed::class, function (JobProcessed $event) {
            $admin = User::where('type', userType::admin->value)->first();
            if ($admin) {
                Notification::send($admin, new JobCompletedNotification($event->job->resolveName()));
            }
        });

    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
