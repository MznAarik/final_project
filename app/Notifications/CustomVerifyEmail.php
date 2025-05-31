<?php


namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class CustomVerifyEmail extends Notification
{
    use Queueable;

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addHours(24),
            ['id' => $notifiable->getKey(), 'hash' => sha1($notifiable->getEmailForVerification())]
        );

        $data = [
            'name' => $notifiable->name,
            'email' => $notifiable->email,
            'verificationUrl' => $verificationUrl,
            'imageUrl' => 'https://res.cloudinary.com/dzglpqsfi/image/upload/v1747669702/dancin-monkey_fe5hy2.gif',
        ];

        Log::info('Sending email with data: ', $data); // Debug log

        return (new MailMessage)
            ->subject('Verify Your Email Address')
            ->view('emails.verify-email', ['data' => $data]); // Explicitly name the array as 'data'
    }
}