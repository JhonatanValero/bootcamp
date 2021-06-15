<?php

namespace OmniPro\PayU\Gateway\Request;

class PayerBuilder implements \Magento\Payment\Gateway\Request\BuilderInterface
{
    /**
     * @param \Magento\Payment\Gateway\Helper\SubjectReader
     */
    private $subjectReader;

    /**
     * @param \Magento\Payment\Model\Method\Logger
     */
    private $logger;

    public function __construct(
        \Magento\Payment\Gateway\Helper\SubjectReader $subjectReader,
        \Magento\Payment\Model\Method\Logger $logger
    ) {
        $this->subjectReader = $subjectReader;
        $this->logger = $logger;
    }

    public function build(array $buildSubject)
    {
        $this->logger->debug([
            'Debug' => 'Payer Builder'
        ]);

        $payment = $this->subjectReader->readPayment($buildSubject);
        $order = $payment->getOrder();
        $billingAddress = $order->getBillingAddress();

        return [
            'transaction' => [
                'payer' => [
                    'merchantPayerId' => $order->getCustomerId(),
                    'fullName' => $billingAddress->getFirstname() . $billingAddress->getLastname(),
                    'emailAddress' => $billingAddress->getEmail(),
                    'contactPhone' => $billingAddress->getTelephone(),
                    'dniNumber' => '5415668464654',
                    'billingAddress' => [
                        'street1' => $billingAddress->getStreetLine1(),
                        'street2' => $billingAddress->getStreetLine2(),
                        'city' => $billingAddress->getCity(),
                        'state' => $billingAddress->getRegionCode(),
                        'country' => $billingAddress->getCountryId(),
                        'postalCode' => $billingAddress->getPostcode(),
                        'phone' => $billingAddress->getTelephone()
                    ]
                ]
            ]
        ];
    }
}
