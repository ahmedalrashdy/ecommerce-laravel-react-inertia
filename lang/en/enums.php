<?php

return [
    'attribute_type' => [
        'text' => 'Text',
        'color' => 'Color',
    ],
    'brand_status' => [
        'draft' => 'Draft',
        'published' => 'Published',
        'archived' => 'Archived',
    ],
    'product_status' => [
        'draft' => 'Draft',
        'published' => 'Published',
        'archived' => 'Archived',
    ],
    'category_status' => [
        'draft' => 'Draft',
        'published' => 'Published',
        'archived' => 'Archived',
    ],
    'return_status' => [
        'requested' => 'Requested',
        'approved' => 'Approved',
        'shipped_back' => 'Shipped Back',
        'received' => 'Received',
        'inspected' => 'Inspected',
        'completed' => 'Completed',
        'rejected' => 'Rejected',
    ],
    'order_status' => [
        'pending' => 'Pending',
        'processing' => 'Processing',
        'shipped' => 'Shipped',
        'delivered' => 'Delivered',
        'cancelled' => 'Cancelled',
        'returned' => 'Returned',
    ],
    'payment_status' => [
        'pending' => 'Pending',
        'paid' => 'Paid',
        'failed' => 'Failed',
        'refunded' => 'Refunded',
        'refund_pending' => 'Refund Pending',
        'partially_refunded' => 'Partially Refunded',
    ],
    'payment_method' => [
        'pending' => 'Pending',
        'credit_card' => 'Credit Card',
        'mada' => 'Mada',
        'apple_pay' => 'Apple Pay',
        'bank_transfer' => 'Bank Transfer',
    ],
    'user_address_type' => [
        'home' => 'Home',
        'work' => 'Work',
        'billing' => 'Billing',
        'shipping' => 'Shipping',
        'other' => 'Other',
    ],
    'stock_movement_type' => [
        'sale' => 'Sale',
        'return_restock' => 'Customer Return',
        'supplier_restock' => 'Supplier Restock',
        'adjustment' => 'Adjustment',
        'order_cancellation' => 'Order Cancellation',
        'waste' => 'Waste',
        'transfer' => 'Transfer',
        'replacement_out' => 'Replacement Out',
    ],
    'transaction_type' => [
        'payment' => 'Payment',
        'refund' => 'Refund',
    ],
    'transaction_status' => [
        'pending' => 'Pending',
        'success' => 'Success',
        'failed' => 'Failed',
        'cancelled' => 'Cancelled',
    ],
    'item_condition' => [
        'sealed' => 'Sealed',
        'open_box' => 'Open Box',
        'damaged' => 'Damaged',
        'wrong_item' => 'Wrong Item',
    ],
    'return_resolution' => [
        'refund' => 'Refund',
        'replacement' => 'Replacement',
        'reject' => 'Reject',
    ],
    'refund_method' => [
        'original' => 'Original Payment Method',
        'wallet' => 'Wallet',
        'bank_transfer' => 'Bank Transfer',
    ],
];
