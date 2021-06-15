<?php
namespace OmniPro\ProductUpdate\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ProductUpdate extends Command
{
    /**
     * @param \OmniPro\ProductUpdate\Model\ProductUpdate
     */
    private $productUpdate;

    /**
    * @var \Magento\Framework\App\State 
    */
    private $state;


    /**
     * @param \OmniPro\ProductUpdate\Helper\Email
     */
    private $email;

    public function __construct(
        \Magento\Framework\App\State $state,
        \OmniPro\ProductUpdate\Model\ProductUpdate $productUpdate,
        \OmniPro\ProductUpdate\Helper\Email $email
    )
    {
        $this->state = $state;
        $this->productUpdate = $productUpdate;
        $this->email = $email;
        parent::__construct();
    }
    
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('omnipro:product-update');
        $this->setDescription('Product update from csv.');
        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return null|int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND);
        $contador = $this->productUpdate->process();
        $this->email->sendEmail($contador['errors'], $contador['createdProduct'], $contador['updatedProduct']);
        $output->writeln('');
    }
}
