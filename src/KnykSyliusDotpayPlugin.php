<?php

declare(strict_types=1);

namespace Knyk\SyliusDotpayPlugin;

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class KnykSyliusDotpayPlugin extends Bundle
{
    use SyliusPluginTrait;
}
