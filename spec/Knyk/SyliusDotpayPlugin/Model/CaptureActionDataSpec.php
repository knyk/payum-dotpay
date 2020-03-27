<?php

declare(strict_types=1);

namespace spec\Knyk\SyliusDotpayPlugin\Model;

use PhpSpec\ObjectBehavior;

class CaptureActionDataSpec extends ObjectBehavior
{
    public function it_should_return_array(): void
    {
        $this->beConstructedWith(
            'apiversionstring',
            'idstring',
            'amountstring',
            'currencystring',
            'urlstring',
            'typestring',
            'urlcstring',
            'descriptionstirng',
            1,
            'controlstring',
            'chkstring'
        );

        $this->toArray()->shouldBe([
            'api_version' => 'apiversionstring',
            'id' => 'idstring',
            'amount' => 'amountstring',
            'currency' => 'currencystring',
            'description' => 'descriptionstirng',
            'url' => 'urlstring',
            'type' => 'typestring',
            'urlc' => 'urlcstring',
            'ignore_last_payment_channel' => 1,
            'control' => 'controlstring',
            'chk' => 'chkstring',
        ]);
    }
}
