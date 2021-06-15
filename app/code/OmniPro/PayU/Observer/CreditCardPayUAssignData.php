<?php
namespace OmniPro\PayU\Observer;

use Magento\Quote\Api\Data\PaymentInterface;

class CreditCardPayUAssignData extends \Magento\Payment\Observer\AbstractDataAssignObserver
{
    const CCNUMBER = 'cc_number';
    const CCEXPMONTH = 'cc_exp_month';
    const CCEXPYEAR = 'cc_exp_year';
    const CCTYPE = 'cc_type';
    const CVV = 'cc_cid';

    protected $additionalInformationList = [
        self::CCNUMBER,
        self::CCEXPMONTH,
        self::CCEXPYEAR,
        self::CVV,
        self::CCTYPE
    ];

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $data = $this->readDataArgument($observer);

        $additionalData = $data->getData(PaymentInterface::KEY_ADDITIONAL_DATA);
        if (!is_array($additionalData)) {
            return;
        }

        $paymentInfo = $this->readPaymentModelArgument($observer);

        foreach ($this->additionalInformationList as $additionalInformationKey) {
            if (isset($additionalData[$additionalInformationKey])) {
                $paymentInfo->setAdditionalInformation(
                    $additionalInformationKey,
                    $paymentInfo->encrypt($additionalData[$additionalInformationKey])
                );

                if($additionalInformationKey !== self::CCNUMBER || $additionalInformationKey !== self::CVV) {
                    $paymentInfo->setData($additionalInformationKey, $additionalData[$additionalInformationKey]);
                } else {
                    $paymentInfo->setCcLast4(substr($additionalData[$additionalInformationKey], -4));
                }
            }
        }
    }
}