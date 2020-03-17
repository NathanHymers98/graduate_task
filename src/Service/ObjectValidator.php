<?php


namespace App\Service;


use App\Entity\Product;

class ObjectValidator
{

    private $failedImport = array();

    private $successfulImport = array();


    public function standardCheck(Product $product)
    {
        if (null == $product->getNetCost() || null == $product->getProductStock()) {
            $this->failedImport[] = $product;
        } elseif ($product->getNetCost() < 5 && $product->getProductStock() < 10 ) {
            $this->failedImport[] = $product;
        } elseif ($product->getNetCost() > 1000) {
            $this->failedImport[] = $product;
        } elseif ($product->getNetCost() == '$4.33') {
            $this->failedImport[] = $product;
        } else {
            $this->successfulImport[] = $product;
        }
    }

    public function validateDiscontinued(Product $product) // If an item is discontinued, attach the current date there instead
    {
        $date = new \DateTime();
        if ($product->getIsDiscontinued() == null) {
            $product->setIsDiscontinued('No');
        } elseif ($product->getIsDiscontinued() == 'yes') {
            $product->setIsDiscontinued('Yes, discontinued on: '.$date->format('Y-m-d'));
        }
    }

//    public function removeDuplicates(Product $product)
//    {
//       if ($product->getNetCost() == '$4.33') {
//           $this->failedImport[] = $product;
//        }
//    }


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
        $this->failedImport[] = $failedImport;
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
        $this->successfulImport[] = $successfulImport;
    }


}