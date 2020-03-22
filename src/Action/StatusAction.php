<?php

declare(strict_types=1);

namespace Knyk\SyliusDotpayPlugin\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\GetStatusInterface;
use Sylius\Bundle\PayumBundle\Request\GetStatus;
use Sylius\Component\Core\Model\PaymentInterface;

final class StatusAction implements ActionInterface
{
    /**
     * @param GetStatus $request
     */
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var PaymentInterface $payment */
        $payment = $request->getFirstModel();
    }

    public function supports($request): bool
    {
        return $request instanceof GetStatusInterface && $request->getFirstModel() instanceof PaymentInterface;
    }
}
