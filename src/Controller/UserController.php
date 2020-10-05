<?php

declare(strict_types=1);

namespace App\Controller;

use App\ElasticSearch\ElasticSearchUsers;
use App\Form\UserSearchType;
use App\Service\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/user-list", name="app_userlist")
     * @IsGranted("ROLE_USER")
     */
    public function listUsernames(Request $request, UserService $userService, ElasticSearchUsers $elasticSearchUsers)
    {
        $form = $this->createForm(UserSearchType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $search = $form->get('search')->getData();
            $users = $elasticSearchUsers->getUsersFromElastic($search);

            return $this->render('user/userlist.html.twig', [
                'UserSearchForm' => $form->createView(),
                'users' => $users,
            ]);
        }

        $users = $userService->getAllUsers($request);

        return $this->render('user/userlist.html.twig', [
            'UserSearchForm' => $form->createView(),
            'users' => $users,
        ]);
    }

    /**
     * @Route("/delete_users_elastic", name="app_delete_from_elastic")
     */
    public function deleteFromElastic(ElasticSearchUsers $elasticSearchUsers)
    {
        $elasticSearchUsers->deleteUsers();

        return $this->render('user/userlist.html.twig');
    }
}
