<?php

namespace Theaarch\SmsForwarder;

use Theaarch\SmsForwarder\Exceptions\SignatureVerificationException;

class WebhookSignature
{
    /**
     * Verifies the signature payload sent by SmsForwarder.
     *
     * @param  string  $payload
     * @param  string  $secret
     * @param  int|null  $tolerance
     * @return bool
     *
     * @throws SignatureVerificationException
     */
    /**
     * Verifies the signature payload sent by SmsForwarder.
     *
     * @param  array  $payload
     * @param  string  $secret
     * @param  int|null  $tolerance
     * @return bool
     *
     * @throws SignatureVerificationException
     */
    public static function verifyPayload(array $payload, string $secret, int $tolerance = null): bool
    {
        $timestamp = $payload['timestamp'] ?? null;
        $signature = $payload['sign'] ?? null;

        if (empty($timestamp) || empty($signature)) {
            throw SignatureVerificationException::factory(
                'Unable to extract timestamp and signatures from payload',
                http_build_query($payload)
            );
        }

        $beforeSign = $timestamp . "\n" . $secret;
        $expectedSignature = self::computeSignature($beforeSign, $secret);

        if (! hash_equals($expectedSignature, $signature)) {
            throw SignatureVerificationException::factory(
                'No signatures found matching the expected signature for payload',
                http_build_query($payload)
            );
        }

        // Check if timestamp is within tolerance
        if (($tolerance > 0) && (abs(time() - $timestamp) > $tolerance)) {
            throw SignatureVerificationException::factory(
                'Timestamp outside the tolerance zone',
                http_build_query($payload)
            );
        }

        return true;
    }

    /**
     * Computes the signature for a given payload and secret.
     *
     * @param  string  $payload
     * @param  string  $secret
     * @return string
     */
    private static function computeSignature(string $payload, string $secret): string
    {
        $binary = hash_hmac('sha256', $payload, $secret, true);

        return urlencode(base64_encode($binary));
    }
}
