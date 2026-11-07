<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssetAlertNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $subject,
        public string $message,
        public string $url,
        public string $alertKey,
        public string $kind
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('AssetFlow alert: '.$this->subject)
            ->greeting('Hello '.$notifiable->name.',')
            ->line($this->message)
            ->action('Open AssetFlow', url($this->url))
            ->line('You are receiving this because you have an authorized AssetFlow account.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'subject' => $this->subject,
            'message' => $this->message,
            'url' => $this->url,
            'alert_key' => $this->alertKey,
            'kind' => $this->kind,
        ];
    }
}
