<?php

namespace App\Notifications\Orders;

use App\Data\Notifications\Orders\OrderShippedData;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderShippedNotification extends Notification implements ShouldQueue
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
            ->subject("🚚 تم شحن طلبك رقم #{$this->order->order_number}")
            ->greeting("مرحباً {$notifiable->name} 👋")
            ->line("خبر سار! تم شحن طلبك رقم **#{$this->order->order_number}** بنجاح وهو الآن في طريقه إليك.")
            ->line('📦 **حالة الطلب الحالية:** تم الشحن')
            ->line('سيصلك الطلب خلال الفترة المتوقعة، وسنقوم بإشعارك فور تسليمه.')
            ->action('عرض تفاصيل الطلب', $orderUrl)
            ->line("شكراً لاختيارك {$appName}، ونتمنى لك تجربة تسوق رائعة 🌟");

    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return (new OrderShippedData(
            order_id: $this->order->id,
            order_number: $this->order->order_number,
            title: 'تم شحن الطلب',
            message: "تم شحن طلبك رقم {$this->order->order_number} بنجاح.",
            action_url: route('store.account.orders.show', $this->order),
        ))->toArray();
    }
}
