<?php

declare(strict_types=1);

namespace Knyk\SyliusDotpayPlugin\Model;

class NotifyActionData
{
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function toArray(): array
    {
        return [
            $this->data['id'],
            $this->data['operation_number'],
            $this->data['operation_type'],
            $this->data['operation_status'],
            $this->data['operation_amount'],
            $this->data['operation_currency'],
            $this->data['operation_withdrawal_amount'] ?? null,
            $this->data['operation_commission_amount'] ?? null,
            $this->data['is_completed'] ?? null,
            $this->data['operation_original_amount'],
            $this->data['operation_original_currency'],
            $this->data['operation_datetime'],
            $this->data['operation_related_number'] ?? null,
            $this->data['control'],
            $this->data['description'],
            $this->data['email'],
            $this->data['p_info'],
            $this->data['p_email'],
            $this->data['credit_card_issuer_identification_number'] ?? null,
            $this->data['credit_card_masked_number'] ?? null,
            $this->data['credit_card_brand_codename'] ?? null,
            $this->data['credit_card_brand_code'] ?? null,
            $this->data['credit_card_id'] ?? null,
            $this->data['channel'],
            $this->data['channel_country'] ?? null,
            $this->data['geoip_country'] ?? null,
        ];
    }
}
