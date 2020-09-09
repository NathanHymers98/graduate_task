<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MessageRepository::class)
 */
class Message
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="string")
     */
    private $chatRoomId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $content;

    /**
     * @ORM\Column(type="integer")
     */
    private $senderId;

    /**
     * @ORM\Column(type="integer")
     */
    private $recipientId;

    /**
     * @ORM\Column(type="datetime")
     */
    private $sentAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $seen;

    public function __construct()
    {
        date_default_timezone_set('Europe/London');
        $this->sentAt = new \DateTime;
        $this->seen = false;
    }

    /**
     * @return mixed
     */
    public function getChatRoomId()
    {
        return $this->chatRoomId;
    }

    /**
     * @param mixed $chatRoomId
     */
    public function setChatRoomId($chatRoomId): void
    {
        $this->chatRoomId = $chatRoomId;
    }


    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getSenderId(): ?int
    {
        return $this->senderId;
    }

    public function setSenderId(int $senderId): self
    {
        $this->senderId = $senderId;

        return $this;
    }

    public function getRecipientId(): ?int
    {
        return $this->recipientId;
    }

    public function setRecipientId(int $recipientId): self
    {
        $this->recipientId = $recipientId;

        return $this;
    }

    public function getSentAt(): ?\DateTimeInterface
    {
        return $this->sentAt;
    }

    public function setSentAt(\DateTimeInterface $sentAt): self
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    public function getSeen(): ?string
    {
        return $this->seen;
    }

    public function setSeen(string $seen): self
    {
        $this->seen = $seen;

        return $this;
    }
}
