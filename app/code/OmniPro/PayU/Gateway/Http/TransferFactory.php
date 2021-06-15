<?php
namespace OmniPro\PayU\Gateway\Http;

class TransferFactory implements \Magento\Payment\Gateway\Http\TransferFactoryInterface
{
    /**
     * @param \Magento\Payment\Gateway\Http\TransferBuilder
     */
    private $transferBuilder;

    /**
     * @param \Magento\Payment\Gateway\ConfigInterface
     */
    private $config;

    /**
     * @param \Magento\Payment\Model\Method\Logger
     */
    private $logger;

    public function __construct(
        \Magento\Payment\Gateway\Http\TransferBuilder $transferBuilder,
        \Magento\Payment\Gateway\ConfigInterface $config,
        \Magento\Payment\Model\Method\Logger $logger
    )
    {
        $this->transferBuilder = $transferBuilder;
        $this->config = $config;
        $this->logger = $logger;
    }

    public function create(array $request) {
        $this->logger->debug([
            'Debug' => 'Tranfer Factory'
        ]);

        return $this->transferBuilder
            ->setBody($request)
            ->setMethod('POST')
            ->setUri($this->config->getValue('payu_gateway'))
            ->build();
    }
}
