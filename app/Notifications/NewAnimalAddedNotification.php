<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewAnimalAddedNotification extends Notification
{
    use Queueable;

    public $animal;

    public function __construct($animal)
    {
        $this->animal = $animal;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_animal',
            'message' => 'New Animal Registered: ' . $this->animal->tag_number,
            'details' => $this->animal->breed . ' - ' . $this->animal->gender . ' (ID: ' . $this->animal->animal_id . ')',
            'link' => route('dashboard'),
            'animal_id' => $this->animal->id
        ];
    }
}
