<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DoctorAdviceNotification extends Notification
{
    use Queueable;

    private $alert;
    private $doctor;

    public function __construct($alert, $doctor)
    {
        $this->alert = $alert;
        $this->doctor = $doctor;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "🩺 Doctor has replied to Alert #{$this->alert->id}",
            'details' => "Dr. {$this->doctor->name} has provided advice for Animal: " . ($this->alert->animal ? $this->alert->animal->animal_id : 'N/A'),
            'link' => '/alerts',
            'type' => 'doctor_advice',
            'alert_id' => $this->alert->id
        ];
    }
}
