<?php

namespace App\Serializer\Normalizer;

use App\Entity\Message;
use App\Entity\User;
use Symfony\Component\Serializer\Exception\BadMethodCallException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\ExtraAttributesException;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Exception\RuntimeException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Doctrine\ORM\EntityManagerInterface;

class MessageNormalizer implements DenormalizerInterface, NormalizerInterface, CacheableSupportsMethodInterface
{
    private $normalizer;
    private $entityManager;

    public function __construct(ObjectNormalizer $normalizer, EntityManagerInterface $entityManager)
    {
        $this->normalizer = $normalizer;
        $this->entityManager = $entityManager;
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

    /**
     * @inheritDoc
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        $sender = $this->entityManager->getRepository(User::class)->find(['id' => $data['senderId']]);
        $recipient = $this->entityManager->getRepository(User::class)->find(['id' => $data['recipientId']]);

//        dd($recipient, $sender);

        $message = $this->normalizer->denormalize($data, $type, $format, $context);

        $message->setSenderId($sender);
        $message->setRecipientId($recipient);

        return $message;
    }

    /**
     * @inheritDoc
     */
    public function supportsDenormalization($data, string $type, string $format = null)
    {
        return Message::class === $type;
    }
}
