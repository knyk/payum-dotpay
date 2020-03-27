<?php

declare(strict_types=1);

namespace Knyk\SyliusDotpayPlugin\Action;

use Knyk\SyliusDotpayPlugin\Api\DotpayApi;
use Knyk\SyliusDotpayPlugin\Factory\ActionDataFactory;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\ApiAwareTrait;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Reply\HttpPostRedirect;
use Payum\Core\Request\Capture;
use Payum\Core\Security\GenericTokenFactoryAwareInterface;
use Payum\Core\Security\GenericTokenFactoryAwareTrait;
use Payum\Core\Security\TokenInterface;
use Sylius\Component\Core\Model\PaymentInterface;

final class CaptureAction implements ActionInterface, ApiAwareInterface, GenericTokenFactoryAwareInterface
{
    use ApiAwareTrait;
    use GenericTokenFactoryAwareTrait;

    /**
     * @var DotpayApi
     */
    protected $api;
    private ActionDataFactory $actionDataFactory;

    public function __construct(ActionDataFactory $actionDataFactory)
    {
        $this->apiClass = DotpayApi::class;
        $this->actionDataFactory = $actionDataFactory;
    }

    /**
     * @param Capture $request
     */
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var PaymentInterface $payment */
        $payment = $request->getModel();

        /** @var TokenInterface $token */
        $token = $request->getToken();

        $notifyToken = $this->tokenFactory->createNotifyToken($token->getGatewayName(), $token->getDetails());

        $captureActionData = $this->actionDataFactory->createCaptureActionData(
            $request,
            $this->api,
            $notifyToken
        );

        $payment->setDetails(['control' => $captureActionData->control()]);

        throw new HttpPostRedirect($this->api->host(), $captureActionData->toArray());
    }

    public function supports($request): bool
    {
        return $request instanceof Capture && $request->getModel() instanceof PaymentInterface;
    }
}
