<?php

declare(strict_types=1);

namespace spec\Knyk\SyliusDotpayPlugin\Factory;

use Payum\Core\Request\GetHttpRequest;
use PhpSpec\ObjectBehavior;

class HttpRequestFactorySpec extends ObjectBehavior
{
    public function it_should_create_get_http_request(): void
    {
        $this->createGet()->shouldBeLike(new GetHttpRequest());
    }
}
