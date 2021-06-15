<?php

namespace OmniPro\ProductUpdate\Helper;

class readerCsv
{
    const FILENAME = '/file01.csv';
    const DIR = '/productUpdate';
    const DELIMITER = '|';

    /**
     * @param \Magento\Framework\File\Csv
     */
    private $csv;

    /**
     * @param \OmniPro\ProductUpdate\Logger\Logger
     */
    private $logger;

    /**
     * @param \Magento\Framework\Filesystem\DirectoryList
     */
    private $directoryList;

    public function __construct(
        \Magento\Framework\File\Csv $csv,
        \OmniPro\ProductUpdate\Logger\Logger $logger,
        \Magento\Framework\Filesystem\DirectoryList $directoryList
    ) {
        $this->csv = $csv;
        $this->logger = $logger;
        $this->directoryList = $directoryList;
    }

    public function importData()
    {

        $path = $this->directoryList->getPath('media') . self::DIR;
        $file = $path . self::FILENAME;

        $this->csv->setDelimiter(self::DELIMITER);
        $data = $this->csv->getData($file);

        //$this->logger->debug(var_dump($data));

        if ((strlen($data[0][0]) - 3) == 0) {
            $this->logger->debug('El archivo no contiene registros');
        } else {
            $product = array();
            foreach ($data as $keyRow => $row) {
                if (count($row) != 8) {
                    $this->logger->debug('Registro ' . $keyRow . ' no creado por falta de campos');
                } else {
                    foreach ($row as $key => $value) {
                        if ($this->validateEmpty($value) && $this->validateRequiredFields($key)) {
                            $this->logger->debug('Registro ' . $keyRow . ' no creado por falta del campo ' . $key);
                            $product[$keyRow] =  array();
                            break;
                        } else {
                            if ($this->validateNumber($key, $value) || $this->validateString($key, $value)) {
                                // $this->logger->debug('Campo ' . $key . ' valido');
                                $product[$keyRow][$key] = $value;

                                if($key == 3 && $this->validateSpecialPrice($value, $row[2], $row[3], $row[4])) {
                                    $product[$keyRow][$key] = '';
                                }
                                
                                if ($key == 7) {
                                    $return = $this->validateUrlKey($value, $row[1], $row[0]);
                                    $product[$keyRow][$key] = $return;
                                }

                            } else {
                                $this->logger->debug('Registro ' . $keyRow . ' no creado por campo ' . $key . ' invalido');
                                $product[$keyRow] =  array();
                                break;
                            }
                        }
                    }
                    
                    // $this->logger->debug(print_r($product));
                }
                //$this->logger->debug(print_r($row));
            }
            return $product;
        }
    }

    public function validateEmpty($value)
    {
        return empty($value);
    }

    public function validateNumber($key, $value)
    {
        if (($key == 2 || $key == 3) && is_numeric($value)) {
            return true;
        } else {
            if ($key == 3 && $this->validateEmpty($value))
                return true;
        }
    }

    public function validateString($key, $value)
    {
        if (($key == 0 || $key == 1 || $key == 4 || $key == 5 || $key == 6 || $key == 7) && is_string($value)){
            return true;
        }
    }

    public function validateRequiredFields($key)
    {
        if ($key == 0 || $key == 1 || $key == 2 || $key == 6) {
            return true;
        } else {
            return false;
        }
    }

    public function validateUrlKey($value, $nombre, $sku) 
    {
        if (empty($value)) {
            return (strtolower(str_replace(' ', '',$nombre))) . (strtolower(str_replace('-', '',$sku)));
        } else {
            return $value;
        }
    }

    public function validateSpecialPrice($value, $price, $specialPrice, $dateFrom) 
    {
        if ((empty($value) || empty($dateFrom)) || ($price < $specialPrice)) {
            return true;
        }
    }

    public function returnData() {
        
    }

    /***
     * Columnas como constantes
     * || como switch
     */
}