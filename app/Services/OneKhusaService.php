<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class OneKhusaService
{
    protected $baseUrl;
    protected $checkoutUrl;

    public function __construct()
    {
        $this->baseUrl = env('ONEKHUSA_BASE_URL');
        $this->checkoutUrl = env('ONEKHUSA_CHECKOUT_URL');
    }

    public function initiateCheckout($amount, $reference)
    {
        $callbackBase = env('PUBLIC_CALLBACK_URL');

        $payload = new \stdClass();
        $payload->authentication = new \stdClass();
        $payload->authentication->apiKey = env('ONEKHUSA_API_KEY');
        $payload->authentication->apiSecret = env('ONEKHUSA_API_SECRET');

        $payload->merchant = new \stdClass();
        $payload->merchant->organisationId = env('ONEKHUSA_ORG_ID');
        $payload->merchant->merchantAccountNumber = (int)env('ONEKHUSA_MERCHANT_NUMBER');

        $payload->payment = new \stdClass();
        $payload->payment->sourceReferenceNumber = $reference;
        $payload->payment->description = "OneTicket Laravel Purchase";
        $payload->payment->amount = (float)$amount;

        $payload->route = new \stdClass();
        $payload->route->successRedirectionUrl = "$callbackBase/?ref=$reference";
        $payload->route->failureRedirectionUrl = "$callbackBase/?ref=$reference";
        $payload->route->callbackApiUrl = "$callbackBase/api/webhooks/payments";

        $headers = new \stdClass();
        $headers->{'X-Idempotency-Key'} = 'PHP-CHK-' . $reference . '-' . Str::random(8);

        $response = Http::withHeaders((array)$headers)->post($this->checkoutUrl, (array)$payload);

        if ($response->failed()) {
            throw new \Exception("OneKhusa API Error: " . $response->body());
        }

        return $response->json();
    }
}
