<?php

declare(strict_types=1);

namespace Knyk\SyliusDotpayPlugin\Formatter;

class MoneyFormatter
{
    public function format(int $amount): string
    {
        return bcdiv((string) $amount, '100', 2);
    }
}
