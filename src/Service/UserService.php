<?php

namespace App\Service;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;

class UserService
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAllUsers(Request $request)
    {
        $q = $request->query->get('q');
        $users = $this->userRepository->findAllWithSearch($q);

        return $users;
    }
}
