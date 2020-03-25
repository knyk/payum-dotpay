<?php

declare(strict_types=1);

namespace Knyk\SyliusDotpayPlugin\Factory;

use Knyk\SyliusDotpayPlugin\Api\DotpayApi;
use Knyk\SyliusDotpayPlugin\Formatter\MoneyFormatter;
use Knyk\SyliusDotpayPlugin\Model\CaptureActionData;
use Payum\Core\Request\Capture;
use Payum\Core\Security\TokenInterface;
use Sylius\Component\Core\Model\PaymentInterface;

final class ActionDataFactory
{
    private MoneyFormatter $moneyFormatter;

    public function __construct(MoneyFormatter $moneyFormatter)
    {
        $this->moneyFormatter = $moneyFormatter;
    }

    public function createCaptureActionData(
        Capture $capture,
        DotpayApi $api,
        TokenInterface $notifyToken
    ): CaptureActionData {
        /** @var PaymentInterface $payment */
        $payment = $capture->getModel();

        $token = $capture->getToken();

        $control = uniqid();

        $checksumData = [
            $api::apiVersion(),
            $api->id(),
            $this->moneyFormatter->format($payment->getAmount()),
            $payment->getCurrencyCode(),
            $api::description($payment->getOrder()->getNumber()),
            $control,
            $token->getAfterUrl(),
            $api::type(),
            $notifyToken->getTargetUrl(),
            (int) $api->ignoreLastPaymentChannel(),
        ];

        $chk = $api->generateChecksum($checksumData);

        return new CaptureActionData(
            $api::apiVersion(),
            $api->id(),
            $this->moneyFormatter->format($payment->getAmount()),
            $payment->getCurrencyCode(),
            $token->getAfterUrl(),
            (string) $api::type(),
            $notifyToken->getTargetUrl(),
            $api::description($payment->getOrder()->getNumber()),
            (int) $api->ignoreLastPaymentChannel(),
            $control,
            $chk
        );
    }
}
