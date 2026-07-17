<?php

namespace App\Notifications;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvitationCreated extends Notification
{
    use Queueable;

    public function __construct(public Invitation $invitation) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('You have been invited')
            ->greeting('Hello!')
            ->line("You have been invited to join {$this->invitation->company->name} as an {$this->invitation->role}.")
            ->action('Accept invitation', route('invitations.accept', $this->invitation->token))
            ->line('This invitation expires in 7 days.');
    }
}
