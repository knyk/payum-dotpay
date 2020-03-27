<?php

declare(strict_types=1);

namespace Knyk\SyliusDotpayPlugin\Factory;

use Knyk\SyliusDotpayPlugin\Api\DotpayApi;
use Knyk\SyliusDotpayPlugin\Formatter\MoneyFormatter;
use Knyk\SyliusDotpayPlugin\Model\CaptureActionData;
use Knyk\SyliusDotpayPlugin\Model\NotifyActionData;
use Knyk\SyliusDotpayPlugin\Provider\ControlProvider;
use Payum\Core\Request\Capture;
use Payum\Core\Security\TokenInterface;
use Sylius\Bundle\PayumBundle\Provider\PaymentDescriptionProviderInterface;
use Sylius\Component\Core\Model\PaymentInterface;

class ActionDataFactory
{
    private MoneyFormatter $moneyFormatter;
    private PaymentDescriptionProviderInterface $paymentDescriptionProvider;
    private ControlProvider $controlProvider;

    public function __construct(
        MoneyFormatter $moneyFormatter,
        PaymentDescriptionProviderInterface $paymentDescriptionProvider,
        ControlProvider $controlProvider
    ) {
        $this->moneyFormatter = $moneyFormatter;
        $this->paymentDescriptionProvider = $paymentDescriptionProvider;
        $this->controlProvider = $controlProvider;
    }

    public function createCaptureActionData(
        Capture $capture,
        DotpayApi $api,
        TokenInterface $notifyToken
    ): CaptureActionData {
        /** @var PaymentInterface $payment */
        $payment = $capture->getModel();

        $token = $capture->getToken();

        $control = $this->controlProvider->unique();

        $description = $this->paymentDescriptionProvider->getPaymentDescription($payment);

        $checksumData = [
            $api->apiVersion(),
            $api->id(),
            $this->moneyFormatter->format($payment->getAmount()),
            $payment->getCurrencyCode(),
            $description,
            $control,
            $token->getAfterUrl(),
            (string) $api->type(),
            $notifyToken->getTargetUrl(),
            (int) $api->ignoreLastPaymentChannel(),
        ];

        $chk = $api->generateChecksum($checksumData);

        return new CaptureActionData(
            $api->apiVersion(),
            $api->id(),
            $this->moneyFormatter->format($payment->getAmount()),
            $payment->getCurrencyCode(),
            $token->getAfterUrl(),
            (string) $api->type(),
            $notifyToken->getTargetUrl(),
            $description,
            (int) $api->ignoreLastPaymentChannel(),
            $control,
            $chk
        );
    }

    public function createNotifyActionData(array $data): NotifyActionData
    {
        return new NotifyActionData($data);
    }
}
