<?php
namespace OmniPro\Nmi\Gateway\Http\Converter;

class QueryParamsToArray implements \Magento\Payment\Gateway\Http\ConverterInterface
{
    /**
     * @param \Magento\Payment\Model\Method\Logger
     */
    private $logger;

    public function __construct(
        \Magento\Payment\Model\Method\Logger $logger
    )
    {
        $this->logger = $logger;
    }
    
    public function convert($response) {
        $this->logger->debug([
            'Debug' => 'Converter'
        ]);

        $this->logger->debug($response);

        $resp = explode("&",$response);

        $this->logger->debug($resp);

        for($i=0;$i<count($resp);$i++) {
            $rdata = explode("=",$resp[$i]);
            $bodyResponse[$rdata[0]] = $rdata[1];
        }

        $this->logger->debug([
            'bodyResponse' => $bodyResponse
        ]);

        return $bodyResponse;
    }
}
