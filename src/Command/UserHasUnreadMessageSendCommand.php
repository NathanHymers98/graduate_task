<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Message;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\FireBaseService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class UserHasUnreadMessageSendCommand.
 */
class UserHasUnreadMessageSendCommand extends Command
{
    protected static $defaultName = 'app:user-has-unread-message:send';

    private $userRepository;
    /**
     * @var FireBaseService
     */
    private $fireBaseService;
    /**
     * @var MailerInterface
     */
    private $mailer;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * UserHasUnreadMessageSendCommand constructor.
     * @param UserRepository $userRepository
     * @param FireBaseService $fireBaseService
     * @param MailerInterface $mailer
     * @param RouterInterface $router
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(UserRepository $userRepository, FireBaseService $fireBaseService, MailerInterface $mailer, RouterInterface $router, EntityManagerInterface $entityManager)
    {
        parent::__construct(null);
        $this->userRepository = $userRepository;
        $this->fireBaseService = $fireBaseService;
        $this->mailer = $mailer;
        $this->router = $router;
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this
            ->setDescription('Used to send users an email if they have had an unread message for 15 minutes or more')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $unreadMessages = $this->fireBaseService->getUnreadMessages();
        $groupedMessages = $this->groupUnreadMessages($unreadMessages);

        // Looping over the unread messages and sending an email to the email attached to the recipient of the message
        $io->progressStart(count($groupedMessages));
        foreach ($groupedMessages as $groupedMessage) {
            $recipient = $this->entityManager->getRepository(User::class)->find(['id' => $groupedMessage['recipientId']]);

            if (!empty($groupedMessages)) {
                $email = (new TemplatedEmail())
                   ->from(new Address('gradtask@wren.com', 'gradtask'))
                   ->to(new Address($recipient->getEmail()))
                   ->subject('You have unread messages!')
                   ->htmlTemplate('email/unread_messages_email.html.twig')
                   ->context([
                       'messages' => $groupedMessages,
                   ]);

                $this->mailer->send($email);
                $io->progressAdvance();
            }
        }
        $io->progressFinish();
        $io->success('Emails were sent to users');

        return 0;
    }

    /**
     * @param $unreadMessages
     *
     * @return mixed
     */
    public function groupUnreadMessages($unreadMessages)
    {
        foreach ($unreadMessages as $message) {
            $groupedMessages[$message['recipientId']] = $message;
        }

        return $groupedMessages;
    }
}
