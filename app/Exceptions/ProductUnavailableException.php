<?php

namespace App\Exceptions;

use Exception;

class ProductUnavailableException extends Exception
{
    // متى يطلق: إذا كان المنتج موجوداً في السلة، لكن تم تغيير حالته
    // (status) إلى "غير فعال" أو "مؤرشف" قبل الدفع.
}
