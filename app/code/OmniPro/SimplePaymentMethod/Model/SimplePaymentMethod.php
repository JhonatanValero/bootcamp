<?php
namespace OmniPro\SimplePaymentMethod\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Model\InfoInterface;

class SimplePaymentMethod extends \Magento\Payment\Model\Method\Cc
{
    const NMI_APPROVED = 1;
    const NMI_DECLINED = 2;
    const NMI_ERROR = 3;

    protected $_code = 'simplepayment';

    protected $_isOffline = false;

    protected $_isGateway = true;

    protected $_canAuthorize = true;
    
    protected $_canCapture = true;

    protected $_canRefund = true;

    protected $_canVoid = true;

    /**
     * @param \Magento\Framework\HTTP\Client\Curl
     */
    private $curl;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->curl = $curl;
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $moduleList,
            $localeDate,
            $resource,
            $resourceCollection,
            $data
        );
    }

    public function authorize(InfoInterface $payment, $amount)
    {
        $result = $this->authorizeRequest($payment, $amount);
        switch ($result['response']) {
            case self::NMI_APPROVED:
                $payment->setTransactionId($result['transactionid'])
                        ->setLastTransactionId($result['transactionid'])
                        ->setParentTransactionId($result['transactionid'])
                        ->setTransactionClosed();
                break;
            case self::NMI_DECLINED:
            case self::NMI_ERROR:
                throw new LocalizedException(__($result['responsetext']));
                break;
        }
        return $this;
    }

    protected function authorizeRequest($payment, $amount) {
        $order = $payment->getOrder();

        $data = [
            'type' => 'auth',
            'security_key' => $this->getConfigData('security_key'),
            'ccnumber' => $payment->getCcNumber(),
            'ccexp' => sprintf('%02d', $payment->getCcExpMonth()).substr($payment->getCcExpYear(), 2, 2),
            'amount' => $amount,
            'orderid' => $order->getIncrementId()
        ];
        
        $amp = '';
        $postRequestData = '';
        foreach ($data as $key => $value) {
            if (!empty($value)) {
                $postRequestData .= $amp . urlencode($key) . '=' . urlencode($value);
                $amp = '&';
            }
        }
        $this->curl->get($this->getConfigData('nmi_gateway') . '?' . $postRequestData);
        $response = $this->curl->getBody();
        $response = explode("&",$response);
        for($i=0;$i<count($response);$i++) {
            $rdata = explode("=",$response[$i]);
            $responses[$rdata[0]] = $rdata[1];
        }
        return $responses;
    }
}
