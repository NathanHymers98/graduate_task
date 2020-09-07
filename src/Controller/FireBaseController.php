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
    /**
     * @Route("/test", name="test")
     */
    public function addUserToFireBase(Firestore $firestore, User $user) // Adds data to the database
    {
        $data = [
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
        ];
        $firestore->database()->collection('Users')->newDocument()->set($data);

        return $this->json('Data added to firebase');
    }



}




