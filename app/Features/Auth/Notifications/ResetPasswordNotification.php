<?php

namespace App\Features\Auth\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sends the password reset link.
 *
 * The token itself is never included: the link carries Laravel's signed,
 * hashed token, so a forwarded email cannot be replayed to guess a valid one.
 */
class ResetPasswordNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly string $url) {}

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
            ->subject('পাসওয়ার্ড রিসেট — রেশমা ইন্টারন্যাশনাল স্কুল')
            ->greeting('আসসালামু আলাইকুম,')
            ->line('আপনার অ্যাকাউন্টের পাসওয়ার্ড রিসেট করার অনুরোধ পেয়েছি।')
            ->action('পাসওয়ার্ড রিসেট করুন', $this->url)
            ->line('এই লিংক ৬০ মিনিট পর্যন্ত কার্যকর থাকবে।')
            ->line('আপনি এই অনুরোধটি করেননি, তাহলে এই ইমেইলটি উপেক্ষা করুন।');
    }
}
