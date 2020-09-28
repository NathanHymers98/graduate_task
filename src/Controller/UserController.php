<?php
declare(strict_types=1);

namespace App\Controller;


use App\Repository\UserRepository;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class UserController extends AbstractController
{
    /**
     * @Route("/user-list", name="app_userlist")
     * @IsGranted("ROLE_USER")
     */
    public function listUsernames(UserRepository $userRepository, Request $request, UserService $userService)
    {
        $users = $userService->getAllUsers($request);
        return $this->render('user/userlist.html.twig', [
            'users' => $users
        ]);
    }
}