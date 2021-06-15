<?php
namespace OmniPro\ProductUpdate\Helper;

class Email extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @param \Magento\Framework\Escaper
     */
    private $escaper;

    /**
     * @param \Magento\Framework\Mail\Template\TransportBuilder
     */
    private $transportBuilder;

    /**
     * @param Magento\Framework\Translate\Inline\StateInterface
     */
    private $inlineTranslation;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
    )
    {
        $this->inlineTranslation = $inlineTranslation;
        $this->escaper = $escaper;
        $this->transportBuilder = $transportBuilder;
        $this->logger = $context->getLogger();
        parent::__construct($context);
    }

    public function sendEmail($errors, $createdProduct, $updatedProduct) {
        $template = $this->scopeConfig->getValue('omnipro_product_update/general/email_template');
        $receiver = $this->scopeConfig->getValue('omnipro_product_update/general/email_receiver');
        try {
            $this->inlineTranslation->suspend();
            $sender = [
                'name' => $this->escaper->escapeHtml('Test'),
                'email' => $this->escaper->escapeHtml('jhonatan.valero@omni.pro'),
            ];
            $transport = $this->transportBuilder
                ->setTemplateIdentifier($template)
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars([
                    'templateVar'  => 'My Topic',
                    'errors'  => $errors,
                    'createdProduct'  => $createdProduct,
                    'updatedProduct'  => $updatedProduct,
                ])
                ->setFrom($sender)
                ->addTo($receiver)
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }
    }
}
