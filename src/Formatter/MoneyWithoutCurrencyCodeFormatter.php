<?php

declare(strict_types=1);

namespace Knyk\SyliusDotpayPlugin\Formatter;

use Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatterInterface;

class MoneyWithoutCurrencyCodeFormatter implements MoneyFormatterInterface
{
    private MoneyFormatterInterface $moneyFormatter;

    public function __construct(MoneyFormatterInterface $moneyFormatter)
    {
        $this->moneyFormatter = $moneyFormatter;
    }

    public function format(int $amount, string $currencyCode, ?string $locale = null): string
    {
        return trim(str_replace($currencyCode, '', $this->moneyFormatter->format($amount, $currencyCode)));
    }
}
