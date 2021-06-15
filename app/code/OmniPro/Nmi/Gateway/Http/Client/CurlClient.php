<?php
namespace OmniPro\Nmi\Gateway\Http\Client;

use Magento\Payment\Gateway\Http\TransferInterface;

class CurlClient implements \Magento\Payment\Gateway\Http\ClientInterface
{
    /**
     * @param \Magento\Payment\Model\Method\Logger
     */
    private $logger;

    /**
     * @param \Magento\Payment\Gateway\Http\ConverterInterface
     */
    private $converter;

    /**
     * @param \Magento\Framework\HTTP\Client\Curl
     */
    private $curl;

    public function __construct(
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Payment\Gateway\Http\ConverterInterface $converter = null
    )
    {
        $this->logger = $logger;
        $this->curl = $curl;
        $this->converter = $converter;
        
    }

    public function placeRequest(TransferInterface $transferObject) {
        $this->logger->debug([
            'Debug' => 'Client'
        ]);

        $log = [
            'request' => $transferObject->getBody(),
            'request_uri' => $transferObject->getUri()
        ];

        $result = [];

        $this->logger->debug($log);
    
        try {
            $this->curl->get($transferObject->getBody());
            $result = $this->converter
                ? $this->converter->convert($this->curl->getBody())
                : [$this->curl->getBody()];
            $log['response'] = $result;
        } catch (\Exception $e) {
            throw new \Magento\Payment\Gateway\Http\ClientException(
                __($e->getMessage())
            );
        } catch (\Magento\Payment\Gateway\Http\ConverterException $e) {
            throw $e;
        } finally {
            $this->logger->debug($log);
        }

        return $result;
    }
}
