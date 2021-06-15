<?php
namespace OmniPro\Nmi\Model\Ui;

class ConfigProvider implements \Magento\Checkout\Model\ConfigProviderInterface
{
    const CODE = 'nmi';

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
