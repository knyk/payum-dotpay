<?php

declare(strict_types=1);

namespace spec\Knyk\SyliusDotpayPlugin\Action;

use Knyk\SyliusDotpayPlugin\Factory\HttpRequestFactory;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayInterface;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Request\GetStatusInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\PaymentInterface;

class StatusActionSpec extends ObjectBehavior
{
    public function let(HttpRequestFactory $httpRequestFactory): void
    {
        $this->beConstructedWith($httpRequestFactory);
    }

    public function it_should_support_get_status_interface_request_with_payment_model(
        GetStatusInterface $getStatus,
        PaymentInterface $payment
    ): void {
        $getStatus->getModel()->shouldBeCalledOnce()->willReturn($payment);

        $this->supports($getStatus)->shouldBe(true);
    }

    public function it_should_not_support_get_status_interface_request_with_model_not_instance_of_payment(
        GetStatusInterface $getStatus
    ): void {
        $getStatus->getModel()->shouldBeCalledOnce()->willReturn(new \stdClass());

        $this->supports($getStatus)->shouldBe(false);
    }

    public function it_should_not_support_request_not_instance_of_get_status_interface(): void
    {
        $this->supports(new \stdClass())->shouldBe(false);
    }

    public function it_should_throw_exception_if_request_is_not_supported_on_execute(): void
    {
        $this->shouldThrow(RequestNotSupportedException::class)->during('execute', [new \stdClass()]);
    }

    public function it_should_mark_request_new_if_dotpay_operation_status_is_missing_in_payment_details_on_execute(
        GetStatusInterface $getStatus,
        PaymentInterface $payment,
        HttpRequestFactory $httpRequestFactory,
        GetHttpRequest $getHttpRequest,
        GatewayInterface $gateway
    ): void {
        $getStatus->getModel()->shouldBeCalledTimes(2)->willReturn($payment);
        $payment->getDetails()->shouldBeCalledOnce()->willReturn([]);

        $httpRequestFactory->createGet()->shouldBeCalledOnce()->willReturn($getHttpRequest);

        $this->setGateway($gateway);

        $gateway->execute($getHttpRequest)->shouldBeCalledOnce();

        $getStatus->markNew()->shouldBeCalledOnce();

        $this->execute($getStatus);
    }

    public function it_should_mark_request_new_if_dotpay_operation_status_is_new_on_execute(
        GetStatusInterface $getStatus,
        PaymentInterface $payment,
        HttpRequestFactory $httpRequestFactory,
        GetHttpRequest $getHttpRequest,
        GatewayInterface $gateway
    ): void {
        $getStatus->getModel()->shouldBeCalledTimes(2)->willReturn($payment);
        $payment->getDetails()->shouldBeCalledOnce()->willReturn(['dotpay_operation_status' => 'new']);

        $httpRequestFactory->createGet()->shouldBeCalledOnce()->willReturn($getHttpRequest);

        $this->setGateway($gateway);

        $gateway->execute($getHttpRequest)->shouldBeCalledOnce();

        $getStatus->markNew()->shouldBeCalledOnce();

        $this->execute($getStatus);
    }

    public function it_should_mark_request_pending_if_dotpay_operation_status_is_processing_on_execute(
        GetStatusInterface $getStatus,
        PaymentInterface $payment,
        HttpRequestFactory $httpRequestFactory,
        GetHttpRequest $getHttpRequest,
        GatewayInterface $gateway
    ): void {
        $getStatus->getModel()->shouldBeCalledTimes(2)->willReturn($payment);
        $payment->getDetails()->shouldBeCalledOnce()->willReturn(['dotpay_operation_status' => 'processing']);

        $httpRequestFactory->createGet()->shouldBeCalledOnce()->willReturn($getHttpRequest);

        $this->setGateway($gateway);

        $gateway->execute($getHttpRequest)->shouldBeCalledOnce();

        $getStatus->markPending()->shouldBeCalledOnce();

        $this->execute($getStatus);
    }

