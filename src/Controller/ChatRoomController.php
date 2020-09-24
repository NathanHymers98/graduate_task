<?php


namespace App\Controller;

use App\Entity\Message;
use App\Form\ChatRoomFormType;
use App\Repository\UserRepository;
use App\Service\FireBaseService;
use Doctrine\ORM\EntityManagerInterface;
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
    public function chatRoom($recipient, Request $request, NormalizerInterface $normalizer, FireBaseService $fireBaseService, UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        // Setting variables that will be used for the senderId and recipientId respectively.
        $currentUser = $this->getUser();
        $currUserID = $currentUser->getId();
        $recipientId = $userRepository->findOneBy(['id' => $recipient]);
        $recipientId = $recipientId->getId();
        $msg = new Message();

        $form = $this->createForm(ChatRoomFormType::class, $msg);

        // Getting the chatroom name via firebase service
        $chatRoom = $fireBaseService->getChatRoomId($currUserID, $recipientId);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $message = $form->get('message')->getData();

            // Storing the messages, passing the information it needs in order to be able to store the data in FB
            $fireBaseService->storeMessage($message, $chatRoom, $currUserID, $recipientId);

            return $this->redirectToRoute('app_chat_room', ['recipient' => $recipientId]);
        }

        // Getting all the current messages stored in FB
        $userMessages = $fireBaseService->displayMessages($chatRoom);


        // looping over the messages from FB database and removing any read messages they have from users that have sent messages to the current user that is logged in
        // This loop is also used to update FB's seen field if the current logged in user has opened their chatrooms by sending it the currently logged in users ID.
        foreach($userMessages as $item) {
            if($item instanceof $msg) {
                if($currentUser === $item->getRecipientId()) {
                    $currentUser->removeReadMessages($item->getSenderId()->getId());

                    $entityManager->persist($item->getSenderId());
                    $entityManager->flush();

                    $fireBaseService->updateSeen($chatRoom, $currUserID);
                }
            }
        }



        return $this->render('chat_room/chat_room.html.twig', [
            'chatroomForm' => $form->createView(),
            'messages' => $userMessages,
        ]);
    }
}