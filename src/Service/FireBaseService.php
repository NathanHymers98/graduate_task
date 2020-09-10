<?php


namespace App\Service;

use Kreait\Firebase\Firestore;
use App\Entity\Message;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class FireBaseService
{
    private $firestore;

    private $normalizer;

    private $messages;


    public function __construct(Firestore $firestore, NormalizerInterface $normalizer)
    {
        $this->firestore = $firestore;
        $this->normalizer = $normalizer;
    }

    public function getChatRoomId($senderId, $recipientId)
    {
        if($senderId < $recipientId) {
            $chatRoom = 'chat_room'. $senderId . '_' . $recipientId;
        } else {
            $chatRoom = 'chat_room'. $recipientId . '_' . $senderId;
        }
        return $chatRoom;
    }

    public function storeMessage($message, $chatRoom, $senderId, $recipientId)
    {
        $msg = new Message();
        $msg->setSenderId($senderId);
        $msg->setRecipientId($recipientId);
        $msg->setContent($message);
        $msg->setChatRoomId($chatRoom);
        $msg->setSeen('false');
        $normalMsg = $this->normalizer->normalize($msg);


        $this->firestore->database()->collection('chatroom')->document($chatRoom)->collection('messages')->newDocument()->set($normalMsg);
    }

    public function displayMessages($chatroom)
    {
        $messagesRef = $this->firestore->database()->collection('chatroom')->document($chatroom)->collection('messages');
        $documents = $messagesRef->orderBy('sentAt', 'asc')->documents();
        foreach ($documents as $document) {
            if ($document->exists()) {
                $message = $document->data();
                 $messages[] = $this->normalizer->denormalize($message, Message::class);
            } else {
                printf('Document %s does not exist!' . PHP_EOL);
            }
        }
        if (empty($messages)) {
            return 'Message this user';
        }
        return $messages;
    }
}