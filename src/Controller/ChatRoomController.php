<?php


namespace App\Controller;

use App\Entity\Participant;
use App\Entity\User;
use App\Form\ChatRoomFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Kreait\Firebase\Firestore;


class ChatRoomController extends AbstractController
{
    /**
     * @Route("/chat_room/{user1}/{user2}", name="app_chat_room")
     */
    public function chatRoom($user1, $user2, Request $request, FireBaseController $fireBaseController, FireStore $firestore)
    {

        $users = $fireBaseController->getUsers();

        $form = $this->createForm(ChatRoomFormType::class);

        $chatRoom = 'chat_room'.$user1 . '_' . $user2;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $message = $form->get('message')->getData();

            $firestore->database()->collection('chatroom')->document($chatRoom)->collection('messages')->newDocument()->set(['content' => $message, 'sender_id' => $user1, 'recipient_id' => $user2]);

            return $this->redirectToRoute('app_chat_room', ['user1' => $user1, 'user2' => $user2]);
        }

        return $this->render('chat_room/chat_room.html.twig', [
            'chatroomForm' => $form->createView(),
            'users' => $users,
        ]);
    }

//    public function displayMessages()
//    {
//        $message =
//    }
}