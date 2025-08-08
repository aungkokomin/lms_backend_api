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

class StudentGrantNotification extends Notification
{
    use Queueable;

    /**
     * The user grant code.
     */
    protected $userGrantCode;
    protected $message;
    protected $user_id;
    /**
     * Create a new notification instance.
     */
    public function __construct($userGrantCode, $message, $user_id)
    {
        $this->userGrantCode = $userGrantCode;
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
        return ['database', 'mail', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Congratulation! Your grant application has been approved')
            ->line('Your grant application has been approved. Please use the following code to redeem your grant.')
            ->line("Code: {$this->userGrantCode->code}")
            ->line('Thank you for using our LMS!')
            ->salutation('Phenomenon Based LMS');
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
            'title' => 'Grant Application Approved',
            'type' => 'student_grant',
            'message' => $this->message,
        ];
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
}
