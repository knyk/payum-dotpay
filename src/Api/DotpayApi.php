<?php

declare(strict_types=1);

namespace Knyk\SyliusDotpayPlugin\Api;

final class DotpayApi
{
    private const HOST_PRODUCTION = 'https://ssl.dotpay.pl/t2/';
    private const HOST_TEST = 'https://ssl.dotpay.pl/test_payment/';
    private const CHECKSUM_HASH_ALGORITHM = 'sha256';
    private const API_VERSION = 'dev';
    private const TYPE = 0;

    public const STATUS_DETAILS_KEY = 'dotpay_operation_status';
    public const STATUS_NEW = 'new';
    public const STATUS_PENDING_PROCESSING = 'processing';
    public const STATUS_PENDING_PROCESSING_REALIZATION_WAITING = 'processing_realization_waiting';
    public const STATUS_PENDING_PROCESSING_REALIZATION = 'processing_realization';
    public const STATUS_FAILED = 'rejected';
    public const STATUS_CAPTURED = 'completed';

    public const RESPONSE_NOTIFY_SUCCESS = 'OK';

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

    public static function type(): int
    {
        return self::TYPE;
    }

    public function generateChecksum(array $data): string
    {
        return hash(self::CHECKSUM_HASH_ALGORITHM, sprintf('%s%s', $this->pin, implode('', $data)));
    }
}
