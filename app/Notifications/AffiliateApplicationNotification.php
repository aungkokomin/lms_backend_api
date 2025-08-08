<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AffiliateApplicationNotification extends Notification
{
    use Queueable;

    protected $affiliate;

    /**
     * Create a new notification instance.
     */
    public function __construct($affiliate)
    {
        //
        $this->affiliate = $affiliate;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database','broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $name = $this->affiliate->full_name ?? null;
        if($name){
            $message = $name.' has submitted an affiliate application.';
        }else{
            $message = 'An affiliate application has been submitted.';
        }

        return [
            //
            'title' => 'Affiliate Application Received',
            'type' => 'affiliate_application',
            'message' => $message,
        ];
    }

    /**
     * Broadcast the message
     * 
     * @return BroadcastMessage
     */
    public function toBroadcast($notifiable)
    {
        if($this->affiliate->name){
            $message = $this->affiliate->name.' has submitted an affiliate application.';
        } else{
            $message = 'An affiliate application has been submitted.';
        }
        return new BroadcastMessage([
            'noti_count' => $notifiable->unreadNotifications->count() + 1,
            'message' => $message,
        ]);
    }
}
