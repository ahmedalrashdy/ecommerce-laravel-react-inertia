<?php

namespace App\Services\Payments;

use App\Data\Payments\TransactionEntryData;
use App\Models\Transaction;

class TransactionService
{
    public function record(TransactionEntryData $data): Transaction
    {
        return Transaction::create([
            'order_id' => $data->order_id,
            'user_id' => $data->user_id,
            'type' => $data->type, // Casted to Enum automatically
            'payment_method' => $data->payment_method,
            'amount' => $data->amount,
            'currency' => $data->currency,
            'status' => $data->status, // Casted to Enum
            'transaction_ref' => $data->transaction_ref,
            'gateway_response' => $data->gateway_response, // Casted to Array/JSON
            'description' => $data->description,
            'gateway' => $data->gateway,
            'idempotency_key' => $data->idempotency_key,
            'event_id' => $data->event_id,
            'event_type' => $data->event_type,
            'checkout_session_id' => $data->checkout_session_id,
            'payment_intent_id' => $data->payment_intent_id,
        ]);
    }
}
