<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AlertUpdateNotification extends Notification
{
    use Queueable;

    protected $message;
    protected $url;
    protected $type;

    /**
     * Create a new notification instance.
     */
    public function __construct($message, $url = '#', $type = 'info')
    {
        $this->message = $message;
        $this->url = $url;
        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => $this->message,
            'url' => $this->url,
            'type' => $this->type,
            'icon' => $this->getIcon($this->type)
        ];
    }

    private function getIcon($type)
    {
        switch ($type) {
            case 'success': return 'solar:check-circle-bold';
            case 'warning': return 'solar:bell-bold';
            case 'danger': return 'solar:danger-circle-bold';
            default: return 'solar:info-circle-bold';
        }
    }
}
