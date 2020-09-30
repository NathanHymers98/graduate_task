<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Product;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ObjectValidator // This class validates the objects that are taken from the file upload.
{
    const  COST_TO_LOW = 'Cost is less than 5.';
    const  COST_TO_HIGH = 'Cost is over £1000';
    const  STOCK_TO_LOW = 'Stock is less than 10.';

    public function standardCheck(ValidatorInterface $validator, Product $product)
    {
        $errors = $validator->validate($product);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            $product->setIsSuccessful(false);
            $product->setReasonsForFailure(implode(', ', $errorMessages));

            return implode(', ', $errorMessages);
        }

        return '';
    }

    public function validateDiscontinued(Product $product) // If an item is discontinued, attach the current date there instead
    {
        $date = new \DateTime();
        if (null == $product->getIsDiscontinued()) {
            $product->setIsDiscontinued('No');
        } elseif ('yes' == $product->getIsDiscontinued()) {
            $product->setIsDiscontinued('Yes, discontinued on: '.$date->format('Y-m-d'));
        }
    }
}
