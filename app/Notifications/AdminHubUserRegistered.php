<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminHubUserRegistered extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public string $password
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject('帳戶建立通知信')
            ->line("感謝您的註冊，您的登入臨時密碼為：{$this->password}")
            ->line('請由以下網址登入並立即修改您的密碼。')
            ->action('登入', url('admin/login'))
            ->line('如果您未要求重設密碼，請忽略此郵件。');
    }
}
