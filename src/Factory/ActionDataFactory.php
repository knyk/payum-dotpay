<?php

declare(strict_types=1);

namespace Knyk\SyliusDotpayPlugin\Factory;

use Knyk\SyliusDotpayPlugin\Api\DotpayApi;
use Knyk\SyliusDotpayPlugin\Model\CaptureActionData;
use Payum\Core\Request\Capture;
use Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatterInterface;
use Sylius\Component\Core\Model\PaymentInterface;

final class ActionDataFactory
{
    private MoneyFormatterInterface $moneyFormatter;

    public function __construct(MoneyFormatterInterface $moneyFormatter)
    {
        $this->moneyFormatter = $moneyFormatter;
    }

    public function createCaptureActionData(Capture $capture, DotpayApi $api): CaptureActionData
    {
        /** @var PaymentInterface $payment */
        $payment = $capture->getModel();

        $token = $capture->getToken();

        $checksumData = [
            $api::apiVersion(),
            $api->id(),
            $this->moneyFormatter->format($payment->getAmount(), $payment->getCurrencyCode()),
            $payment->getCurrencyCode(),
            $api::description($payment->getOrder()->getNumber()),
            $token->getAfterUrl(),
            $api::type(),
            $token->getTargetUrl(),
            $api->ignoreLastPaymentChannel(),
        ];

        $chk = $api->generateChecksum($checksumData);

        return new CaptureActionData(
            $api::apiVersion(),
            $api->id(),
            $this->moneyFormatter->format($payment->getAmount(), $payment->getCurrencyCode()),
            $payment->getCurrencyCode(),
            $token->getAfterUrl(),
            (string) $api::type(),
            $token->getTargetUrl(),
            $api::description($payment->getOrder()->getNumber()),
            (int) $api->ignoreLastPaymentChannel(),
            $chk
        );
    }
}
