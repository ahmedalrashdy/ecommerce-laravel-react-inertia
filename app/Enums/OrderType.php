<?php

namespace App\Enums;

enum OrderType: int
{
    case NORMAL = 1;       // طلب شراء عادي
    case REPLACEMENT = 2;  // طلب استبدال (مجاني غالباً)
    case RETURN_SHIPMENT = 3; // طلب إعادة شحن منتج مرفوض للعميل (قد يكون عليه رسوم شحن)
}
