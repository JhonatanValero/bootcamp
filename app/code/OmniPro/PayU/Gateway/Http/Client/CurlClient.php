<?php

namespace OmniPro\PayU\Gateway\Http\Client;

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
    ) {
        $this->logger = $logger;
        $this->curl = $curl;
        $this->converter = $converter;
    }

    public function placeRequest(TransferInterface $transferObject)
    {
        $this->logger->debug([
            'Debug' => 'Client'
        ]);

        $log = [
            'request' => $transferObject->getBody(),
            'request_uri' => $transferObject->getUri()
        ];

        $result = [];

        try {
            /**
             * Revisar Uso de serialize para Json
             */

            $this->curl->addHeader('Content-Type', 'application/json');
            $this->curl->addHeader('Accept', 'application/json');
            $this->curl->setTimeout(60);
            $this->curl->post($transferObject->getUri(), json_encode($transferObject->getBody(), JSON_UNESCAPED_SLASHES));
            $result = json_decode($this->curl->getBody(), true);
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
