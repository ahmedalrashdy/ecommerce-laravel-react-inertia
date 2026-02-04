<?php

namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public const CONTEXT_REGISTRATION = 'registration';

    public const CONTEXT_LOGIN = 'login';

    /**
     * Create a new notification instance.
     */
    public function __construct(public string $context)
    {
        $this->afterCommit();
    }

    public function context(): string
    {
        return $this->context;
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
        $appName = (string) config('app.name');
        $isRegistration = $this->context === self::CONTEXT_REGISTRATION;

        return (new MailMessage)
            ->subject($isRegistration ? "مرحباً بك في {$appName}" : "مرحباً بعودتك إلى {$appName}")
            ->greeting("مرحباً {$notifiable->name}")
            ->line($isRegistration ? 'سعداء بانضمامك إلينا. تم إنشاء حسابك بنجاح.' : 'سعداء بعودتك. تم تسجيل دخولك بنجاح.')
            ->action('تصفح المتجر', route('store.home'))
            ->line('إذا احتجت أي مساعدة، يسعدنا خدمتك في أي وقت.');
    }
}
