<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class JobCompletedNotification extends Notification
{
    use Queueable;

    protected $jobName;

    /**
     * Create a new notification instance.
     *
     * @param string $jobName
     */
    public function __construct(string $jobName)
    {
        $this->jobName = $jobName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database']; // Use database channel for notifications
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'job_name' => $this->jobName,
            'message' => 'export file has been completed successfully from The job ' . $this->jobName . '.',
        ];
    }
}

