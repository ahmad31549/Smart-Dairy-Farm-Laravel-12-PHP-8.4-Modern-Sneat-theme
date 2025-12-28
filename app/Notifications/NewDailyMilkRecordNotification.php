<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewDailyMilkRecordNotification extends Notification
{
    use Queueable;

    public $record;

    public function __construct($record)
    {
        $this->record = $record;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'milk_entry',
            'message' => 'New Daily Milk Record Added: ' . $this->record->total_milk_quantity . ' Liters',
            'details' => 'By ' . ($this->record->recorder->name ?? 'Worker'),
            'link' => route('dashboard'),
            'record_id' => $this->record->id
        ];
    }
}
