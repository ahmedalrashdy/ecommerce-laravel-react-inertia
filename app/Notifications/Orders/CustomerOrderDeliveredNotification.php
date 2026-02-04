<?php

namespace App\Notifications\Orders;

use App\Data\Notifications\Orders\OrderDeliveredData;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomerOrderDeliveredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Order $order)
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
        $appName = (string) config('app.name');
        $orderUrl = route('store.account.orders.show', $this->order);

        return (new MailMessage)
            ->subject("تم تسليم الطلب #{$this->order->order_number}")
            ->greeting("مرحباً {$notifiable->name}")
            ->line("تم تسليم طلبك رقم {$this->order->order_number} بنجاح.")
            ->action('عرض تفاصيل الطلب', $orderUrl)
            ->line("شكراً لتسوقك من {$appName}.");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return (new OrderDeliveredData(
            order_id: $this->order->id,
            order_number: $this->order->order_number,
            title: '📦 تم تسليم طلبك بنجاح',
            message: "يسعدنا إبلاغك بأن طلبك رقم #{$this->order->order_number} قد تم تسليمه بنجاح.\n\nنأمل أن تكون تجربتك معنا مميزة، ونتطلع لخدمتك مرة أخرى قريباً 🌟",
            action_url: route('store.account.orders.show', $this->order),
        ))->toArray();

    }
}
