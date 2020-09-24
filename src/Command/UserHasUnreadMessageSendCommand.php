<?php

namespace App\Command;

use App\Repository\UserRepository;
use App\Service\FireBaseService;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

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

    public function __construct(UserRepository $userRepository, FireBaseService $fireBaseService, MailerInterface $mailer)
    {
        parent::__construct(null);
        $this->userRepository = $userRepository;
        $this->fireBaseService = $fireBaseService;
        $this->mailer = $mailer;
    }


    protected function configure()
    {
        $this
            ->setDescription('Used to send users an email if they have had an unread message for 15 minutes or more')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $unreadMessages = $this->fireBaseService->getUnreadMessages();

//        dd($messages);

        // Looping over the unread messages and sending an email to the email attached to the recipient of the message
        $io->progressStart(count($unreadMessages));
        foreach ($unreadMessages as $unreadMessage) {
            if ($unreadMessage) {
                $email = (new TemplatedEmail())
                    ->from(new Address('gradtask@wren.com', 'gradtask'))
                    ->to($unreadMessage->getRecipientId()->getEmail())
                    ->subject('You have an unread message!')
                    ->html('Yes');

                $this->mailer->send($email);

                $io->progressAdvance();
            }
        }
        $io->progressFinish();

        $io->success('Emails were sent to users');

        return 0;
    }
}
