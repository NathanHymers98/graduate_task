<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Kreait\Firebase\Firestore;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class FireBaseService.
 */
class FireBaseService
{
    /**
     * @var Firestore
     */
    private $firestore;

    /**
     * @var NormalizerInterface
     */
    private $normalizer;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * FireBaseService constructor.
     * @param Firestore $firestore
     * @param NormalizerInterface $normalizer
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(Firestore $firestore, NormalizerInterface $normalizer, EntityManagerInterface $entityManager)
    {
        $this->firestore = $firestore;
        $this->normalizer = $normalizer;
        $this->entityManager = $entityManager;
    }

    /**
     * @param UserInterface $sender
     * @param User $recipient
     * @return string
     */
    public function getChatRoomId(UserInterface $sender, User $recipient)
    {
        return $sender->getId() < $recipient->getId() ? 'chat_room'.$sender->getId().'_'.$recipient->getId() : 'chat_room'.$recipient->getId().'_'.$sender->getId();
    }

    /**
     * @param $message
     * @param $chatRoom
     *
     * @param UserInterface $sender
     * @param User $recipient
     * @throws ExceptionInterface
     */
    public function storeMessage($message, $chatRoom, UserInterface $sender, User $recipient)
    {
        $msg = new Message();
        $msg->setSenderId($sender->getId());
        $msg->setRecipientId($recipient->getId());
        $msg->setContent($message);
        $msg->setChatRoomId($chatRoom);
        $normalMsg = $this->normalizer->normalize($msg);

        // Ensuring that the collection is created before the messages so that I can access the messages data
        $this->firestore->database()->collection('chatroom')->document($chatRoom)->set([]);

        $this->firestore->database()
            ->collection('chatroom')
            ->document($chatRoom)
            ->collection('messages')
            ->newDocument()
            ->set($normalMsg);
    }

    /**
     * @param $chatRoom
     * @param UserInterface $sender
     */
    public function updateSeen($chatRoom, UserInterface $sender)
    {
        // Querying FB for any messages documents that meet the two query requirements.
        // I giving this method an argument of $senderId because if the recipient of a message is the sender ID that I give it
        // then it will update the messages seen field to indicate that the message has been seen.
        $messagesRef = $this->firestore->database()->collection('chatroom')->document($chatRoom)->collection('messages');
        $q = $messagesRef
            ->where('seen', '=', 'Delivered')
            ->where('recipientId', '=', $sender->getId());
        $documents = $q->documents();

        foreach ($documents as $document) {
            if ($document->exists()) {
                $document->reference()->update([['path' => 'seen', 'value' => 'Read']]);
            }
        }
    }

    /**
     * @param $chatRoom
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getMessages($chatRoom)
    {
        // Getting all the messages that are stored in FB and ordering them by the date that they were sent at.
        $messagesRef = $this->firestore->database()->collection('chatroom')->document($chatRoom)->collection('messages');
        $documents = $messagesRef->orderBy('sentAt', 'asc')->documents();
        // Looping to get the individual message documents add denormalizing them from an array to a Message object
        foreach ($documents as $document) {
            if ($document->exists()) {
                $message = $document->data();
                $messages[] = $this->normalizer->denormalize($message, Message::class);
            } else {
                printf('Document %s does not exist!'.PHP_EOL);
            }
        }

        // If no messages were found, e.g. a two users have not created a chatroom together yet, it will display this default message.
        if (!$messagesRef->documents()->isEmpty()) {
            return $messages;
        } else {
            $messages[] = [
                'username' => 'System',
                'content' => 'You have not started a chatroom with this user! Send this user a message',
                'senderId' => new User(),
                'sentAt' => new \DateTime(),
                'seen' => 'N/A',
            ];
        }

        return $messages;
    }

    /**
     * @return array
     *
     * @throws \Exception
     */
    public function getUnreadMessages()
    {
        $messagesRef = $this->firestore->database()->collection('chatroom')->documents();

        date_default_timezone_set('Europe/London');
        $timeIn15Minutes = new \DateTime();
        $timeIn15Minutes = $timeIn15Minutes->sub(new \DateInterval('PT15M'));
        $timeIn15Minutes = strtotime($timeIn15Minutes->format('D H:i:s'));

        // Looping over all the messages in FB and querying for messages that have been delivered and checking to see if the sentAt field is greater than
        // the time object created above. Any messages the query finds is then looped over to get the specific data of those messages and adds them to an array
        // where they are then denormalized into Message objects
        $unreadMessagesArr = [];
        foreach ($messagesRef as $messageRef) {
            if ($messageRef->exists()) {
                $messages = $messageRef
                   ->reference()
                   ->collection('messages')
                   ->where('emailSent', '=', false)
                   ->where('seen', '=', 'Delivered')
                   ->where('sentAt', '<', $timeIn15Minutes)
                   ->documents()
                   ->rows();

                foreach ($messages as $message) {
                    $unreadMessagesArr[] = $message->data();
                    $message->reference()->update([['path' => 'emailSent', 'value' => true]]);
                }
            }
        }

        return $unreadMessagesArr;
    }

    /**
     * @param $chatRoom
     *
     * @param UserInterface $sender
     * @throws \Exception
     */
    public function updateUnreadMessages($chatRoom, UserInterface $sender)
    {
        $userMessages = $this->getMessages($chatRoom);
        $msg = new Message();

        foreach ($userMessages as $item) {
            if ($item instanceof $msg) {
                if ($sender === $item->getRecipientId()) {
                    $sender->removeReadMessages($item->getSenderId()->getId());

                    $this->entityManager->persist($item->getSenderId());
                    $this->entityManager->flush();

                    $this->updateSeen($chatRoom, $sender);
                }
            }
        }
    }
}
