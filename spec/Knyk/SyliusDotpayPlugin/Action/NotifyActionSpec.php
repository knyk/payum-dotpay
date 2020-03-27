<?php

declare(strict_types=1);

namespace spec\Knyk\SyliusDotpayPlugin\Action;

use Knyk\SyliusDotpayPlugin\Api\DotpayApi;
use Knyk\SyliusDotpayPlugin\Factory\ActionDataFactory;
use Knyk\SyliusDotpayPlugin\Factory\HttpRequestFactory;
use Knyk\SyliusDotpayPlugin\Formatter\MoneyFormatter;
use Knyk\SyliusDotpayPlugin\Model\NotifyActionData;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\InvalidArgumentException;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayInterface;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Request\Notify;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NotifyActionSpec extends ObjectBehavior
{
    public function let(
        MoneyFormatter $moneyFormatter,
        HttpRequestFactory $httpRequestFactory,
        ActionDataFactory $actionDataFactory
    ): void {
        $this->beConstructedWith($moneyFormatter, $httpRequestFactory, $actionDataFactory);
    }

    public function it_should_throw_exception_if_request_is_not_supported_on_execute(): void
    {
        $this->shouldThrow(RequestNotSupportedException::class)->during('execute', [new \stdClass()]);
    }

    public function it_should_throw_exception_if_status_is_already_set_in_payment_details_on_execute(
        Notify $notify,
        GatewayInterface $gateway,
        GetHttpRequest $getHttpRequest,
        HttpRequestFactory $httpRequestFactory
    ): void {
        $notify->getModel()->shouldBeCalledTimes(2)->willReturn(
            new ArrayObject(
                ['control' => 'controlstring', 'dotpay_operation_status' => 'dotpay_operation_statusstring']
            )
        );

        $this->setGateway($gateway);

        $httpRequestFactory->createGet()->shouldBeCalledOnce()->willReturn($getHttpRequest);

        $gateway->execute($getHttpRequest)->shouldBeCalledOnce();

        $this->shouldThrow(new NotFoundHttpException('Payment already confirmed.'))->during('execute', [$notify]);
    }

    public function it_should_throw_exception_if_control_is_missing_in_payment_details_on_execute(
        Notify $notify,
        GatewayInterface $gateway,
        GetHttpRequest $getHttpRequest,
        HttpRequestFactory $httpRequestFactory
    ): void {
        $notify->getModel()->shouldBeCalledTimes(2)->willReturn(new ArrayObject([]));

        $this->setGateway($gateway);

        $httpRequestFactory->createGet()->shouldBeCalledOnce()->willReturn($getHttpRequest);

        $gateway->execute($getHttpRequest)->shouldBeCalledOnce();

        $this->shouldThrow(new NotFoundHttpException('Control is missing.'))->during('execute', [$notify]);
    }

    public function it_should_throw_exception_if_control_is_invalid_on_execute(
        Notify $notify,
        GatewayInterface $gateway,
        GetHttpRequest $getHttpRequest,
        HttpRequestFactory $httpRequestFactory
    ): void {
        $notify->getModel()->shouldBeCalledTimes(2)->willReturn(
            new ArrayObject(['control' => 'invalid_controlstring'])
        );

        $this->setGateway($gateway);

        $httpRequestFactory->createGet()->shouldBeCalledOnce()->willReturn($getHttpRequest);

        $gateway->execute($getHttpRequest)->shouldBeCalledOnce();

        $getHttpRequest->request = [
            'control' => 'controlstring',
        ];

        $this->shouldThrow(new InvalidArgumentException('Control is invalid.'))->during('execute', [$notify]);
    }

    public function it_should_throw_exception_if_signature_is_invalid_on_execute(
        Notify $notify,
        GatewayInterface $gateway,
        GetHttpRequest $getHttpRequest,
        HttpRequestFactory $httpRequestFactory,
        ActionDataFactory $actionDataFactory,
        NotifyActionData $notifyActionData,
        DotpayApi $api
    ): void {
        $notify->getModel()->shouldBeCalledTimes(2)->willReturn(
            new ArrayObject(['control' => 'controlstring'])
        );

        $this->setGateway($gateway);

        $httpRequestFactory->createGet()->shouldBeCalledOnce()->willReturn($getHttpRequest);

        $gateway->execute($getHttpRequest)->shouldBeCalledOnce();

        $getHttpRequest->request = [
            'control' => 'controlstring',
            'signature' => 'invalid_checksumstring',
        ];

        $actionDataFactory->createNotifyActionData($getHttpRequest->request)->shouldBeCalledOnce()->willReturn(
            $notifyActionData
        );

        $this->setApi($api);

        $notifyActionData->toArray()->shouldBeCalledOnce()->willReturn(['foo' => 'bar']);

        $api->generateChecksum(['foo' => 'bar'])->shouldBeCalledOnce()->willReturn('checksumstring');

        $this->shouldThrow(new InvalidArgumentException('Signature is invalid.'))->during('execute', [$notify]);
    }

    public function it_should_throw_http_response_on_execute(
        Notify $notify,
        GatewayInterface $gateway,
        GetHttpRequest $getHttpRequest,
        HttpRequestFactory $httpRequestFactory,
        ActionDataFactory $actionDataFactory,
        NotifyActionData $notifyActionData,
        DotpayApi $api
    ): void {
        $notify->getModel()->shouldBeCalledTimes(2)->willReturn(new ArrayObject(['control' => 'controlstring']));

        $this->setGateway($gateway);

        $httpRequestFactory->createGet()->shouldBeCalledOnce()->willReturn($getHttpRequest);

        $gateway->execute($getHttpRequest)->shouldBeCalledOnce();

        $getHttpRequest->request = [
            'control' => 'controlstring',
            'signature' => 'checksumstring',
            'operation_number' => 'operation_numberstring',
            'operation_status' => 'operation_statusstring',
            'operation_amount' => 'operation_amountstring',
            'operation_currency' => 'operation_currencystring',
            'operation_original_amount' => 'operation_original_amountstring',
            'operation_original_currency' => 'operation_original_currencystring',
            'operation_datetime' => 'operation_datetimestring',
        ];

        $actionDataFactory->createNotifyActionData($getHttpRequest->request)->shouldBeCalledOnce()->willReturn(
            $notifyActionData
        );

        $this->setApi($api);

        $notifyActionData->toArray()->shouldBeCalledOnce()->willReturn(['foo' => 'bar']);

        $api->generateChecksum(['foo' => 'bar'])->shouldBeCalledOnce()->willReturn('checksumstring');

        $httpResponse = new HttpResponse('OK');

        $this->shouldThrow($httpResponse)->during('execute', [$notify]);
    }

    public function it_should_support_notify_request_with_array_model(
        Notify $notify,
        \ArrayAccess $model
    ): void {
        $notify->getModel()->shouldBeCalledOnce()->willReturn($model);

        $this->supports($notify)->shouldBe(true);
    }

    public function it_should_not_support_notify_request_with_model_not_type_of_array(Notify $notify): void
    {
        $notify->getModel()->shouldBeCalledOnce()->willReturn(new \stdClass());

        $this->supports($notify)->shouldBe(false);
    }

    public function it_should_not_support_request_not_instance_of_notify(): void
    {
        $this->supports(new \stdClass())->shouldBe(false);
    }
}
