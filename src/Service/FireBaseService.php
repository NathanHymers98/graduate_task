<?php


namespace App\Service;

use Kreait\Firebase\Firestore;
use App\Entity\Message;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class FireBaseService
{
    private $firestore;

    private $normalizer;


    public function __construct(Firestore $firestore, NormalizerInterface $normalizer)
    {
        $this->firestore = $firestore;
        $this->normalizer = $normalizer;
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
        $documents = $messagesRef->documents();
        //$snapshot = $usersRef->snapshot();
        foreach ($documents as $document) {
            if ($document->exists()) {
                printf('Document data for document %s:' . PHP_EOL, $document->id());
                print_r($document->data());
                printf(PHP_EOL);
                dump($document);
            } else {
                printf('Document %s does not exist!' . PHP_EOL);
            }
        }
    }
}