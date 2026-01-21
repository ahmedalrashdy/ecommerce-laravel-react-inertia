<?php

namespace App\Http\Controllers\Store\Marketing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Store\Newsletter\SubscribeRequest;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\JsonResponse;

class NewsletterController extends Controller
{
    /**
     * Subscribe to newsletter
     */
    public function store(SubscribeRequest $request): JsonResponse
    {
        $email = $request->validated()['email'];
        $user = auth()->user();

        // Check if email already exists
        $existingSubscriber = NewsletterSubscriber::where('email', $email)->first();

        if ($existingSubscriber) {
            // If user is logged in and the subscription doesn't have a user_id, update it
            if ($user && ! $existingSubscriber->user_id) {
                $existingSubscriber->update(['user_id' => $user->id]);
            }

            return response()->json([
                'message' => 'أنت مشترك بالفعل في النشرة الإخبارية',
            ], 200);
        }

        // Create new subscription
        NewsletterSubscriber::create([
            'email' => $email,
            'user_id' => $user?->id,
        ]);

        return response()->json([
            'message' => 'تم الاشتراك بنجاح في النشرة الإخبارية',
        ], 201);
    }
}
