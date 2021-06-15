<?php

namespace OmniPro\PayU\Gateway\Request;

use Braintree\Merchant;
use Magento\TestFramework\Helper\Api;

class OrderBuilder implements \Magento\Payment\Gateway\Request\BuilderInterface
{
    const LANGUAJE = 'es';
    const MERCHANTID = '508029';

    /**
     * @param \Magento\Payment\Gateway\Helper\SubjectReader
     */
    private $subjectReader;

    /**
     * @param \Magento\Payment\Model\Method\Logger
     */
    private $logger;

    /**
     * @param \Magento\Payment\Gateway\ConfigInterface
     */
    private $config;

    public function __construct(
        \Magento\Payment\Gateway\Helper\SubjectReader $subjectReader,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Payment\Gateway\ConfigInterface $config
    ) {
        $this->subjectReader = $subjectReader;
        $this->logger = $logger;
        $this->config = $config;
    }

    public function build(array $buildSubject)
    {
        $this->logger->debug([
            'Debug' => 'Order Builder'
        ]);

        $payment = $this->subjectReader->readPayment($buildSubject);
        $amount = $this->subjectReader->readAmount($buildSubject);
        $order = $payment->getOrder();
        $shippingAddress = $order->getShippingAddress();
        $paymentDO = $payment->getPayment();
        $storeId = $order->getStoreId();

        $apiKey = $this->config->getValue('api_key', $storeId);
        $referenceCode = $order->getOrderIncrementId().'V2x4';
        
        return [
            'transaction' => [
                'order' => [
                    'accountId' => $this->config->getValue('account_id', $storeId),
                    'referenceCode' => $referenceCode,
                    'description' => 'test',
                    'language' => self::LANGUAJE,
                    'signature' => $this->createSignature($apiKey, self::MERCHANTID, $referenceCode, $amount, $order->getCurrencyCode()),
                    'additionalValues' => [
                        'TX_VALUE' => [
                            'value' => $amount,
                            'currency' => $order->getCurrencyCode()
                        ]
                    ],
                    'buyer' => [
                        'merchantBuyerId' => $order->getCustomerId(),
                        'fullName' => $shippingAddress->getFirstname() . $shippingAddress->getLastname(),
                        'emailAddress' => $shippingAddress->getEmail(),
                        'contactPhone' => $shippingAddress->getTelephone(),
                        'dniNumber' => '5415668464654'
                    ],
                    'shippingAddress' => [
                        'street1' => $shippingAddress->getStreetLine1(),
                        'street2' => $shippingAddress->getStreetLine2(),
                        'city' => $shippingAddress->getCity(),
                        'state' => $shippingAddress->getRegionCode(),
                        'country' => $shippingAddress->getCountryId(),
                        'postalCode' => $shippingAddress->getPostcode(),
                        'phone' => $shippingAddress->getTelephone()
                    ]
                ]
            ]
        ];
    }

    public function createSignature($apiKey, $merchantId, $referenceCode, $amount, $currency) {
        return md5($apiKey.'~'.$merchantId.'~'.$referenceCode.'~' . $amount.'~'.$currency);
    }
}

