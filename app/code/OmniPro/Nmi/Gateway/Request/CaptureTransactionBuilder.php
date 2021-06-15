<?php
namespace OmniPro\Nmi\Gateway\Request;

use Magento\Sales\Model\Order\Payment;

class CaptureTransactionBuilder implements \Magento\Payment\Gateway\Request\BuilderInterface
{
    /**
     * @param \Magento\Payment\Gateway\Helper\SubjectReader
     */
    private $subjectReader;

    /**
     * @param \Magento\Payment\Gateway\ConfigInterface
     */
    private $config;

    public function __construct(
        \Magento\Payment\Gateway\Helper\SubjectReader $subjectReader,
        \Magento\Payment\Gateway\ConfigInterface $config
    )
    {
        $this->subjectReader = $subjectReader;
        $this->config = $config;
        
    }

    public function build(array $buildSubject) {
        $payment = $this->subjectReader->readPayment($buildSubject);
        
        /** @var Payment $paymentDO */
        $paymentDO = $payment->getPayment();
        $amount = $this->subjectReader->readAmount($buildSubject);
        $order = $payment->getOrder();
        $storeId = $order->getStoreId();

        return [
            'type' => 'capture',
            'security_key' => $this->config->getValue('security_key', $storeId),
            'transactionid' => $paymentDO->getParentTransactionId(),
            'amount' => $amount,
            'orderid' => $order->getOrderIncrementId()
        ];
    }
}
