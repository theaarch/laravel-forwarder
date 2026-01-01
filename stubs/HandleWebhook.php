<?php

namespace App\Actions\SmsForwarder;

use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Theaarch\SmsForwarder\Contracts\HandlesWebhooks;

class HandleWebhook implements HandlesWebhooks
{
    /**
     * Handle a webhook call.
     *
     * @param  array  $payload
     * @return Response
     */
    public function handle(array $payload): Response
    {
        Log::info('SMS Forwarder Webhook', $payload);

        // Your logic here...

        return new Response('Webhook Handled', Response::HTTP_OK);
    }
}
