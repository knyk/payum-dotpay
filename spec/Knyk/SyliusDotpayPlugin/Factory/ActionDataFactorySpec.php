<?php

declare(strict_types=1);

namespace spec\Knyk\SyliusDotpayPlugin\Factory;

use Knyk\SyliusDotpayPlugin\Api\DotpayApi;
use Knyk\SyliusDotpayPlugin\Formatter\MoneyFormatter;
use Knyk\SyliusDotpayPlugin\Model\CaptureActionData;
use Knyk\SyliusDotpayPlugin\Model\NotifyActionData;
use Knyk\SyliusDotpayPlugin\Provider\ControlProvider;
use Payum\Core\Request\Capture;
use Payum\Core\Security\TokenInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\PayumBundle\Provider\PaymentDescriptionProviderInterface;
use Sylius\Component\Core\Model\PaymentInterface;

class ActionDataFactorySpec extends ObjectBehavior
{
    public function let(
        MoneyFormatter $moneyFormatter,
        PaymentDescriptionProviderInterface $paymentDescriptionProvider,
        ControlProvider $controlProvider
    ): void {
        $this->beConstructedWith($moneyFormatter, $paymentDescriptionProvider, $controlProvider);
    }

    public function it_should_create_capture_action_data(
        Capture $capture,
        PaymentInterface $payment,
        ControlProvider $controlProvider,
        PaymentDescriptionProviderInterface $paymentDescriptionProvider,
        DotpayApi $api,
        TokenInterface $notifyToken,
        TokenInterface $captureToken,
        MoneyFormatter $moneyFormatter
    ): void {
        $capture->getModel()->shouldBeCalledOnce()->willReturn($payment);
        $capture->getToken()->shouldBeCalledOnce()->willReturn($captureToken);

        $controlProvider->unique()->shouldBeCalledOnce()->willReturn('controlstring');

        $paymentDescriptionProvider->getPaymentDescription($payment)->shouldBeCalledOnce()->willReturn(
            'descriptionstring'
        );

        $api->apiVersion()->shouldBeCalledTimes(2)->willReturn('versionstring');
        $api->id()->shouldBeCalledTimes(2)->willReturn('idstring');
        $api->type()->shouldBeCalledTimes(2)->willReturn(1);
        $api->ignoreLastPaymentChannel()->shouldBeCalledTimes(2)->willReturn(true);

        $payment->getAmount()->shouldBeCalledTimes(2)->willReturn(1000);
        $payment->getCurrencyCode()->shouldBeCalledTimes(2)->willReturn('currencycodestring');

        $moneyFormatter->format(1000)->shouldBeCalledTimes(2)->willReturn('10.00');

        $captureToken->getAfterUrl()->shouldBeCalledTimes(2)->willReturn('afterurlstring');

        $notifyToken->getTargetUrl()->shouldBeCalledTimes(2)->willReturn('targeturlstring');

        $api->generateChecksum(
            [
                'versionstring',
                'idstring',
                '10.00',
                'currencycodestring',
                'descriptionstring',
                'controlstring',
                'afterurlstring',
                '1',
                'targeturlstring',
                1,
            ]
        )->shouldBeCalledOnce()->willReturn('chk');

        $captureActionData = new CaptureActionData(
            'versionstring',
            'idstring',
            '10.00',
            'currencycodestring',
            'afterurlstring',
            '1',
            'targeturlstring',
            'descriptionstring',
            1,
            'controlstring',
            'chk'
        );

        $this->createCaptureActionData($capture, $api, $notifyToken)->shouldBeLike($captureActionData);
    }

    public function it_should_create_notify_action_data(): void
    {
        $notifyActionData = new NotifyActionData(['foo' => 'bar']);

        $this->createNotifyActionData(['foo' => 'bar'])->shouldBeLike($notifyActionData);
    }
}
