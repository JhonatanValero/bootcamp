<?php
namespace OmniPro\PayU\Gateway\Request;

use Magento\Payment\Test\Unit\Block\Info\CcTest;

class AuthorizeTransactionBuilder implements \Magento\Payment\Gateway\Request\BuilderInterface
{
    /**
     * @param \Magento\Payment\Gateway\Helper\SubjectReader
     */
    private $subjectReader;

    /**
     * @param \Magento\Payment\Gateway\ConfigInterface
     */
    private $config;

    /**
     * @param \Magento\Payment\Model\Method\Logger
     */
    private $logger;

    public function __construct(
        \Magento\Payment\Gateway\Helper\SubjectReader $subjectReader,
        \Magento\Payment\Gateway\ConfigInterface $config,
        \Magento\Payment\Model\Method\Logger $logger
    )
    {
        $this->subjectReader = $subjectReader;
        $this->config = $config;
        $this->logger = $logger;
        
    }

    public function build(array $buildSubject) {
        $this->logger->debug([
            'Debug' => 'Authorize Builder'
        ]);

        $payment = $this->subjectReader->readPayment($buildSubject);
        $order = $payment->getOrder();
        $paymentDO = $payment->getPayment();
        $ccType = $paymentDO->getAdditionalInformation('cc_type');

        $paymentDO->unsAdditionalInformation('cc_type');

        return [
            'transaction' => [
                'type' => 'AUTHORIZATION_AND_CAPTURE',
                'paymentMethod' => $this->getCodeCc($paymentDO->decrypt($ccType)),
                // 'paymentMethod' => $paymentDO->decrypt($ccType),
                'paymentCountry' => $order->getBillingAddress()->getCountryId(),
                'deviceSessionId' => 'vghs6tvkcle931686k1900o6e1',
                'ipAddress' => $order->getRemoteIp(),
                'cookie' => 'pt1t38347bs6jc9ruv2ecpv7o2',
                'userAgent' => 'Mozilla/5.0 (Windows NT 5.1; rv =>18.0) Gecko/20100101 Firefox/18.0',
            ]
        ];
    }

    public function getCodeCc($value) {
        switch($value) 
        {
            case 'VI':
                return 'VISA';
            case 'AE':
                return 'AMEX';
            case 'MC':
                return 'MASTERCARD';
            case 'DN':
                return 'DINERS';
        }
    }
}
