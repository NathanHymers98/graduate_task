<?php


namespace App\Service;


use App\Entity\Product;

class ObjectValidator
{
    public function standardCheck(Product $product)
    {
        if ($product->getNetCost() < 5 && $product->getProductStock() < 10 ) {
            $product->setIsSuccessful(false);
        } elseif ($product->getNetCost() > 1000) {
            $product->setIsSuccessful(false);
        }
        elseif ($product->getNetCost() == 0) {
            $product->setIsSuccessful(false);
        }else {
            $product->setIsSuccessful(true);
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

}