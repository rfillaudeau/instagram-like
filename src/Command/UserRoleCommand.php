<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:user:role',
    description: 'Promote or demote a user with a role',
)]
class UserRoleCommand extends Command
{
    private const ACTION_PROMOTE = 'promote';
    private const ACTION_DEMOTE = 'demote';

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('action', InputArgument::REQUIRED, 'The action to make on the role: promote or demote')
            ->addArgument('email', InputArgument::REQUIRED, 'The user email')
            ->addArgument('role', InputArgument::REQUIRED, 'The role to promote or demote the user to')
        ;
    }

    /**
     * @throws NonUniqueResultException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $action = $input->getArgument('action');
        $email = $input->getArgument('email');
        $role = $input->getArgument('role');

        if (!in_array($action, [self::ACTION_PROMOTE, self::ACTION_DEMOTE])) {
            $io->error(sprintf(
                'Action "%s" not valid, must be either "%s" or "%s".',
                $action,
                self::ACTION_PROMOTE,
                self::ACTION_DEMOTE
            ));

            return Command::FAILURE;
        }

        $user = $this->userRepository->findOneByEmail($email);
        if (null === $user) {
            $io->error(sprintf('User "%s" not found.', $email));

            return Command::FAILURE;
        }

        if (!in_array($role, User::getAvailableRoles())) {
            $io->error(sprintf('Role "%s" does not exist.', $role));

            return Command::FAILURE;
        }

        if ($action === self::ACTION_PROMOTE) {
            $user->addRole($role);
        } else {
            $user->removeRole($role);
        }

        $this->entityManager->flush();

        $io->success(sprintf('User "%s" successfully %sd with the role "%s".', $email, $action, $role));

        return Command::SUCCESS;
    }
}
