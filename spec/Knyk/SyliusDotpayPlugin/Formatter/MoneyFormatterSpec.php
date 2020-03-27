<?php

declare(strict_types=1);

namespace spec\Knyk\SyliusDotpayPlugin\Formatter;

use PhpSpec\ObjectBehavior;

class MoneyFormatterSpec extends ObjectBehavior
{
    public function it_should_return_number_divided_by_100_with_two_decimal_points(): void
    {
        $this->format(1000)->shouldBe('10.00');
        $this->format(100)->shouldBe('1.00');
        $this->format(10)->shouldBe('0.10');
        $this->format(0)->shouldBe('0.00');
    }
}
