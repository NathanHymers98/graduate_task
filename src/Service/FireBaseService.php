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

        // Ensuring that the collection is created before the messages so that I can access the messages data
        $this->firestore->database()->collection('chatroom')->document($chatRoom)->set([]);

        $this->firestore->database()
            ->collection('chatroom')
            ->document($chatRoom)
            ->collection('messages')
            ->newDocument()
            ->set($normalMsg);
    }


    public function updateSeen($chatRoom, $senderId)
    {
        // Querying FB for any messages documents that meet the two query requirements.
        // I giving this method an argument of $senderId because if the recipient of a message is the sender ID that I give it
        // then it will update the messages seen field to indicate that the message has been seen.
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
        // Getting all the messages that are stored in FB and ordering them by the date that they were sent at.
        $messagesRef = $this->firestore->database()->collection('chatroom')->document($chatRoom)->collection('messages');
        $documents = $messagesRef->orderBy('sentAt', 'asc')->documents();
        // Looping to get the individual message documents add denormalizing them from an array to a Message object
        foreach ($documents as $document) {
            if ($document->exists()) {
                $message = $document->data();
                $messages[] = $this->normalizer->denormalize($message, Message::class);
            } else {
                printf('Document %s does not exist!' . PHP_EOL);
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
                'sentAt' => new \DateTime,
                'seen' => 'N/A',
            ];
        }
        return $messages;
    }

    public function getUnreadMessages()
    {
       $messagesRef = $this->firestore->database()->collection('chatroom')->documents();

       date_default_timezone_set('Europe/London');

       // Creating two datetime objects, one with the current time and another which behind by 15 minutes
       $currentTime = new \DateTime();
       $timeIn15Minutes = new \DateTime();
       $timeIn15Minutes = $timeIn15Minutes->sub(new \DateInterval('PT5M'));
       $newTime = $timeIn15Minutes->format('D H:i');

       $timeDifference = $timeIn15Minutes->diff($currentTime);

        // Looping over all the messages in FB and querying for messages that have been delivered and checking to see if the sentAt field is greater than
        // the time object created above. Any messages the query finds is then looped over to get the specific data of those messages and adds them to an array
        // where they are then denormalized into Message objects
       $unreadMessagesArr = [];
       foreach ($messagesRef as $messageRef) {
           if ($messageRef->exists()) {
               $messages = $messageRef
                   ->reference()
                   ->collection('messages')
                   ->where('emailSent', '=', 'false')
                   ->where('seen', '=', 'Delivered')
                   ->where('sentAt', '<', $newTime)
                   ->documents()
                   ->rows();

               foreach ($messages as $message) {
                   $unreadMessages = $message->data();
                   $unreadMessagesArr[] = $this->normalizer->denormalize($unreadMessages, Message::class);

                   $message->reference()->update([['path' => 'emailSent', 'value' => 'True']]);
               }
           }

       }
       return $unreadMessagesArr;
    }
}