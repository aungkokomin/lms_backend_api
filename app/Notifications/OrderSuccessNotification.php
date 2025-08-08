<?php

namespace App\Notifications;

use Broadcast;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderSuccessNotification extends Notification
{
    use Queueable;

    protected $transaction_id;
    
    protected $amountPaid;

    protected $paymentDate;

    protected $message;
    protected $user_id;
    /**
     * Create a new notification instance.
     */
    public function __construct($payment,$user_id)
    {
        //
        $this->transaction_id = $payment->transaction_id;
        $this->amountPaid = $payment->amount;
        $this->paymentDate = $payment->payment_date;
        $this->user_id = $user_id;
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
        return (new MailMessage)
            ->subject('Payment Successful – Thank You!')
            ->greeting('Hi ' . $notifiable->name . ',')
            ->line('We’re happy to let you know that your payment was successfully processed!')
            ->line('**Order Details:**')
            ->line('Order Number: ' . $this->transaction_id)
            ->line('Amount Paid: ' . $this->amountPaid)
            ->line('Date: ' . $this->paymentDate)
            ->line('Your order is now being processed, and we’ll keep you updated on the next steps.')
            ->line('If you have any questions, feel free to reach out to our support team.')
            ->line('Thank you for choosing us!')
            ->salutation(config('app.name'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $message = 'Your payment for order number '.$this->transaction_id.' was successful. You paid an amount of '.$this->amountPaid.' on '.$this->paymentDate;
        return [
            //
            'title' => 'Payment Successful',
            'type' => 'order_success',
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
        return new BroadcastMessage([
            'noti_count' => $notifiable->unreadNotifications->count() + 1,
            'order_number' => $this->transaction_id,
            'amount_paid' => $this->amountPaid,
            'payment_date' => $this->paymentDate,
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
