<?php

namespace App\Notifications;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;

class StudentRegistrationNotification extends Notification
{
    use Queueable;

    /**
     * The user instance that has been updated.
     */
    protected $studentName;
    protected $message;
    protected $user_id;
    protected $user;

    /**
     * Create a new notification instance.
     */
    public function __construct($message,$user,$student)
    {
        //
        $this->studentName = $student->full_name ?? $user->name;
        $this->message = $message;
        $this->user_id = $user->id;
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database','mail','broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable)
    {
        return (new MailMessage)
            ->subject(Lang::get('Welcome to Maal Datalab – Student Registration Successful'))
            ->greeting(Lang::get('Hi, ' . $this->studentName . '!'))
            ->line(Lang::get('We are pleased to inform you that you have successfully registered as a student.You can now access the course materials and start learning from this link:'))
            ->action(Lang::get('Start Learning'), config('services.frontend.url'))
            ->line(Lang::get('Please find attached the student handbook for your reference.'))
            ->line(Lang::get('If you have any questions or need assistance, please do not hesitate to contact us.'))
            ->line(Lang::get('Thank you,'))
            ->salutation(Lang::get('LMS Support Team'))
            ->attach(public_path().'/handbook_pdfs/Latest_Student_Handbook.pdf', [
                'as' => 'MAAL Student Handbook.pdf',
                'mime' => 'application/pdf',
            ]);

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
            'title' => 'Student Registration Successful',
            'type' => 'student_registration',
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
            'noti_count' => $this->user->unreadNotifications->count(),
            'message' => $this->message,
            'user' => $this->user,
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
        return new Channel('lms-user-channel-'.$this->user_id);
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
