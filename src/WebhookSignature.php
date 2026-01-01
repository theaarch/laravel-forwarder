<?php

namespace Theaarch\SmsForwarder;

use Theaarch\SmsForwarder\Exceptions\SignatureVerificationException;

class WebhookSignature
{
    /**
     * Verifies the signature payload sent by SmsForwarder.
     *
     * @param  string  $payload
     * @param  string  $header
     * @param  string  $secret
     * @param  int|null  $tolerance
     * @return bool
     *
     * @throws SignatureVerificationException
     */
    public static function verifyPayload(string $payload, string $header, string $secret, int $tolerance = null): bool
    {
        $timestamp = self::getTimestamp($payload);
        $signature = self::getSignature($payload);

        if (empty($timestamp)) {
            throw SignatureVerificationException::factory(
                'Unable to extract timestamp and signatures from payload',
                $payload,
                $header
            );
        }

        if (empty($signatures)) {
            throw SignatureVerificationException::factory(
                'No signatures found with expected scheme',
                $payload,
                $header
            );
        }

        $signedPayload = $timestamp."\n".$secret;
        $expectedSignature = self::computeSignature($signedPayload, $secret);

        $signatureFound = hash_equals($expectedSignature, $signature);

        if (!$signatureFound) {
            throw SignatureVerificationException::factory(
                'No signatures found matching the expected signature for payload',
                $payload,
                $header
            );
        }

        // Check if timestamp is within tolerance
        if (($tolerance > 0) && (\abs(\time() - $timestamp) > $tolerance)) {
            throw SignatureVerificationException::factory(
                'Timestamp outside the tolerance zone',
                $payload,
                $header
            );
        }

        return true;
    }

    /**
     * Extracts the timestamp in the payload.
     *
     * @param  string  $payload
     * @return string|null
     */
    private static function getTimestamp(string $payload): ?string
    {
        parse_str($payload, $result);

        return $result['timestamp'];
    }

    /**
     * Extracts the signature in the payload.
     *
     * @param  string  $payload
     * @return string|null
     */
    private static function getSignature(string $payload): ?string
    {
        parse_str($payload, $result);

        return $result['sign'];
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
