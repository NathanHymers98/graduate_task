<?php


namespace App\Controller;


use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractController
{
    /**
     * @Route("/user-list", name="app_userlist")
     */
    public function listUsernames(UserRepository $userRepository, Request $request)
    {
        $q = $request->query->get('q');
        $users = $userRepository->findAllWithSearch($q);
        return $this->render('user/userlist.html.twig', [
            'users' =>  $users,
        ]);
    }
}