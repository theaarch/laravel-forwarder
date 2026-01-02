# Laravel SMS Forwarder

A Laravel package to easily handle incoming SmsForwarder webhooks.

## Installation

You can install the package via composer:

```bash
composer require theaarch64/laravel-sms-forwarder:dev-main
```

After installing, run the install command to scaffold the necessary files:

```bash
php artisan sms-forwarder:install
```

This command will:
1. Publish the configuration file to `config/sms_forwarder.php`.
2. Publish the webhook handler action to `app/Actions/SmsForwarder/HandleWebhook.php`.
3. Publish and register the `SmsForwarderServiceProvider` in your application.

## Usage

### Handling Webhooks

The package uses an Action class to handle incoming webhooks. After installation, you can find the handler at `app/Actions/SmsForwarder/HandleWebhook.php`.

You should modify the `handle` method in this class to implement your custom logic (e.g., saving the SMS to the database, forwarding it to Telegram/Slack, etc.).

```php
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
        Log::info('SMS Forwarder Webhook Received', $payload);

        // Access payload data
        // $from = $payload['from'] ?? null;
        // $message = $payload['content'] ?? null;

        return new Response('Webhook Handled', Response::HTTP_OK);
    }
}
```

### Configuration

You can configure the package in `config/sms_forwarder.php`.

#### Route Prefix
By default, the webhook route is available at `/sms-forwarder/webhook`. You can change the prefix in the config or via `.env`:

```env
SMS_FORWARDER_PREFIX=custom-prefix
```

#### Middleware
You can add custom middleware to the webhook route in `config/sms_forwarder.php`:

```php
'middleware' => ['api'],
```

### Security: Webhook Signature Verification

To ensure that the webhook requests are coming from a trusted source, you should configure a webhook secret.

1. Set the secret in your `.env` file:
   ```env
   SMS_FORWARDER_WEBHOOK_SECRET=your-secret-key
   ```

2. The package will automatically verify the signature included in the request body (`sign` parameter) using this secret.

If the secret is not set, signature verification is skipped (not recommended for production).

## Testing

You can run the tests with:

```bash
vendor/bin/pest
```

## License

The MIT License (MIT).
