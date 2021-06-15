<?php
namespace OmniPro\PayU\Gateway\Request;

class DefaultBuilder implements \Magento\Payment\Gateway\Request\BuilderInterface
{
    const LANGUAJE = 'es';
    const COMMAND = 'SUBMIT_TRANSACTION';

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
    )
    {
        $this->subjectReader = $subjectReader;
        $this->logger = $logger;        
        $this->config = $config;
    }

    public function build(array $buildSubject) {
        $this->logger->debug([
            'Debug' => 'Default Builder'
        ]);

        $payment = $this->subjectReader->readPayment($buildSubject);
        $order   = $payment->getOrder();
        $storeId = $order->getStoreId();

        return [
            'test' => true,
            'language' => self::LANGUAJE,
            'command' => self::COMMAND,
            'merchant' => [
                'apiKey' => $this->config->getValue('api_key', $storeId),
                'apiLogin' => $this->config->getValue('api_login', $storeId)
            ]
        ];
    }
}