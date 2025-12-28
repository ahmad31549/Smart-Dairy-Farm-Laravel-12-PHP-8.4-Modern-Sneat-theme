<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class EmergencyAlertNotification extends Notification
{
    use Queueable;

    private $alert;
    private $animal;
    private $worker;

    public function __construct($alert, $animal = null, $worker = null)
    {
        $this->alert = $alert;
        $this->animal = $animal;
        $this->worker = $worker;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $animalInfo = $this->animal ? "Animal: {$this->animal->animal_id}" : "General Alert";
        $workerName = $this->worker ? $this->worker->name : "Farm Worker";
        
        return [
            'message' => "🚨 Emergency Alert from {$workerName}",
            'details' => $animalInfo . " - " . \Illuminate\Support\Str::limit($this->alert->message, 60),
            'link' => '/alerts',
            'type' => 'emergency_alert',
            'alert_id' => $this->alert->id
        ];
    }
}