    public function it_should_mark_request_pending_if_dotpay_operation_status_is_processing_realization_on_execute(
        GetStatusInterface $getStatus,
        PaymentInterface $payment,
        HttpRequestFactory $httpRequestFactory,
        GetHttpRequest $getHttpRequest,
        GatewayInterface $gateway
    ): void {
        $getStatus->getModel()->shouldBeCalledTimes(2)->willReturn($payment);
        $payment->getDetails()->shouldBeCalledOnce()->willReturn(
            ['dotpay_operation_status' => 'processing_realization']
        );

        $httpRequestFactory->createGet()->shouldBeCalledOnce()->willReturn($getHttpRequest);

        $this->setGateway($gateway);

        $gateway->execute($getHttpRequest)->shouldBeCalledOnce();

        $getStatus->markPending()->shouldBeCalledOnce();

        $this->execute($getStatus);
    }

    public function it_should_mark_request_pending_if_dotpay_operation_status_is_processing_realization_waiting_on_execute(
        GetStatusInterface $getStatus,
        PaymentInterface $payment,
        HttpRequestFactory $httpRequestFactory,
        GetHttpRequest $getHttpRequest,
        GatewayInterface $gateway
    ): void {
        $getStatus->getModel()->shouldBeCalledTimes(2)->willReturn($payment);
        $payment->getDetails()->shouldBeCalledOnce()->willReturn(
            ['dotpay_operation_status' => 'processing_realization_waiting']
        );

        $httpRequestFactory->createGet()->shouldBeCalledOnce()->willReturn($getHttpRequest);

        $this->setGateway($gateway);

        $gateway->execute($getHttpRequest)->shouldBeCalledOnce();

        $getStatus->markPending()->shouldBeCalledOnce();

        $this->execute($getStatus);
    }

    public function it_should_mark_request_captured_if_dotpay_operation_status_is_completed_on_execute(
        GetStatusInterface $getStatus,
        PaymentInterface $payment,
        HttpRequestFactory $httpRequestFactory,
        GetHttpRequest $getHttpRequest,
        GatewayInterface $gateway
    ): void {
        $getStatus->getModel()->shouldBeCalledTimes(2)->willReturn($payment);
        $payment->getDetails()->shouldBeCalledOnce()->willReturn(
            ['dotpay_operation_status' => 'completed']
        );

        $httpRequestFactory->createGet()->shouldBeCalledOnce()->willReturn($getHttpRequest);

        $this->setGateway($gateway);

        $gateway->execute($getHttpRequest)->shouldBeCalledOnce();

        $getStatus->markCaptured()->shouldBeCalledOnce();

        $this->execute($getStatus);
    }

    public function it_should_mark_request_failed_if_dotpay_operation_status_is_rejected_on_execute(
        GetStatusInterface $getStatus,
        PaymentInterface $payment,
        HttpRequestFactory $httpRequestFactory,
        GetHttpRequest $getHttpRequest,
        GatewayInterface $gateway
    ): void {
        $getStatus->getModel()->shouldBeCalledTimes(2)->willReturn($payment);
        $payment->getDetails()->shouldBeCalledOnce()->willReturn(
            ['dotpay_operation_status' => 'rejected']
        );

        $httpRequestFactory->createGet()->shouldBeCalledOnce()->willReturn($getHttpRequest);

        $this->setGateway($gateway);

        $gateway->execute($getHttpRequest)->shouldBeCalledOnce();

        $getStatus->markFailed()->shouldBeCalledOnce();

        $this->execute($getStatus);
    }

    public function it_should_mark_request_unknown_if_dotpay_operation_status_is_not_supported_on_execute(
        GetStatusInterface $getStatus,
        PaymentInterface $payment,
        HttpRequestFactory $httpRequestFactory,
        GetHttpRequest $getHttpRequest,
        GatewayInterface $gateway
    ): void {
        $getStatus->getModel()->shouldBeCalledTimes(2)->willReturn($payment);
        $payment->getDetails()->shouldBeCalledOnce()->willReturn(
            ['dotpay_operation_status' => 'dummy']
        );

        $httpRequestFactory->createGet()->shouldBeCalledOnce()->willReturn($getHttpRequest);

        $this->setGateway($gateway);

        $gateway->execute($getHttpRequest)->shouldBeCalledOnce();

        $getStatus->markUnknown()->shouldBeCalledOnce();

        $this->execute($getStatus);
    }
}
