<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use App\Form\ChatRoomFormType;
use App\Service\FireBaseService;
use App\Service\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class ChatRoomController extends AbstractController
{
    /**
     * @Route("/chat_room/{recipient}", name="app_chat_room")
     * @Entity("user", expr="repository.findOneBy(recipient)")
     *
     * @param User $recipient
     * @param Request $request
     * @param FireBaseService $fireBaseService
     * @param UserService $userService
     * @return RedirectResponse|Response
     *
     * @throws ExceptionInterface
     * @throws \Exception
     */
    public function chatRoom(User $recipient, Request $request, FireBaseService $fireBaseService, UserService $userService)
    {
        $currentUser = $this->getUser();
        $recipientId = $recipient->getId();
        $msg = new Message();
        $form = $this->createForm(ChatRoomFormType::class, $msg);
        $chatRoom = $fireBaseService->getChatRoomId($currentUser, $recipient);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $message = $form->get('message')->getData();
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
