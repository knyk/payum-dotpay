<?php

declare(strict_types=1);

namespace spec\Knyk\SyliusDotpayPlugin\Action;

use Knyk\SyliusDotpayPlugin\Api\DotpayApi;
use Knyk\SyliusDotpayPlugin\Factory\ActionDataFactory;
use Knyk\SyliusDotpayPlugin\Model\CaptureActionData;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Reply\HttpPostRedirect;
use Payum\Core\Request\Capture;
use Payum\Core\Security\GenericTokenFactoryInterface;
use Payum\Core\Security\TokenInterface;
use Payum\Core\Storage\IdentityInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\PaymentInterface;

class CaptureActionSpec extends ObjectBehavior
{
    public function let(ActionDataFactory $actionDataFactory): void
    {
        $this->beConstructedWith($actionDataFactory);
    }

    public function it_should_throw_exception_if_request_is_not_supported_on_execute(): void
    {
        $this->shouldThrow(RequestNotSupportedException::class)->during('execute', [new \stdClass()]);
    }

    public function it_should_throw_http_post_redirect_with_host_and_capture_action_data_on_execute(
        Capture $capture,
        PaymentInterface $payment,
        TokenInterface $token,
        GenericTokenFactoryInterface $genericTokenFactory,
        IdentityInterface $identity,
        TokenInterface $notifyToken,
        ActionDataFactory $actionDataFactory,
        DotpayApi $api,
        CaptureActionData $captureActionData
    ): void {
        $capture->getModel()->shouldBeCalledTimes(2)->willReturn($payment);
        $capture->getToken()->shouldBeCalledOnce()->willReturn($token);

        $this->setGenericTokenFactory($genericTokenFactory);

        $token->getGatewayName()->shouldBeCalledOnce()->willReturn('gateway-name');
        $token->getDetails()->shouldBeCalledOnce()->willReturn($identity);

        $genericTokenFactory->createNotifyToken('gateway-name', $identity)->shouldBeCalledOnce()->willReturn(
            $notifyToken
        );

        $this->setApi($api);

        $actionDataFactory->createCaptureActionData($capture, $api, $notifyToken)->shouldBeCalledOnce()->willReturn(
            $captureActionData
        );

        $captureActionData->control()->shouldBeCalledOnce()->willReturn('controlstring');
        $captureActionData->toArray()->shouldBeCalledOnce()->willReturn(['foo' => 'bar']);

        $payment->setDetails(['control' => 'controlstring'])->shouldBeCalledOnce();

        $api->host()->shouldBeCalledOnce()->willReturn('host');

        $httpPostRedirect = new HttpPostRedirect('host', ['foo' => 'bar']);

        $this->shouldThrow($httpPostRedirect)->during('execute', [$capture]);
    }

    public function it_should_support_capture_request_with_payment_model(
        Capture $capture,
        PaymentInterface $payment
    ): void {
        $capture->getModel()->shouldBeCalledOnce()->willReturn($payment);

        $this->supports($capture)->shouldBe(true);
    }

    public function it_should_not_support_capture_request_with_model_not_instance_of_payment(Capture $capture): void
    {
        $capture->getModel()->shouldBeCalledOnce()->willReturn(new \stdClass());

        $this->supports($capture)->shouldBe(false);
    }

    public function it_should_not_support_request_not_instance_of_capture(): void
    {
        $this->supports(new \stdClass())->shouldBe(false);
    }
}
