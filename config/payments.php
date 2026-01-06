<?php

return [
    'gateway' => env('PAYMENT_GATEWAY', 'stripe'),
    'currency' => env('PAYMENT_CURRENCY', 'USD'),
];
