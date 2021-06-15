<?php
namespace OmniPro\Nmi\Gateway\Response;

use Magento\Sales\Model\Order\Payment;
class AuthorizationHandler implements \Magento\Payment\Gateway\Response\HandlerInterface
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

    public function handle(array $handlingSubject, array $response) {
        $payment = $this->subjectReader->readPayment($handlingSubject);
        /** @var Payment $paymentDO */
        $paymentDO = $payment->getPayment();

        $paymentDO->setTransactionId($response['transactionid']);
        $paymentDO->setLastTransactionId($response['transactionid']);
        $paymentDO->setParentTransactionId($response['transactionid']);
        $paymentDO->setIsTransactionClosed(0);
    }
}
