<?php

namespace App\Notifications\Orders;

use App\Data\Notifications\Orders\OrderPaymentSucceededData;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomerOrderPaymentSucceededNotification extends Notification implements ShouldQueue
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
            ->subject("✅ تم تأكيد دفع طلبك رقم #{$this->order->order_number}")
            ->greeting("مرحباً {$notifiable->name} 👋")
            ->line("يسعدنا إبلاغك بأن عملية الدفع لطلبك رقم **#{$this->order->order_number}** تمت بنجاح.")
            ->line("💳 **إجمالي المبلغ المدفوع:** {$this->order->grand_total}")
            ->line('📦 **حالة الطلب الحالية:** قيد المعالجة')
            ->line('نقوم حالياً بتجهيز طلبك، وسيتم إشعارك فور انتقاله إلى مرحلة الشحن.')
            ->action('عرض تفاصيل الطلب', $orderUrl)
            ->line('إذا كان لديك أي استفسار، فريق الدعم لدينا جاهز لمساعدتك في أي وقت.')
            ->line("شكراً لثقتك وتسوقك من {$appName} 🌟");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return (new OrderPaymentSucceededData(
            order_id: $this->order->id,
            order_number: $this->order->order_number,
            grand_total: (string) $this->order->grand_total,
            title: 'تم تأكيد الدفع',
            message: "تم استلام دفعتك بنجاح للطلب رقم {$this->order->order_number}. حالة الطلب الآن: قيد المعالجة.",
            action_url: route('store.account.orders.show', $this->order),
        ))->toArray();
    }
}
