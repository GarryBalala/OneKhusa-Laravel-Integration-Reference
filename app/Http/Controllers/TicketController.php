<?php

namespace App\Http\Controllers;

use App\Services\OneKhusaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TicketController extends Controller
{
    public function buy(OneKhusaService $oneKhusa)
    {
        $reference = "OT-PHP" . time();
        
        try {
          $data = $oneKhusa->initiateCheckout(2500, $reference);
            
            // Save status in Cache (for 30 mins) instead of DB for this reference
            Cache::put("status_$reference", "Pending", 1800);

            return response()->json([
                "status" => "success",
                "redirectUrl" => "https://checkout.onekhusa.com/requestToPay/initiate?ptid=" . $data['paymentTransactionId'],
                "reference" => $reference
            ]);
        } catch (\Exception $e) {
            return response()->json(["status" => "error", "message" => $e->getMessage()], 500);
        }
    }

    public function status($reference)
    {
        return response()->json(["status" => Cache::get("status_$reference", "NotFound")]);
    }
}