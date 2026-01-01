<?php

use Illuminate\Support\Facades\Route;
use Theaarch\SmsForwarder\Http\Controllers\WebhookController;

Route::post('webhook', [WebhookController::class, 'handle'])->name('webhook');
