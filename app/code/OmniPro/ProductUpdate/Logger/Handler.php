<?php
namespace OmniPro\ProductUpdate\Logger;

class Handler extends \Magento\Framework\Logger\Handler\Base
{
    protected $loggerType = Logger::DEBUG;

    protected $fileName = '/var/log/product_update.log';
}
