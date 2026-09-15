<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Permintaan Reset Password')
                    ->greeting('Halo!')
                    ->line('Kami menerima permintaan untuk meriset password akun Anda.')
                    ->line('Kode Token Rahasia Anda adalah:')
                    ->line($this->token)
                    ->line('Jika Anda tidak meminta reset password, abaikan saja email ini.');
    }
}