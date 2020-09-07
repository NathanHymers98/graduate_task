<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class MessageController extends AbstractController
{

    /**
     * @Route("/message", name="app_chat_room")
     */
    public function chatRoom()
    {
        return $this->render('chat_room/chat_room.html.twig');
    }
}