<?php

return [
    'attribute_type' => [
        'text' => 'نص',
        'color' => 'لون',
    ],
    'brand_status' => [
        'draft' => 'مسودة',
        'published' => 'منشور',
        'archived' => 'مؤرشف',
    ],
    'product_status' => [
        'draft' => 'مسودة',
        'published' => 'منشور',
        'archived' => 'مؤرشف',
    ],
    'category_status' => [
        'draft' => 'مسودة',
        'published' => 'منشور',
        'archived' => 'مؤرشف',
    ],
    'return_status' => [
        'requested' => 'طلب استرجاع',
        'approved' => 'تمت الموافقة',
        'shipped_back' => 'تم شحنه للعودة',
        'received' => 'تم الاستلام',
        'inspected' => 'تم الفحص',
        'completed' => 'مكتمل',
        'rejected' => 'تم الرفض',
    ],
    'order_status' => [
        'pending' => 'قيد الانتظار',
        'processing' => 'قيد المعالجة',
        'shipped' => 'تم الشحن',
        'delivered' => 'تم التسليم',
        'cancelled' => 'ملغي',
        'returned' => 'تم الإرجاع',
    ],
    'payment_status' => [
        'pending' => 'قيد الانتظار',
        'paid' => 'مدفوع',
        'failed' => 'فشل',
        'refunded' => 'تم الاسترداد',
        'refund_pending' => 'قيد الاسترداد',
        'partially_refunded' => 'استرداد جزئي',
    ],
    'payment_method' => [
        'pending' => 'قيد التحديد',
        'credit_card' => 'بطاقة ائتمان',
        'mada' => 'مدى',
        'apple_pay' => 'أبل باي',
        'bank_transfer' => 'تحويل بنكي',
    ],
    'user_address_type' => [
        'home' => 'منزل',
        'work' => 'عمل',
        'other' => 'أخرى',
    ],
    'stock_movement_type' => [
        'sale' => 'بيع',
        'return_restock' => 'استرجاع عميل',
        'supplier_restock' => 'توريد جديد',
        'adjustment' => 'تسوية جردية',
        'order_cancellation' => 'إلغاء طلب',
        'waste' => 'تالف/مفقود',
        'transfer' => 'نقل بين فروع',
        'replacement_out' => 'صرف منتج بديل',
    ],
    'transaction_type' => [
        'payment' => 'دفع',
        'refund' => 'استرجاع',
    ],
    'transaction_status' => [
        'pending' => 'قيد الانتظار',
        'success' => 'نجح',
        'failed' => 'فشل',
        'cancelled' => 'ملغي',
    ],
    'item_condition' => [
        'sealed' => 'جديد',
        'open_box' => 'مفتوح وسليم',
        'damaged' => 'تالف',
        'wrong_item' => 'منتج خطأ',
    ],
    'return_resolution' => [
        'refund' => 'استرداد',
        'replacement' => 'استبدال',
        'reject' => 'رفض',
    ],
    'refund_method' => [
        'original' => 'نفس طريقة الدفع الأصلية',
        'wallet' => 'محفظة',
        'bank_transfer' => 'تحويل بنكي',
    ],
];
