<?php

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
        if (!isset($data['Stock'])) {
            $data['Stock'] = '0';
        }
        if (!isset($data['Cost in GBP'])) {
            $data['Cost in GBP'] = '0';
        }
        if (!isset($data['Discontinued'])) {
            $data['Discontinued'] = '';
        }

        return $data;
    }

    public function supportsDenormalization($data, string $type, string $format = null)
    {
        return Product::class === $type;
    }
}
