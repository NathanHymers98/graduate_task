<?php
namespace App\Controller;

use App\Entity\User;
use Kreait\Firebase\Firestore;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Kreait\Firebase\Auth;
use Doctrine\ORM\EntityManagerInterface;

class FireBaseController extends AbstractController
{

    private $firestore;

    public $docArray;

    public function __construct(Firestore $firestore)
    {
        $this->firestore = $firestore;
    }


    public function addUserToFireBase(User $user) // Adds data to the database
    {
        $data = [
            'uid' => $user->getId(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
        ];
        $this->firestore->database()->collection('Users')->document($user->getId())->set($data);


        return $this->json('Data added to firebase');
    }

    /**
     * @Route("/test")
     */
    public function getUsers()
    {
        $citiesRef = $this->firestore->database()->collection('Users');
        $documents = $citiesRef->documents();
        foreach ($documents as $document) {
            if ($document->exists()) {
                $docArray[] = $document->data();
            } else {
                return 'Document not found';
            }

        }
        return $docArray;

    }
}




