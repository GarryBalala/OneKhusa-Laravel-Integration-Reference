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

        $payload = [
            "authentication" => [
                "apiKey" => env('ONEKHUSA_API_KEY'),
                "apiSecret" => env('ONEKHUSA_API_SECRET')
            ],
            "merchant" => [
                "organisationId" => env('ONEKHUSA_ORG_ID'),
                "merchantAccountNumber" => (int)env('ONEKHUSA_MERCHANT_NUMBER')
            ],
            "payment" => [
                "sourceReferenceNumber" => $reference,
                "description" => "OneTicket Laravel Purchase",
                "amount" => (float)$amount
            ],
            "route" => [
                "successRedirectionUrl" => "$callbackBase/?ref=$reference",
                "failureRedirectionUrl" => "$callbackBase/?ref=$reference",
                "callbackApiUrl" => "$callbackBase/api/webhooks/payments"
            ]
        ];

        $response = Http::withHeaders([
            'X-Idempotency-Key' => 'PHP-CHK-' . $reference . '-' . Str::random(8)
        ])->post($this->checkoutUrl, $payload);

        if ($response->failed()) {
            throw new \Exception("OneKhusa API Error: " . $response->body());
        }

        return $response->json();
    }
}