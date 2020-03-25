# Payum Dotpay integration

## Installation
`composer install knyk/sylius-dotpay-plugin`

Add plugin to your `config/bundles.php` file:
```php
return [
    ...
    Knyk\SyliusDotpayPlugin\KnykSyliusDotpayPlugin::class => ['all' => true]
];
```
Clear cache by running `bin/console cache:clear`

## Usage
In order to enable Dotpay payment method You have to add new method in admin panel of Your Sylius shop.

To do that go to Admin > Payment methods > Create > Dotpay.

Then You will be asked to provide ID and PIN of Your Dotpay account.

## Sandbox
You can enable sandbox mode by switching Sandbox option in payment method edit form in admin panel.
