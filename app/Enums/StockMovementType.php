<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum StockMovementType: int implements HasLabel
{
    // عمليات البيع والشراء
    // --- عمليات الخروج (Out) ---
    case SALE = 0;              // بيع عادي (للطلب نوع NORMAL)
    case REPLACEMENT_OUT = 7;   // (جديد) صرف منتج بديل (للطلب نوع REPLACEMENT)
    case WASTE = 5;             // تالف/مفقود (إخراج من المخزون نهائياً)

    // --- عمليات الدخول (In) ---
    case SUPPLIER_RESTOCK = 2;  // توريد جديد
    case RETURN_RESTOCK = 1;    // (معدل) استرجاع عميل "سليم" (يعود للرف للبيع)
    case ORDER_CANCELLATION = 4; // إلغاء طلب قبل الشحن (استعادة حجز)

    // --- عمليات التسوية/النقل ---
    case ADJUSTMENT = 3;        // تسوية جردية (+/-)
    case TRANSFER = 6;          // نقل بين فروع

    public function getLabel(): string
    {
        return match ($this) {
            self::SALE => __('enums.stock_movement_type.sale'),
            self::RETURN_RESTOCK => __('enums.stock_movement_type.return_restock'),
            self::SUPPLIER_RESTOCK => __('enums.stock_movement_type.supplier_restock'),
            self::ADJUSTMENT => __('enums.stock_movement_type.adjustment'),
            self::ORDER_CANCELLATION => __('enums.stock_movement_type.order_cancellation'),
            self::WASTE => __('enums.stock_movement_type.waste'),
            self::TRANSFER => __('enums.stock_movement_type.transfer'),
            self::REPLACEMENT_OUT => __('enums.stock_movement_type.replacement_out'),
        };
    }

    public static function options(): array
    {
        return array_column(self::cases(), 'value', 'name');
    }
}
