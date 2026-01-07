<?php

namespace App\Exceptions;

use Exception;

class OrderNotCancellableException extends Exception
{
    // متى يطلق: عندما يحاول العميل إلغاء الطلب من واجهته، لكن الطلب قد دخل مرحلة الشحن أو التجهيز النهائي.
}
