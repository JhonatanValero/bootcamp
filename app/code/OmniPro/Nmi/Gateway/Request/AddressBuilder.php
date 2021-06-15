<?php
namespace OmniPro\Nmi\Gateway\Request;

class AddressBuilder implements \Magento\Payment\Gateway\Request\BuilderInterface
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

    public function build(array $buildSubject) {
        $payment = $this->subjectReader->readPayment($buildSubject);
        $order = $payment->getOrder();
        $billingAddress = $order->getBillingAddress();
        $shippingAddress = $order->getShippingAddress() ?? $billingAddress;

        return [
            'firstname' => $billingAddress->getFirstname(),
            'lastname' => $billingAddress->getLastname(),
            'company' => $billingAddress->getCompany(),
            'address1' => $billingAddress->getStreetLine1(),
            'address2' => $billingAddress->getStreetLine2(),
            'city' => $billingAddress->getCity(),
            'state' => $billingAddress->getRegionCode(),
            'zip' => $billingAddress->getPostcode(),
            'country' => $billingAddress->getCountryId(),
            'phone' => $billingAddress->getTelephone(),
            'email' => $billingAddress->getEmail(),
            'shipping_firstname' => $shippingAddress->getFirstname(),
            'shipping_lastname' => $shippingAddress->getLastname(),
            'shipping_company' => $shippingAddress->getCompany(),
            'shipping_address1' => $shippingAddress->getStreetLine1(),
            'shipping_address2' => $shippingAddress->getStreetLine2(),
            'shipping_city' => $shippingAddress->getCity(),
            'shipping_state' => $shippingAddress->getRegionCode(),
            'shipping_zip' => $shippingAddress->getPostcode(),
            'shipping_country' => $shippingAddress->getCountryId(),
        ];
    }
}
