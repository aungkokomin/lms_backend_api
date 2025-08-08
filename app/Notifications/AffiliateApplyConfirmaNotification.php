<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AffiliateApplyConfirmaNotification extends Notification
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
        return ['mail', 'database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        logger('AffiliateApplyConfirmaNotification toMail called affiliate: ', [$this->affiliate->name]);
        return (new MailMessage)
                    ->subject('Affiliate Application Approved')
                    ->greeting('Hello ' . $this->affiliate->name . ',')
                    ->line('Congratulations! Your affiliate application has been approved.')
                    ->line('Please click the button below to view your affiliate dashboard.')
                    ->action('View Affiliate Dashboard', url('/affiliate/dashboard'))
                    ->line('Thank you for being a part of our affiliate program!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        logger('AffiliateApplyConfirmaNotification toArray called affiliate: ', [$this->affiliate->name]);
        $name = $this->affiliate->full_name ?? null;
        if($name){
            $message = $name.' \'s affiliate application has approved.';
        }else{
            $message = 'An affiliate application has been approved.';
        }

        return [
            //
            'title' => 'Affiliate Application Approved',
            'type' => 'affiliate_confirmation',
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
