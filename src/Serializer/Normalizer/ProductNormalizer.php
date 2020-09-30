<?php

declare(strict_types=1);

namespace App\Serializer\Normalizer;

use App\Entity\Product;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ProductNormalizer implements DenormalizerInterface, CacheableSupportsMethodInterface
{
    private $normalizer;

    public function __construct(ObjectNormalizer $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }

    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        if (!isset($data['Stock']) || !is_numeric($data['Stock'])) { // Setting default values for products that are poorly formatted in the CSV
            $data['Stock'] = '0';
        }
        if (!isset($data['Cost in GBP']) || !is_numeric($data['Cost in GBP'])) {
            $data['Cost in GBP'] = '0';
        }
        if (!isset($data['Discontinued'])) {
            $data['Discontinued'] = '';
        }

        $product = new Product(); // Creating new Product objects with the data that was passed from the serializer
        $product->setProductCode($data['Product Code']);
        $product->setProductName($data['Product Name']);
        $product->setProductDescription($data['Product Description']);
        $product->setProductStock($data['Stock']);
        $product->setNetCost($data['Cost in GBP']);
        $product->setIsDiscontinued($data['Discontinued']);

        return $product;
    }

    public function supportsDenormalization($data, string $type, string $format = null)
    {
        return Product::class === $type;
    }
}
