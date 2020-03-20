<?php


namespace App\Service;


use App\Entity\Product;
use Symfony\Component\Validator\Validation;

class ObjectValidator
{

    const  COST_TO_LOW = 'Cost is less than 5.';
    const  COST_TO_HIGH = 'Cost is over £1000';
    const  STOCK_TO_LOW = 'Stock is less than 10.';


    public function standardCheck(Product $product) // Checks for any invalidated products against the import rules
    {
        if ($product->getNetCost() < 5) {
            $product->setIsSuccessful(false);
            $product->setReasonsForFailure(self::COST_TO_LOW);
        }elseif ($product->getNetCost() > 1000) {
            $product->setIsSuccessful(false);
            $product->setReasonsForFailure(self::COST_TO_HIGH);
        }elseif ($product->getProductStock() < 10) {
            $product->setIsSuccessful(false);
            $product->setReasonsForFailure(self::STOCK_TO_LOW);
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