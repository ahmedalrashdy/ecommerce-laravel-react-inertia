<?php

namespace App\Http\Controllers\Store\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Store\Account\UpdateNotificationPreferencesRequest;
use App\Models\NotificationPreference;
use App\Traits\FlashMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class NotificationPreferencesController extends Controller
{
    use FlashMessage;

    public function edit(Request $request): Response
    {
        $preferences = NotificationPreference::query()->firstOrCreate(
            ['user_id' => $request->user()->id],
            [
                'marketing_email' => false,
                'marketing_sms' => false,
                'marketing_whatsapp' => false,
                'marketing_call' => false,
            ],
        );

        return Inertia::render('store/account/notifications', [
            'preferences' => [
                'marketing_email' => $preferences->marketing_email,
                'marketing_sms' => $preferences->marketing_sms,
                'marketing_whatsapp' => $preferences->marketing_whatsapp,
                'marketing_call' => $preferences->marketing_call,
            ],
        ]);
    }

    public function update(
        UpdateNotificationPreferencesRequest $request
    ): RedirectResponse {
        NotificationPreference::query()->updateOrCreate(
            ['user_id' => $request->user()->id],
            $request->validated()
        );

        $this->flashSuccess('تم تحديث خيارات الإشعارات بنجاح.');

        return redirect()->route('store.account.notifications.edit');
    }
}
