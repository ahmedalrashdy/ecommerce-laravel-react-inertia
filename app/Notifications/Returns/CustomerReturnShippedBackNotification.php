<?php

namespace App\Notifications\Returns;

use App\Data\Notifications\Returns\ReturnShippedBackData;
use App\Models\ReturnOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomerReturnShippedBackNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public ReturnOrder $returnOrder)
    {
        $this->afterCommit();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Determine which queues should be used for each notification channel.
     *
     * @return array<string, string>
     */
    public function viaQueues(): array
    {
        $queueName = (string) config('queue.queues.orders_notifications', 'orders-notifications-high');

        return [
            'database' => $queueName,
            'mail' => $queueName,
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $orderNumber = $this->returnOrder->order?->order_number ?? '—';
        $returnUrl = route('store.account.returns.show', $this->returnOrder);
        $appName = (string) config('app.name');

        return (new MailMessage)
            ->subject("تم شحن المرتجع #{$this->returnOrder->return_number}")
            ->greeting("مرحباً {$notifiable->name}")
            ->line("تم تسجيل شحن المرتجع رقم {$this->returnOrder->return_number} للطلب رقم {$orderNumber}.")
            ->line('سنقوم بإبلاغك فور وصول الشحنة إلى المستودع.')
            ->action('عرض تفاصيل الإرجاع', $returnUrl)
            ->line("شكراً لتسوقك من {$appName}.");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $orderNumber = $this->returnOrder->order?->order_number ?? '—';

        return (new ReturnShippedBackData(
            return_number: $this->returnOrder->return_number,
            order_number: $orderNumber,
            title: 'تم شحن المرتجع',
            message: "تم تسجيل شحن المرتجع رقم {$this->returnOrder->return_number} للطلب رقم {$orderNumber}.",
            action_url: route('store.account.returns.show', $this->returnOrder),
        ))->toArray();
    }
}
