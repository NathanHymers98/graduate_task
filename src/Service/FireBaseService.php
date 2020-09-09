<?php


namespace App\Service;

use Kreait\Firebase\Firestore;

class FireBaseService
{
    private $firestore;


    public function __construct(Firestore $firestore)
    {
        $this->firestore = $firestore;
    }

    public function storeMessage($message, $chatRoom)
    {
        $this->firestore->database()->collection('chatroom')->document($chatRoom)->collection('messages')->newDocument()->set($message);
    }
}