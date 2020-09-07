<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class ChatRoomController extends AbstractController
{

    /**
     * @Route("/chat_room", name="app_chat_room")
     */
    public function chatRoom(FireBaseController $fireBaseController)
    {
        $users = $fireBaseController->getUsers();

        return $this->render('chat_room/chat_room.html.twig', [
            'users' => $users,
        ]);
    }
}