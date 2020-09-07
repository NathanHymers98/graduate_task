<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\Loader;

class UserFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
         $user = new User();
         $user->setUsername('testuser');
         $user->setEmail('email@test.com');
         $user->setPassword('testpass');

         $manager->persist($user);

        $manager->flush();
    }
}
