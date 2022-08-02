<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ForgetPasswordNotification extends Notification
{
    use Queueable;

    private $user;
    private $resetPassword;

    /**
     * UserCreateNotification constructor.
     */
    public function __construct($user, $resetPassword)
    {
        $this->user = $user;
        $this->resetPassword = $resetPassword;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Restore password')
            ->markdown('mails.forget-password', ['user' => $this->user, 'resetPassword' => $this->resetPassword]);
    }
}
