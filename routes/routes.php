<?php

use Illuminate\Support\Facades\Route;
use Theaarch\SmsForwarder\Http\Controllers\WebhookController;

Route::group(['middleware' => config('sms_forwarder.middleware')], function () {
    Route::post('webhook', [WebhookController::class, 'handle'])->name('webhook');
});
