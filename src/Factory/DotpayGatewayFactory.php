<?php

declare(strict_types=1);

namespace Knyk\Payum\Dotpay\Factory;

use Knyk\Payum\Dotpay\Api\DotpayApi;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;

final class DotpayGatewayFactory extends GatewayFactory
{
    protected function populateConfig(ArrayObject $config): void
    {
        $config->defaults(
            [
                'payum.factory_name' => 'dotpay',
                'payum.factory_title' => 'Dotpay',
            ]
        );

        $config['payum.api'] = fn(ArrayObject $config) => new DotpayApi($config->get('id'), $config->get('pin'));
    }
}
