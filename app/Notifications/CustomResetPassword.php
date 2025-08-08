<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;

class CustomResetPassword extends ResetPasswordNotification
{
    use Queueable;

    public $token;

    /**
     * Create a new notification instance.
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        // $resetUrl = url(route('password-reset', [
        //     'token' => $this->token,
        //     'email' => $notifiable->getEmailForPasswordReset(),
        // ]));

        $resetUrl = config('services.frontend.url')."/auth/reset/password?token=".$this->token."&email=".$notifiable->getEmailForPasswordReset()."";
        // env("FRONTEND_URL").

        return (new MailMessage)
            ->subject(Lang::get('Password Reset Link'))
            ->line(Lang::get('You are receiving this email because we received a password reset request for your account.'))
            ->action(Lang::get('Reset Password'), $resetUrl)
            ->line(Lang::get('This password reset link will expire in :count minutes.', ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')]))
            ->line(Lang::get('If you did not request a password reset, no further action is required.'));
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
            'data' => 'We had received a password reset request for your account.',
            'email' => $notifiable->getEmailForPasswordReset(),
        ];
    }
}
