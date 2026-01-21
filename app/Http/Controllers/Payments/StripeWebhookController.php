<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Services\Payments\StripeWebhookService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use UnexpectedValueException;

class StripeWebhookController extends Controller
{
    public function __invoke(Request $request, StripeWebhookService $webhookService): Response
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature', '');
        $secret = config('stripe.webhook_secret');

        if (! $secret) {
            return response('Webhook secret is not configured.', 500);
        }
        try {
            $event = Webhook::constructEvent($payload, $signature, $secret);
        } catch (UnexpectedValueException) {
            return response('Invalid payload.', 400);
        } catch (SignatureVerificationException) {
            return response('Invalid signature.', 400);
        }

        $webhookService->handle($event);

        return response('OK', 200);
    }
}
