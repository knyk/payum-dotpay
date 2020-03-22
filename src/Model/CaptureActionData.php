<?php

declare(strict_types=1);

namespace Knyk\SyliusDotpayPlugin\Model;

class CaptureActionData
{
    private string $apiVersion;
    private string $id;
    private string $amount;
    private string $currency;
    private string $url;
    private string $type;
    private string $urlc;
    private string $description;
    private int $ignoreLastPaymentChannel;
    private string $chk;

    public function __construct(
        string $apiVersion,
        string $id,
        string $amount,
        string $currency,
        string $url,
        string $type,
        string $urlc,
        string $description,
        int $ignoreLastPaymentChannel,
        string $chk
    ) {
        $this->apiVersion = $apiVersion;
        $this->id = $id;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->url = $url;
        $this->type = $type;
        $this->urlc = $urlc;
        $this->description = $description;
        $this->ignoreLastPaymentChannel = $ignoreLastPaymentChannel;
        $this->chk = $chk;
    }

    public function toArray(): array
    {
        return [
            'api_version' => $this->apiVersion,
            'id' => $this->id,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'description' => $this->description,
            'url' => $this->url,
            'type' => $this->type,
            'urlc' => $this->urlc,
            'ignore_last_payment_channel' => $this->ignoreLastPaymentChannel,
            'chk' => $this->chk,
        ];
    }
}
