<?php
namespace OmniPro\PayU\Model\Ui;

class ConfigProvider implements \Magento\Checkout\Model\ConfigProviderInterface
{
    const CODE = 'payu';

    public function getConfig() {
        return [
            'payment' => [
                self::CODE => [
                    'threedsecure' => true
                ]
            ]
        ];
    }
}
