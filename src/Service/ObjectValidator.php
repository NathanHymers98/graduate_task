<?php


namespace App\Service;


use App\Entity\Product;

class ObjectValidator
{
    //private $product;

    private $failedImport = array();

    private $successfulImport = array();

//    public function __construct(Product $product)
//    {
//        $this->product = $product;
//
//    }

    public function validateDiscontinued(Product $product) // If an item is discontinued, attach the current date there instead
    {
        $date = new \DateTime();
        if ($product->getIsDiscontinued() == null) {
            $product->setIsDiscontinued('no');
            $this->successfulImport[] = $product;
        } elseif ($product->getIsDiscontinued() == 'yes') {
            $product->setIsDiscontinued('Yes, discontinued on: '.$date->format('Y-m-d'));
            $this->successfulImport[] = $product;
        }
    }

//    public function emptyEntry(Product $product)
//    {
//        if(empty($product->getProductStock() || empty($product->getNetCost()))) {
//            $product->setProductStock(0) || $product->setNetCost(0);
//            $this->failedImport[] = $product;
//        }
//
//    }

    public function checkLowCostAndStock(Product $product)
    {
        if ($product->getNetCost() < 5 && $product->getProductStock() < 10 ) {
            $this->failedImport[] = $product;
        }
        $this->successfulImport[] = $product;
    }

    public function checkHighCost(Product $product)
    {
        if ($product->getNetCost() > 1000 ) {
            $this->failedImport[] = $product;
        }
        $this->successfulImport[] = $product;
    }

    /**
     * @return array
     */
    public function getFailedImport(): array
    {
        return $this->failedImport;
    }

    /**
     * @param array $failedImport
     */
    public function setFailedImport(array $failedImport): void
    {
        $this->failedImport = $failedImport;
    }

    /**
     * @return array
     */
    public function getSuccessfulImport(): array
    {
        return $this->successfulImport;
    }

    /**
     * @param array $successfulImport
     */
    public function setSuccessfulImport(array $successfulImport): void
    {
        $this->successfulImport = $successfulImport;
    }


}