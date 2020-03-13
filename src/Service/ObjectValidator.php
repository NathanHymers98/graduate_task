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

    public function validateDiscontinued(Product $item) // If an item is discontinued, attach the current date there instead
    {
        $date = new \DateTime();
        if ($item->getIsDiscontinued() == null) {
            $item->setIsDiscontinued('no');
            $this->successfulImport[] = $item;
        } elseif ($item->getIsDiscontinued() == 'yes'){
            $item->setIsDiscontinued('Yes, discontinued on: '.$date->format('Y-m-d'));
            $this->successfulImport[] = $item;
        }
        return $this->successfulImport;
    }

    public function emptyEntry(Product $item)
    {
        if(empty($item->getProductStock() || empty($item->getNetCost()))) {
            $item->setProductStock(0) || $item->setNetCost(0);
            $this->failedImport[] = $item;
        }

//        if ('' == $this->product->getNetCost() || '' == $this->product->getProductStock()) {
//            $this->failedImport[] = $item;
//        }
//        return $this->failedImport;
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