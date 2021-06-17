<?php
namespace OmniPro\RetoFinal\Model;

class Product
{
    /**
     * @param \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @param \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @param \Magento\Catalog\Api\Data\ProductInterfaceFactory
     */
    private $productInterfaceFactory;

    /**
     * @param \Magento\Catalog\Api\SpecialPriceInterface
     */
    private $specialPrice;

    /**
     * @param \Magento\Catalog\Api\Data\SpecialPriceInterfaceFactory
     */
    private $specialPriceInterfaceFactory;

    /**
     * @param \Magento\InventoryApi\Api\SourceItemsSaveInterface
     */
    private $sourceItemsSave;

    /**
     * @param \Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory
     */
    private $sourceItemInterfaceFactory;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Api\Data\ProductInterfaceFactory $productInterfaceFactory,
        \Magento\Catalog\Api\SpecialPriceInterface $specialPrice,
        \Magento\Catalog\Api\Data\SpecialPriceInterfaceFactory $specialPriceInterfaceFactory,
        \Magento\InventoryApi\Api\SourceItemsSaveInterface $sourceItemsSave,
        \Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory $sourceItemInterfaceFactory
    )
    {
        $this->logger = $logger;
        $this->productRepository = $productRepository;
        $this->productInterfaceFactory = $productInterfaceFactory;
        $this->specialPrice = $specialPrice;
        $this->specialPriceInterfaceFactory = $specialPriceInterfaceFactory;        
        $this->sourceItemsSave = $sourceItemsSave;
        $this->sourceItemInterfaceFactory = $sourceItemInterfaceFactory;
    }

    public function getPost($params) {

        // /**
        //  * @var \Magento\Catalog\Model\Product $dataProduct
        //  */
        // $dataProduct = $this->productInterfaceFactory->create();
        // $dataProduct->setName("Nombre");
        // $dataProduct->setSku("Sku");
        // $dataProduct->setAttributeSetId(4);
        // $dataProduct->setTypeId('simple');
        // $dataProduct->setVisibility(4);
        // $dataProduct->setPrice("Price");
        // $dataProduct->setUrlKey("UrlKey");
        // $this->productRepository->save($dataProduct);

        // $updateDatetime = new \DateTime();
        // $prices[] = $this->specialPriceInterfaceFactory->create()
        //     ->setSku($dataProduct->getSku())
        //     ->setStoreId(0)
        //     ->setPrice("SpécialPrice")
        //     ->setPriceFrom($updateDatetime->modify("From")->format('Y-m-d H:i:s'))
        //     ->setPriceTo($updateDatetime->modify("To")->format('Y-m-d H:i:s'));
        // $this->specialPrice->update($prices);
        // 
        // $sourceItem = $this->sourceItemInterfaceFactory->create();
        // $sourceItem->setSourceCode('col_bol');
        // $sourceItem->setSku("24-MB01");
        // $sourceItem->setQuantity(10);
        // $sourceItem->setStatus(0);
        // $this->sourceItemsSave->execute([$sourceItem]); 

        try {
            $response = ['success' => true, 'message' => $params];
        } catch (\Exception $e) {
            $response = ['success' => false, 'message' => $e->getMessage()];
            $this->logger->info($e->getMessage());
        }
        $returnArray = json_encode($response);
        return $returnArray;
    }
}
