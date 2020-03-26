<?php

declare(strict_types=1);

namespace Knyk\SyliusDotpayPlugin\Action;

use Knyk\SyliusDotpayPlugin\Api\DotpayApi;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Request\GetStatusInterface;
use Sylius\Bundle\PayumBundle\Request\GetStatus;
use Sylius\Component\Core\Model\PaymentInterface;

final class StatusAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    /**
     * @param GetStatus $request
     */
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var PaymentInterface $payment */
        $payment = $request->getModel();
        $details = $payment->getDetails();

        $getHttpRequest = new GetHttpRequest();
        $this->gateway->execute($getHttpRequest);

        if (!isset($details[DotpayApi::STATUS_DETAILS_KEY])) {
            $request->markNew();

            return;
        }

        $status = $details[DotpayApi::STATUS_DETAILS_KEY];

        switch ($status) {
            case DotpayApi::STATUS_NEW:
                $request->markNew();
                break;
            case DotpayApi::STATUS_PENDING_PROCESSING:
            case DotpayApi::STATUS_PENDING_PROCESSING_REALIZATION:
            case DotpayApi::STATUS_PENDING_PROCESSING_REALIZATION_WAITING:
                $request->markPending();
                break;
            case DotpayApi::STATUS_CAPTURED:
                $request->markCaptured();
                break;
            case DotpayApi::STATUS_FAILED:
                $request->markFailed();
                break;
            default:
                $request->markUnknown();
        }
    }

    public function supports($request): bool
    {
        return $request instanceof GetStatusInterface && $request->getModel() instanceof PaymentInterface;
    }
}
