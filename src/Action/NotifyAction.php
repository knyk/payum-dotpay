<?php

declare(strict_types=1);

namespace Knyk\SyliusDotpayPlugin\Action;

use Knyk\SyliusDotpayPlugin\Api\DotpayApi;
use Knyk\SyliusDotpayPlugin\Formatter\MoneyFormatter;
use Knyk\SyliusDotpayPlugin\Model\NotifyActionData;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\ApiAwareTrait;
use Payum\Core\Exception\InvalidArgumentException;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Request\Notify;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class NotifyAction implements ActionInterface, GatewayAwareInterface, ApiAwareInterface
{
    use GatewayAwareTrait, ApiAwareTrait;

    /**
     * @var DotpayApi
     */
    protected $api;
    private MoneyFormatter $moneyFormatter;

    public function __construct(MoneyFormatter $moneyFormatter)
    {
        $this->apiClass = DotpayApi::class;
        $this->moneyFormatter = $moneyFormatter;
    }

    /**
     * @param Notify $request
     */
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var PaymentInterface $payment */
        $payment = $request->getFirstModel();

        $getHttpRequest = new GetHttpRequest();
        $this->gateway->execute($getHttpRequest);

        $details = $payment->getDetails();

        if (isset($details['dotpay_operation_status'])) {
            throw new NotFoundHttpException('Payment already confirmed.');
        }

        if (!isset($getHttpRequest->request['control'])) {
            throw new NotFoundHttpException('Control is missing.');
        }

        if ($details['control'] !== $getHttpRequest->request['control']) {
            throw new InvalidArgumentException('Control is invalid.');
        }

        $notifyActionData = new NotifyActionData($getHttpRequest->request);

        $signature = $this->api->generateChecksum($notifyActionData->toArray());

        if ($signature !== $getHttpRequest->request['signature']) {
            throw new InvalidArgumentException('Signature is invalid.');
        }

        if ($payment->getAmount() !== $this->moneyFormatter->format($getHttpRequest->request['operation_amount'])) {
            throw new InvalidArgumentException('Payment amount different than operation amount.');
        }

        $details['dotpay_operation_number'] = $getHttpRequest->request['operation_number'];
        $details['dotpay_operation_status'] = $getHttpRequest->request['operation_status'];
        $details['dotpay_operation_amount'] = $getHttpRequest->request['operation_amount'];
        $details['dotpay_operation_currency'] = $getHttpRequest->request['operation_currency'];
        $details['dotpay_operation_original_amount'] = $getHttpRequest->request['operation_original_amount'];
        $details['dotpay_operation_original_currency'] = $getHttpRequest->request['operation_original_currency'];
        $details['dotpay_operation_datetime'] = $getHttpRequest->request['operation_datetime'];

        $payment->setDetails($details);

        throw new HttpResponse(DotpayApi::RESPONSE_NOTIFY_SUCCESS);
    }

    public function supports($request): bool
    {
        return $request instanceof Notify && $request->getModel() instanceof \ArrayAccess;
    }
}
