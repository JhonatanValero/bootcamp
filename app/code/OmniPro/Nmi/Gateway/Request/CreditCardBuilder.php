<?php
namespace OmniPro\Nmi\Gateway\Request;

class CreditCardBuilder implements \Magento\Payment\Gateway\Request\BuilderInterface
{
    /**
     * @param \Magento\Payment\Gateway\Helper\SubjectReader
     */
    private $subjectReader;

    public function __construct(
        \Magento\Payment\Gateway\Helper\SubjectReader $subjectReader
    )
    {
        $this->subjectReader = $subjectReader;
        
    }

    public function build(array $buildSubject) {
        $payment = $this->subjectReader->readPayment($buildSubject);
        $paymentDO = $payment->getPayment();
        $CcNumber = $paymentDO->getAdditionalInformation('cc_number');
        $cvv = $paymentDO->getAdditionalInformation('cc_cid');

        $paymentDO->unsAdditionalInformation('cc_number');
        $paymentDO->unsAdditionalInformation('cc_cid');

        return [
            'ccnumber' => $paymentDO->decrypt($CcNumber),
            'ccexp' => sprintf('%02d', $paymentDO->getCcExpMonth()).substr($paymentDO->getCcExpYear(), 2, 2),
            'cvv' => $paymentDO->decrypt($cvv)
        ];
    }
}
