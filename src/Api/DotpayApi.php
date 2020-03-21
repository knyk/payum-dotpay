<?php

declare(strict_types=1);

namespace Knyk\Payum\Dotpay\Api;

final class DotpayApi
{
    private string $id;
    private string $pin;

    public function __construct(string $id, string $pin)
    {
        $this->id = $id;
        $this->pin = $pin;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function pin(): string
    {
        return $this->pin;
    }
}
