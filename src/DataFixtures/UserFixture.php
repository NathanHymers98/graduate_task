<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixture extends Fixture
{
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    // Used to automatically create 10 random users and one admin user
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 10; ++$i) {
            $user = new User();
            $user->setUsername('user'.$i);
            $password = $this->encoder->encodePassword($user, 'password');
            $user->setPassword($password);
            $user->setEmail('user'.$i.'@email.com');
            $manager->persist($user);
        }

        $adminUser = new User();
        $adminUser->setUsername('admin');
        $password = $this->encoder->encodePassword($adminUser, 'password');
        $adminUser->setPassword($password);
        $adminUser->setEmail('admin@project.com');
        $manager->persist($adminUser);

        $manager->flush();
    }
}
