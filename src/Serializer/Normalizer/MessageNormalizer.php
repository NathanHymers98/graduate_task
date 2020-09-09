<?php

namespace App\Serializer\Normalizer;

use App\Entity\Message;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class MessageNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    private $normalizer;

    public function __construct(ObjectNormalizer $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    public function normalize($object, $format = null, array $context = array()): array
    {
        $data = $this->normalizer->normalize($object, $format, $context);

        //$data['senderId'] = ['senderId']['id'];

        return $data;
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof Message;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
