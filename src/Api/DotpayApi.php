<?php

declare(strict_types=1);

namespace Knyk\SyliusDotpayPlugin\Api;

final class DotpayApi
{
    private const HOST_PRODUCTION = 'https://ssl.dotpay.pl/t2/';
    private const HOST_TEST = 'https://ssl.dotpay.pl/test_payment/';
    private const CHECKSUM_HASH_ALGORITHM = 'sha256';
    private const API_VERSION = 'dev';
    private const DESCRIPTION_PATTERN = 'Order number: %s';
    private const TYPE = 0;

    public const STATUS_QUERY_PARAM = 'status';
    public const STATUS_CAPTURED = 'OK';
    public const STATUS_FAILED = 'FAIL';

    private string $id;
    private string $pin;
    private bool $sandbox;
    private bool $ignoreLastPaymentChannel;

    public function __construct(string $id, string $pin, bool $sandbox = false, bool $ignoreLastPaymentChannel = false)
    {
        $this->id = $id;
        $this->pin = $pin;
        $this->sandbox = $sandbox;
        $this->ignoreLastPaymentChannel = $ignoreLastPaymentChannel;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function host(): string
    {
        return $this->sandbox ? self::HOST_TEST : self::HOST_PRODUCTION;
    }

    public function ignoreLastPaymentChannel(): bool
    {
        return $this->ignoreLastPaymentChannel;
    }

    public static function apiVersion(): string
    {
        return self::API_VERSION;
    }

    public static function description(string $orderNumber): string
    {
        return sprintf(self::DESCRIPTION_PATTERN, $orderNumber);
    }

    public static function type(): int
    {
        return self::TYPE;
    }

    public function generateChecksum(array $data): string
    {
        return hash(self::CHECKSUM_HASH_ALGORITHM, sprintf('%s%s', $this->pin, implode('', $data)));
    }
}
