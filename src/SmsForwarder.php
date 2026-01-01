<?php

namespace Theaarch\SmsForwarder;

use Theaarch\SmsForwarder\Contracts\HandlesWebhooks;

class SmsForwarder
{
    /**
     * Indicates if SmsForwarder routes will be registered.
     *
     * @var bool
     */
    public static bool $registersRoutes = true;

    /**
     * Register a class / callback that should be used to handle new webhooks.
     *
     * @param  callable|string  $callback
     * @return void
     */
    public static function handleWebhookUsing(callable|string $callback): void
    {
        app()->singleton(HandlesWebhooks::class, $callback);
    }

    /**
     * Configure SmsForwarder to not register its routes.
     *
     * @return static
     */
    public static function ignoreRoutes(): static
    {
        static::$registersRoutes = false;

        return new static;
    }
}
