<?php

namespace App\Exceptions;

use Exception;

class InvalidOrderStatusTransitionException extends Exception
{
    // متى يطلق: عندما تحاول نقل طلب من حالة "تم التوصيل
    //  (Delivered) إلى "قيد المعالجة" (Processing)، أو إلغاء طلب وهو "مشحون" (Shipped).
}
