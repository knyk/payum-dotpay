<?php

declare(strict_types=1);

namespace Knyk\SyliusDotpayPlugin\Provider;

class ControlProvider
{
    public function unique(): string
    {
        return uniqid();
    }
}
