<?php


namespace App\MessageHandler;


use App\Message\QueueUploadedFile;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class QueueUploadedFileHandler implements MessageHandlerInterface
{
    public function __invoke(QueueUploadedFile $queueUploadedFile)
    {
        dump($queueUploadedFile);
    }
}