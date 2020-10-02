<?php

declare(strict_types=1);

namespace App\Serializer\Normalizer;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class MessageNormalizer implements DenormalizerInterface, NormalizerInterface, CacheableSupportsMethodInterface
{
    private $normalizer;
    private $entityManager;

    public function __construct(ObjectNormalizer $normalizer, EntityManagerInterface $entityManager)
    {
        $this->normalizer = $normalizer;
        $this->entityManager = $entityManager;
    }

    public function normalize($object, $format = null, array $context = []): array
    {
        $data = $this->normalizer->normalize($object, $format, $context);

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
     * {@inheritdoc}
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        // Allows me to set the senderId and recipientId of a Message object to the specific User object.
        $sender = $this->entityManager->getRepository(User::class)->find(['id' => $data['senderId']]);
        $recipient = $this->entityManager->getRepository(User::class)->find(['id' => $data['recipientId']]);

        $data['sentAt'] = (new \DateTime())->setTimestamp($data['sentAt'])->format(\DateTime::ATOM);

        $message = $this->normalizer->denormalize($data, $type, $format, $context);

        $message->setSenderId($sender);
        $message->setRecipientId($recipient);

        // if the recipient and sender are different objects and the messages seen property is set to delivered
        // Check to make sure that the sender is not in the recipients hasUnreadMessagesFrom array property
        // If the sender is not in there already, then add them to that array.
        if ($recipient !== $sender && 'Delivered' == $message->getSeen()) {
            if (!in_array($sender->getId(), $recipient->getHasUnreadMessagesFrom())) {
                $recipient->setHasUnreadMessagesFrom($sender->getId());
            }
        }
        $this->entityManager->persist($recipient);
        $this->entityManager->flush();

        return $message;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, string $type, string $format = null)
    {
        return Message::class === $type;
    }
}
