<?php

declare(strict_types=1);

namespace spec\Knyk\SyliusDotpayPlugin\Api;

use PhpSpec\ObjectBehavior;

class DotpayApiSpec extends ObjectBehavior
{
    public function it_should_return_sandbox_host(): void
    {
        $this->beConstructedWith('id', 'pin', true);

        $this->host()->shouldBe('https://ssl.dotpay.pl/test_payment/');
    }

    public function it_should_return_production_host(): void
    {
        $this->beConstructedWith('id', 'pin', false);

        $this->host()->shouldBe('https://ssl.dotpay.pl/t2/');
    }

    public function it_should_return_api_version(): void
    {
        $this->beConstructedWith('id', 'pin', false);

        $this->apiVersion()->shouldBe('dev');
    }

    public function it_should_generate_checksum_as_sha256_of_concatenated_pin_and_data(): void
    {
        $this->beConstructedWith('id', 'pin', false);

        $hash = hash('sha256', 'pinfoobar');

        $this->generateChecksum(['foo', 'bar'])->shouldBe($hash);
    }
}
