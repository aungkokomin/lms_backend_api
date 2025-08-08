<?php

namespace App\Notifications;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuizReattemptAppealNotification extends Notification
{
    use Queueable;

    protected $status;
    protected $lesson_title;
    protected $user;
    protected $student;
    /**
     * Create a new notification instance.
     */
    public function __construct($status, $lesson_title, $user,$student)
    {
        $this->status = $status;
        $this->lesson_title = $lesson_title;
        $this->user = $user;
        $this->student = $student;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail','database','broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $studentName = $this->student->full_name ?? $this->user->name;
        if($this->status == 'approved'){
            return (new MailMessage)
                ->subject('Quiz Reattempt Appeal Approved')
                ->greeting('Hello '.$studentName)
                ->line('Your request for reattempting the quiz for the lesson "'.$this->lesson_title.'" has been approved.')
                ->line('You can now reattempt the quiz by following the link below.')
                ->action('Reattempt Quiz', config('services.frontend.url'))
                ->line('Thank you for using our application!')
                ->salutation('Phenomenon Based LMS');
        }else{
            return (new MailMessage)
                ->subject('Quiz Reattempt Appeal Rejected')
                ->greeting('Hello '.$studentName)
                ->line('Your request for reattempting the quiz for the lesson '.$this->lesson_title.' has been rejected.')
                ->line('You can contact your instructor for further assistance.')
                ->line('Thank you for using our application!')
                ->salutation('Phenomenon Based LMS');
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        if($this->status == 'approved'){
            $message = 'Your request for reattempting the quiz for the lesson "'.$this->lesson_title.'" has been approved.';
        }else{
            $message = 'Your request for reattempting the quiz for the lesson "'.$this->lesson_title.'" has been rejected.';
        }
        return [
            //
            'title' => 'Quiz Reattempt Appeal',
            'type' => 'quiz_reattempt_appeal',
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
        if($this->status == 'approved'){
            $message = 'Your request for reattempting the quiz for the lesson "'.$this->lesson_title.'" has been approved.';
        }else{
            $message = 'Your request for reattempting the quiz for the lesson "'.$this->lesson_title.'" has been rejected.';
        }
        return new BroadcastMessage([
            'noti_count' => $notifiable->unreadNotifications->count() + 1,
            'message' => $message,
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
        return new Channel('lms-user-channel-'.$this->user->id);
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
