<?php


namespace App\Service;

use App\Entity\User;
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
        $normalMsg = $this->normalizer->normalize($msg);

        $this->firestore->database()
            ->collection('chatroom')
            ->document($chatRoom)
            ->collection('messages')
            ->newDocument()
            ->set($normalMsg);
    }


    public function updateSeen($chatRoom, $senderId)
    {

        $messagesRef = $this->firestore->database()->collection('chatroom')->document($chatRoom)->collection('messages');
        $q = $messagesRef
            ->where('seen', '=', 'Delivered')
            ->where('recipientId', '=', $senderId);
        $documents = $q->documents();

        foreach ($documents as $document) {
            if ($document->exists()) {
                $document->reference()->update([['path' => 'seen', 'value' => 'Read']]);
           }
        }

    }

    public function displayMessages($chatRoom)
    {
        $messagesRef = $this->firestore->database()->collection('chatroom')->document($chatRoom)->collection('messages');
        $documents = $messagesRef->orderBy('sentAt', 'asc')->documents();
        foreach ($documents as $document) {
            if ($document->exists()) {
                $message = $document->data();
                $messages[] = $this->normalizer->denormalize($message, Message::class);
            } else {
                printf('Document %s does not exist!' . PHP_EOL);
            }
        }

        if (!$messagesRef->documents()->isEmpty()) {
            return $messages;
        } else {
            $messages[] = [
                'username' => 'System',
                'content' => 'Send this user a message',
                'senderId' => new User(),
                'sentAt' => new \DateTime,
                'seen' => 'true',
            ];
        }
        return $messages;
    }

    public function unreadMessageCount($chatRoom, $messages)
    {
        $colRef = $this->firestore->database()->collection('chatroom')->document($chatRoom)->collection('messages');
        for ($i = 0; $i < $messages; $i++) {
            $doc = $colRef->document($i);
            $doc->set(['Cnt' => 0]);
        }
    }
}