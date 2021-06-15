<?php
namespace OmniPro\PayU\Gateway\Request;

class CreditCardBuilder implements \Magento\Payment\Gateway\Request\BuilderInterface
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
    )
    {
        $this->subjectReader = $subjectReader;
        $this->logger = $logger;
        
    }

    public function build(array $buildSubject) {
        $this->logger->debug([
            'Debug' => 'CC Builder'
        ]);

        $payment = $this->subjectReader->readPayment($buildSubject);
        $paymentDO = $payment->getPayment();
        $ccNumber = $paymentDO->getAdditionalInformation('cc_number');
        $ccCvv = $paymentDO->getAdditionalInformation('cc_cid');
        $ccExpMonth = $paymentDO->getCcExpMonth();
        $ccExpYear = $paymentDO->getCcExpYear();

        $paymentDO->unsAdditionalInformation('cc_number');
        $paymentDO->unsAdditionalInformation('cc_cid');
        $paymentDO->unsAdditionalInformation('cc_exp_month');
        $paymentDO->unsAdditionalInformation('cc_exp_year');
        
        return [
            'transaction' => [
                'creditCard' => [
                    'number' => $paymentDO->decrypt($ccNumber),
                    'securityCode' => $paymentDO->decrypt($ccCvv),
                    'expirationDate' => $ccExpYear.'/'.sprintf('%02d', $ccExpMonth),
                    'name' => 'Test'
                ]
            ]
        ];
    }
}
