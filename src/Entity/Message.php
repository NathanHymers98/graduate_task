<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Boolean;

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

    /**
     * @ORM\Column(type="boolean", length=255)
     */
    private $emailSent;

    public function __construct()
    {
        date_default_timezone_set('Europe/London');
        $timeobj = new \DateTime();
        $time = $timeobj->format('D H:i');
        $this->setEmailSent(false);
        $this->sentAt = $time;
        $this->seen = 'Delivered';
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

    public function getSenderId()
    {
        return $this->senderId;
    }

    public function setSenderId($senderId): self
    {
        $this->senderId = $senderId;

        return $this;
    }

    public function getRecipientId()
    {
        return $this->recipientId;
    }

    public function setRecipientId($recipientId): self
    {
        $this->recipientId = $recipientId;

        return $this;
    }

    public function getSentAt()
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

    public function getEmailSent(): ?bool
    {
        return $this->emailSent;
    }

    public function setEmailSent(bool $emailSent): self
    {
        $this->emailSent = $emailSent;

        return $this;
    }
}
