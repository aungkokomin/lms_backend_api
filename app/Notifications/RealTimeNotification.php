<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RealTimeNotification extends Notification
{
    use Queueable;

    protected $message;

    protected $user_id;
    /**
     * Create a new notification instance.
     */
    public function __construct($message,$user_id)
    {
        //
        $this->message = $message;
        $this->user_id = $user_id;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['broadcast','database'];
    }

    /**
     * Broadcast the message
     * 
     * @return BroadcastMessage
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'noti_count' => User::find($this->user_id)->unreadNotifications->count(),
            'message' => $this->message,
        ]);
    }

    /**
     * Get the broadcast channel the notification should be sent on.
     *
     * @return \Illuminate\Broadcasting\Channel
     */
    public function broadcastOn()
    {
        // Customize the channel name here
        return new Channel('lms-user-channel-' . $this->user_id);
    }

    /**
     * Get the broadcast event name
     * 
     * @return string
     */
    public function broadcastAs()
    {
        return 'my-event';
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
        return [
            //
            'title' => 'Test Notification',
            'type' => 'test',
            'message' => $this->message,
        ];
    }
}
