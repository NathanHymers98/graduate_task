<?php
namespace App\Controller;

use Kreait\Firebase\Firestore;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
//    /**
//     * @Route("/test", name="test")
//     */
//    public function Test()
//    {
//        $projectId = 'graduatetask';
//        $db = new FirestoreClient([
//            'projectId' => $projectId,
//        ]);
//        $data = [
//            'name' => 'adam',
//            'state' => 'scunny',
//            'country' => 'Donny'
//        ];
//        $db->collection('sample')->document('test')->set($data);
//
//        return $this->json('Data added to firebase');
//    }

    /**
     * @Route("/test", name="test")
     */
    public function Test(Firestore $firestore) // Adds hardcoded data to the firestore database
    {
        $data = [
            'name' => 'josh',
            'state' => 'scunny',
            'country' => 'Donny'
        ];
        $firestore->database()->collection('sample')->document('test')->set($data);

        return $this->json('Data added to firebase');
    }
}




