<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\WebhookController;

// The prefix 'api' is added automatically by Laravel
Route::post('Tickets/buy/{eventId}', [TicketController::class, 'buy']);
Route::get('Tickets/status/{reference}', [TicketController::class, 'status']);
Route::post('webhooks/payments', [WebhookController::class, 'handle']);