<?php

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Config;
use Theaarch\SmsForwarder\Contracts\HandlesWebhooks;
use Theaarch\SmsForwarder\Events\WebhookHandled;
use Theaarch\SmsForwarder\Events\WebhookReceived;
use Theaarch\SmsForwarder\SmsForwarder;

it('can handle webhook calls', function () {
    Event::fake();

    $mock = Mockery::mock(HandlesWebhooks::class);
    $mock->shouldReceive('handle')->andReturn(response('ok'));

    SmsForwarder::handleWebhookUsing(function () use ($mock) {
        return $mock;
    });

    $payload = ['foo' => 'bar'];

    $response = $this->withHeaders([
        'Content-Type' => 'application/x-www-form-urlencoded',
    ])->post(
        route('sms_forwarder.webhook'),
        $payload
    );

    $response->assertOk();

    Event::assertDispatched(WebhookReceived::class, function ($event) use ($payload) {
        return $event->payload['foo'] == $payload['foo'];
    });

    Event::assertDispatched(WebhookHandled::class, function ($event) use ($payload) {
        return $event->payload['foo'] == $payload['foo'];
    });
});

it('verifies webhook signature when secret is set', function () {
    Config::set('sms_forwarder.webhook.secret', 'test-secret');

    $mock = Mockery::mock(HandlesWebhooks::class);
    $mock->shouldReceive('handle')->andReturn(response('ok'));

    SmsForwarder::handleWebhookUsing(function () use ($mock) {
        return $mock;
    });

    $timestamp = time();
    $data = ['foo' => 'bar', 'timestamp' => $timestamp];
    $secret = 'test-secret';

    $beforeSign = $timestamp . "\n" . $secret;
    $signature = urlencode(base64_encode(
        hash_hmac('sha256', $beforeSign, $secret, true)
    ));

    $postData = array_merge($data, ['sign' => $signature]);

    $response = $this->withHeaders([
        'Content-Type' => 'application/x-www-form-urlencoded',
    ])->post(
        route('sms_forwarder.webhook'),
        $postData
    );

    $response->assertOk();
});

it('fails when signature is missing but secret is set', function () {
    Config::set('sms_forwarder.webhook.secret', 'test-secret');

    $response = $this->withHeaders([
        'Content-Type' => 'application/x-www-form-urlencoded',
    ])->post(
        route('sms_forwarder.webhook'),
        ['foo' => 'bar']
    );

    $response->assertStatus(403);
});

it('fails when signature is invalid', function () {
    Config::set('sms_forwarder.webhook.secret', 'test-secret');

    $timestamp = time();
    $secret = 'wrong-secret';
    $data = ['foo' => 'bar', 'timestamp' => $timestamp];

    $beforeSign = $timestamp . "\n" . $secret;
    $signature = urlencode(base64_encode(
        hash_hmac('sha256', $beforeSign, $secret, true)
    ));

    $postData = array_merge($data, ['sign' => $signature]);

    $response = $this->withHeaders([
        'Content-Type' => 'application/x-www-form-urlencoded',
    ])->post(
        route('sms_forwarder.webhook'),
        $postData
    );

    $response->assertStatus(403);
});
