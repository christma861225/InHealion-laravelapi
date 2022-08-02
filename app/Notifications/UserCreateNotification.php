<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserCreateNotification extends Notification
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
            ->subject('New account')
            ->markdown('mails.user-create', ['user' => $this->user, 'resetPassword' => $this->resetPassword]);
    }
}
