<?php

declare(strict_types=1);

namespace App\Controller;

use App\ElasticSearch\ElasticSearchUsers;
use App\Entity\Message;
use App\Entity\User;
use App\Form\ChatRoomFormType;
use App\Service\FireBaseService;
use App\Service\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ChatRoomController extends AbstractController
{
    /**
     * @Route("/chat_room/{recipient}", name="app_chat_room")
     * @Entity("user", expr="repository.findOneBy(recipient)")
     */
    public function chatRoom(User $recipient, Request $request, FireBaseService $fireBaseService, UserService $userService, ElasticSearchUsers $elasticSearchUsers)
    {
        // Setting variables that will be used for the senderId and recipientId respectively.
        $currentUser = $this->getUser();
        $recipientId = $recipient->getId();
        $msg = new Message();

        $form = $this->createForm(ChatRoomFormType::class, $msg);

        // Getting the chatroom name via firebase service
        $chatRoom = $fireBaseService->getChatRoomId($currentUser, $recipient);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $message = $form->get('message')->getData();

            // Storing the messages, passing the information it needs in order to be able to store the data in FB
            $fireBaseService->storeMessage($message, $chatRoom, $currentUser, $recipient);

            return $this->redirectToRoute('app_chat_room', ['recipient' => $recipientId]);
        }

        $fireBaseService->updateUnreadMessages($chatRoom, $currentUser);
        $userMessages = $fireBaseService->getMessages($chatRoom);

        $users = $userService->getAllUsers($request);

        return $this->render('chat_room/chat_room.html.twig', [
            'chatroomForm' => $form->createView(),
            'messages' => $userMessages,
            'users' => $users,
        ]);
    }
}
