<?php

namespace App\Http\Controllers\Store\Account\Orders;

use App\Data\Basic\ReturnDetailsData;
use App\Http\Controllers\Controller;
use App\Models\ReturnOrder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReturnDetailsController extends Controller
{
    public function show(Request $request, ReturnOrder $returnOrder): Response
    {
        $user = $request->user();

        if ($returnOrder->user_id !== $user->id) {
            abort(404);
        }

        $returnOrder->load([
            'order',
            'items.orderItem',
            'items.inspections',
            'history' => fn ($query) => $query->where('is_visible_to_user', true)->latest(),
        ]);

        return Inertia::render('store/account/returns/show', [
            'returnOrder' => ReturnDetailsData::fromModel($returnOrder),
        ]);
    }
}
