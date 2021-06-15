<?php

namespace OmniPro\ProductUpdate\Cron;

class ProductUpdate
{
    private $productUpdate;

    /**
     * @param \OmniPro\ProductUpdate\Logger\Logger
     */
    private $logger;

    /**
     * @param \OmniPro\ProductUpdate\Helper\Email
     */
    private $email;

    public function __construct(
        \OmniPro\ProductUpdate\Model\ProductUpdate $productUpdate,
        \OmniPro\ProductUpdate\Logger\Logger $logger,
        \OmniPro\ProductUpdate\Helper\Email $email
    ) {
        $this->productUpdate = $productUpdate;
        $this->logger = $logger;
        $this->email = $email;
    }

    public function execute()
    {
        $contador = $this->productUpdate->process();
        $this->email->sendEmail($contador['errors'], $contador['createdProduct'], $contador['updatedProduct']);
        $this->logger->debug('Cron productUpdate ejecutado');
    }
}
