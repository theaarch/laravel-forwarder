<?php

namespace Theaarch\SmsForwarder\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Theaarch\SmsForwarder\Contracts\HandlesWebhooks;
use Theaarch\SmsForwarder\Events\WebhookHandled;
use Theaarch\SmsForwarder\Events\WebhookReceived;
use Theaarch\SmsForwarder\Http\Middleware\VerifyWebhookSignature;

class WebhookController extends Controller
{
    public function __construct()
    {
        if (config('sms_forwarder.webhook.secret')) {
            $this->middleware(VerifyWebhookSignature::class);
        }
    }

    /**
     * Handle a SmsForwarder webhook call.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Theaarch\SmsForwarder\Contracts\HandlesWebhooks  $handler
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, HandlesWebhooks $handler)
    {
        $payload = $request->input();

        WebhookReceived::dispatch($payload);

        $response = $handler->handle($payload);

        WebhookHandled::dispatch($payload);

        return $response;
    }
}
