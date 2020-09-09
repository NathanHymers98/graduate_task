<?php


namespace App\Controller;

use App\Entity\Message;
use App\Form\ChatRoomFormType;
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
    public function chatRoom($recipient, Request $request, FireStore $firestore, NormalizerInterface $normalizer, FireBaseService $fireBaseService)
    {

        $currentUser = $this->getUser();
        $currUserID = $currentUser->getId();
        $msg = new Message();


        $form = $this->createForm(ChatRoomFormType::class, $msg);


        if($currUserID < $recipient) {
            $chatRoom = 'chat_room'. $currUserID . '_' . $recipient;
        } else {
            $chatRoom = 'chat_room'. $recipient . '_' . $currUserID;
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $message = $form->get('message')->getData();

            $normalMsg = $normalizer->normalize($msg);
            $fireBaseService->storeMessage($message, $chatRoom, $currUserID, $recipient);
//            $yes = $fireBaseService->displayMessages($chatRoom);
//            dd($yes);

            return $this->redirectToRoute('app_chat_room', ['recipient' => $recipient]);
        }

        return $this->render('chat_room/chat_room.html.twig', [
            'chatroomForm' => $form->createView(),
        ]);
    }
}