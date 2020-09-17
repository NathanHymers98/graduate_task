<?php


namespace App\Controller;

use App\Entity\Message;
use App\Form\ChatRoomFormType;
use App\Repository\UserRepository;
use App\Service\FireBaseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Kreait\Firebase\Firestore;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;


class ChatRoomController extends AbstractController
{


    /**
     * @Route("/chat_room/{recipient}", name="app_chat_room")
     */
    public function chatRoom($recipient, Request $request, NormalizerInterface $normalizer, FireBaseService $fireBaseService, UserRepository $userRepository)
    {
        $currentUser = $this->getUser();
        $currUserID = $currentUser->getId();
        $recipientId = $userRepository->findOneBy(['id' => $recipient]);
        $recipientId = $recipientId->getId();
        $msg = new Message();


        $form = $this->createForm(ChatRoomFormType::class, $msg);

        $chatRoom = $fireBaseService->getChatRoomId($currUserID, $recipientId);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $message = $form->get('message')->getData();

            $fireBaseService->storeMessage($message, $chatRoom, $currUserID, $recipientId);

            return $this->redirectToRoute('app_chat_room', ['recipient' => $recipientId]);
        }

        $userMessages = $fireBaseService->displayMessages($chatRoom);

        foreach($userMessages as $item) {
            if($item instanceof $msg) {
                if($currentUser === $item->getRecipientId()) {
                    $fireBaseService->updateSeen($chatRoom, $currUserID);
                }
            }
        }

//        $fireBaseService->unreadMessageCount($chatRoom, $userMessages);


        return $this->render('chat_room/chat_room.html.twig', [
            'chatroomForm' => $form->createView(),
            'messages' => $userMessages,
        ]);
    }
}