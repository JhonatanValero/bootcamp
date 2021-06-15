<?php
namespace OmniPro\PayU\Gateway\Response;

use Magento\Sales\Model\Order\Payment;

class AuthorizationHandler implements \Magento\Payment\Gateway\Response\HandlerInterface
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

    public function handle(array $handlingSubject, array $response) {
        $this->logger->debug([
            'Debug' => 'Handler'
        ]);

        $this->logger->debug($response);

        $payment = $this->subjectReader->readPayment($handlingSubject);
        /** @var Payment $paymentDO */
        $paymentDO = $payment->getPayment();

        $paymentDO->setTransactionId($response['transactionResponse']['orderId']);
        $paymentDO->setLastTransactionId($response['transactionResponse']['orderId']);
        $paymentDO->setParentTransactionId($response['transactionResponse']['orderId']);
        $paymentDO->setIsTransactionClosed(0);
    }
}
