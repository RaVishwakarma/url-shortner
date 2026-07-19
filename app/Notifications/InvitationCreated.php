<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvitationCreated extends Notification
{
    use Queueable;

    public $invitation;

    public function __construct($invitation)
    {
        $this->invitation = $invitation;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('You have been invited')
            ->greeting('Hello!')
            ->line("You have been invited to join {$this->invitation->company_name} as an {$this->invitation->role}.")
            ->action('Accept invitation', route('invitations.accept', $this->invitation->token))
            ->line('This invitation expires in 7 days.');
    }
}
