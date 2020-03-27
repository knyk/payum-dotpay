<?php

declare(strict_types=1);

namespace Knyk\SyliusDotpayPlugin\Factory;

use Payum\Core\Request\GetHttpRequest;

class HttpRequestFactory
{
    public function createGet(): GetHttpRequest
    {
        return new GetHttpRequest();
    }
}
