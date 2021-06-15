<?php

namespace OmniPro\PayU\Gateway\Validator;

use Exception;
use Magento\Framework\Exception\LocalizedException;
use \Magento\Payment\Gateway\Validator\ResultInterfaceFactory;

class ResponseCodeValidator extends \Magento\Payment\Gateway\Validator\AbstractValidator
{
    const PAYU_REJECTED = 'REJECTED';
    const PAYU_DECLINED = 'DECLINED';
    const PAYU_APPROVED = 'APPROVED';

    /**
     * @param \Magento\Payment\Gateway\Helper\SubjectReader
     */
    protected $subjectReader;

    /**
     * @param \Magento\Payment\Model\Method\Logger
     */
    private $logger;

    public function __construct(
        ResultInterfaceFactory $resultFactory,
        \Magento\Payment\Gateway\Helper\SubjectReader $subjectReader,
        \Magento\Payment\Model\Method\Logger $logger
    ) {
        parent::__construct($resultFactory);
        $this->subjectReader = $subjectReader;
        $this->logger = $logger;
    }

    public function validate(array $validationSubject)
    {
        $this->logger->debug([
            'Debug' => 'Validator'
        ]);

        $response = $this->subjectReader->readResponse($validationSubject);
        if (isset($response['transactionResponse']['state'])) {
            switch ($response['transactionResponse']['state']) {
                case self::PAYU_APPROVED:
                    return $this->createResult(true, []);
                    break;
                case self::PAYU_DECLINED:
                case self::PAYU_REJECTED:
                    return $this->createResult(false, [__($response['transactionResponse']['responseMessage']), $response['transactionResponse']['responseCode']]);
                    break;
            }
        } else {
            return $this->createResult(false, [__("Ha ocurrido un error procesando la transacción")]);
        }
    }
}