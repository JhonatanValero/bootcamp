<?php

namespace OmniPro\ProductUpdate\Model;

class ProductUpdate
{
    /**
     * @param \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @param \Magento\Catalog\Api\Data\ProductInterfaceFactory
     */
    private $productInterfaceFactory;

    /**
     * @param \OmniPro\ProductUpdate\Logger\Logger
     */
    private $logger;

    /**
     * @param \Magento\Catalog\Api\Data\SpecialPriceInterfaceFactory
     */
    private $specialPriceInterfaceFactory;

    /**
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    private $stockRegistry;

    /**
     * @param \Magento\Catalog\Model\Product\Action
     */
    private $action;

    /**
     * @param \Magento\Catalog\Api\SpecialPriceInterface
     */
    private $specialPrice;

    /**
     * @param \OmniPro\ProductUpdate\Helper\readerCsv
     */
    private $readerCsv;

    /**
     * @param \Magento\Catalog\Model\Product
     */
    private $product;

    public function __construct(
        \OmniPro\ProductUpdate\Logger\Logger $logger,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Api\Data\ProductInterfaceFactory $productInterfaceFactory,
        \Magento\Catalog\Api\SpecialPriceInterface $specialPrice,
        \Magento\Catalog\Api\Data\SpecialPriceInterfaceFactory $specialPriceInterfaceFactory,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \OmniPro\ProductUpdate\Helper\readerCsv $readerCsv,
        \Magento\Catalog\Model\Product $product
    ) {
        $this->logger = $logger;
        $this->productRepository = $productRepository;
        $this->productInterfaceFactory = $productInterfaceFactory;
        $this->specialPrice = $specialPrice;
        $this->specialPriceInterfaceFactory = $specialPriceInterfaceFactory;
        $this->stockRegistry = $stockRegistry;
        $this->readerCsv = $readerCsv;
        $this->product = $product;
    }

    public function process()
    {
        $data = $this->readerCsv->importData();
        $return = $this->saveProduct($data);

        return $return;

        // $this->logger->debug(print_r($return));
    }

    public function saveProduct($data)
    {
        $contador = array(
            'errors' => 0,
            'createdProduct' => 0,
            'updatedProduct' => 0
        );

        foreach ($data as $key => $value) {
            if (!empty($value)) {
                // $this->logger->debug(print_r($value));

                if ($this->productExsist($value[0])) {
                    $this->logger->debug('Producto Actualizado');
                    $contador['updatedProduct'] += 1;
                } else {
                    $this->logger->debug('Producto Creado');
                    $contador['createdProduct'] += 1;
                }

                try {
                    /**
                     * @var \Magento\Catalog\Model\Product $dataProduct
                     */
                    $dataProduct = $this->productInterfaceFactory->create();
                    $dataProduct->setName($value[1]);
                    $dataProduct->setSku($value[0]);
                    $dataProduct->setAttributeSetId(4);
                    $dataProduct->setTypeId('simple');
                    $dataProduct->setVisibility(4);
                    $dataProduct->setPrice($value[2]);
                    $dataProduct->setUrlKey($value[7]);
                    $this->productRepository->save($dataProduct);
                } catch (\Exception $e) {
                    $this->logger->debug($e->getMessage());
                }

                try {
                    $stockData = $this->stockRegistry->getStockItemBySku($value[0]);
                    $stockData->setIsInStock(true);
                    $stockData->setQty($value[6]);
                    $this->stockRegistry->updateStockItemBySku($value[0], $stockData);
                } catch (\Exception $e) {
                    $this->logger->debug($e->getMessage());
                }

                if (!empty($value[3])) {
                    $this->logger->debug('special Price');
                    try {
                        $updateDatetime = new \DateTime();
                        $prices[] = $this->specialPriceInterfaceFactory->create()
                            ->setSku($dataProduct->getSku())
                            ->setStoreId(0)
                            ->setPrice($value[3])
                            ->setPriceFrom($updateDatetime->modify($value[4])->format('Y-m-d H:i:s'))
                            ->setPriceTo($updateDatetime->modify($value[5])->format('Y-m-d H:i:s'));
                        $this->specialPrice->update($prices);
                    } catch (\Exception $e) {
                        $this->logger->debug($e->getMessage());
                    }
                }
            } else {
                $contador['errors'] += 1;
            }
        }
        return $contador;
    }

    public function productExsist($sku)
    {
        try {
            $this->productRepository->get($sku);
            return true;
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return false;
        }
    }
}
