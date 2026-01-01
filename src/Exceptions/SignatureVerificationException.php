<?php

namespace Theaarch\SmsForwarder\Exceptions;

use Exception;

class SignatureVerificationException extends Exception implements ExceptionInterface
{
    protected ?string $httpBody;
    protected ?string $sigHeader;

    /**
     * Creates a new SignatureVerificationException exception.
     *
     * @param  string  $message the exception message
     * @param  string|null  $httpBody the HTTP body as a string
     * @param  string|null  $sigHeader the `X-Signature` HTTP header
     *
     * @return SignatureVerificationException
     */
    public static function factory(
        string $message,
        string $httpBody = null,
        string $sigHeader = null
    ): SignatureVerificationException
    {
        $instance = new static($message);
        $instance->setHttpBody($httpBody);
        $instance->setSigHeader($sigHeader);

        return $instance;
    }

    /**
     * Gets the HTTP body as a string.
     *
     * @return null|string
     */
    public function getHttpBody(): ?string
    {
        return $this->httpBody;
    }

    /**
     * Sets the HTTP body as a string.
     *
     * @param  string|null  $httpBody
     */
    public function setHttpBody(?string $httpBody): void
    {
        $this->httpBody = $httpBody;
    }

    /**
     * Gets the `X-Signature` HTTP header.
     *
     * @return null|string
     */
    public function getSigHeader(): ?string
    {
        return $this->sigHeader;
    }

    /**
     * Sets the `X-Signature` HTTP header.
     *
     * @param  string|null  $sigHeader
     */
    public function setSigHeader(?string $sigHeader): void
    {
        $this->sigHeader = $sigHeader;
    }
}
