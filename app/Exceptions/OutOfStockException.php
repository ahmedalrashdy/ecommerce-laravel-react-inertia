<?php

namespace App\Exceptions;

use App\Models\ProductVariant;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OutOfStockException extends Exception
{
    public function __construct(ProductVariant $variant, int $requestedQuantity)
    {
        $message = "الكمية المطلوبة غير متوفرة للمنتج ({$variant->name}). المتوفر: {$variant->quantity}, المطلوب: {$requestedQuantity}";
        parent::__construct($message, 422);
    }


}
