<?php

namespace App\Tests\Entity;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use http\Client;
use Liip\FunctionalTestBundle\Test\WebTestCase;

class RegistrationTest extends WebTestCase
{
    /** @var EntityManager */
    private $manager;

    /** @var Client */
    private $client;

    protected function setUp(): void
    {
        $this->client = $this->makeClient();
        $kernel = self::bootKernel();
        $this->manager = $kernel->getContainer()->get('doctrine')->getManager();
        parent::setUp();
    }

    public function testRegistraionPageAccess() // Tests that the registration page can be accessed
    {
        $this->client->request('GET', '/register');
        $this->assertStatusCode(200, $this->client);
    }

    public function testRegistrationForm() // Tests that the form works
    {
        $crawler = $this->client->request('GET', '/register');
        $form = $crawler->selectButton('Register')->form();
        $form->setValues(['registration_form[username]' => 'nobody', 'registration_form[email]' => 'nobody@email.com', 'registration_form[plainPassword]' => 'password', 'registration_form[agreeTerms]' => 1]);
        $this->client->submit($form);
        $this->assertStatusCode(302, $this->client);
    }

    public function testNewUserIsInDatabase() // Tests that information entered to the form is added to the database
    {
        $crawler = $this->client->request('GET', '/register');
        $form = $crawler->selectButton('Register')->form();
        $form->setValues(['registration_form[username]' => 'testuser1', 'registration_form[email]' => 'testuser1@test.com', 'registration_form[plainPassword]' => 'password', 'registration_form[agreeTerms]' => 1]);
        $this->client->submit($form);

        $user = $this->manager
            ->getRepository(User::class)
            ->findOneBy(['username' => 'testuser1', 'email' => 'testuser1@test.com']);

        $this->assertSame('testuser1', $user->getUsername());
        $this->assertSame('testuser1@test.com', $user->getEmail());
        $this->assertStatusCode(302, $this->client);
    }

//    public function testRegistrationValidation()
//    {
//        $crawler = $this->client->request('GET', '/register');
//        $form = $crawler->selectButton('Register')->form();
//        $this->client->submit($form);
//        dd($this->getContainer()->get('liip_functional_test.validator')->getLastErrors());
//
//        $this->assertValidationErrors(['data.email'], $this->getContainer());
//
//
//    }
}
