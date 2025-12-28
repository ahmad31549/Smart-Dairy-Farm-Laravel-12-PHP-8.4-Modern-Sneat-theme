<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DoctorForwardNotification extends Notification
{
    use Queueable;

    private $alert;
    private $sender;

    public function __construct($alert, $sender)
    {
        $this->alert = $alert;
        $this->sender = $sender;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "🏥 New Case Forwarded by {$this->sender->name}",
            'details' => "Priority Case: " . ($this->alert->animal ? "Animal ID: " . $this->alert->animal->animal_id : "General Emergency"),
            'link' => '/alerts',
            'type' => 'doctor_forward',
            'alert_id' => $this->alert->id
        ];
    }
}
