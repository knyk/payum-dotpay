<?php

declare(strict_types=1);

namespace Knyk\SyliusDotpayPlugin\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

final class SyliusGatewayConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'id',
            TextType::class,
            [
                'constraints' => [
                    new NotBlank(),
                ],
            ]
        );

        $builder->add(
            'pin',
            TextType::class,
            [
                'constraints' => [
                    new NotBlank(),
                ],
            ]
        );

        $builder->add('sandbox', CheckboxType::class);
        $builder->add('ignoreLastPaymentChannel', CheckboxType::class);
    }
}
