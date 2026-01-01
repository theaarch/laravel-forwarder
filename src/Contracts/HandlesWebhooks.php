<?php

namespace Theaarch\SmsForwarder\Contracts;

use Symfony\Component\HttpFoundation\Response;

interface HandlesWebhooks
{
    /**
     * Handle a webhook call.
     *
     * @param  array  $payload
     * @return Response
     */
    public function handle(array $payload): Response;
}
