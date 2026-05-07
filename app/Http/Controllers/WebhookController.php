<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->all();
        
        // Handle TitleCase 'ReferenceNumber' from OneKhusa Metadata
        $myRef = $payload['metaData']['ReferenceNumber'] ?? 
                 $payload['metaData']['referenceNumber'] ?? 
                 $payload['sourceReferenceNumber'] ?? null;

        Log::info("Webhook received for Ref: " . $myRef);

        if ($payload['responseCode'] === "S100" || $payload['transactionStatusCode'] === "S") {
            Cache::put("status_$myRef", "Paid", 1800);
            Log::info("Status updated to PAID for " . $myRef);
        }

        return response("acknowledged", 200)->header('Content-Type', 'text/plain');
    }
}