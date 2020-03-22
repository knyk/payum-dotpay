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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class StatusAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    /**
     * @param GetStatus $request
     */
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $getHttpRequest = new GetHttpRequest();

        $this->gateway->execute($getHttpRequest);

        if (!isset($getHttpRequest->query[DotpayApi::STATUS_QUERY_PARAM])) {
            throw new NotFoundHttpException('Missing payment status in response from Dotpay.');
        }

        $status = $getHttpRequest->query[DotpayApi::STATUS_QUERY_PARAM];

        switch ($status) {
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
        return $request instanceof GetStatusInterface && $request->getFirstModel() instanceof PaymentInterface;
    }
}
