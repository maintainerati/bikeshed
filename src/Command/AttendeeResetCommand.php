<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Command;

use Doctrine\ORM\EntityManagerInterface;
use Maintainerati\Bikeshed\DataTransfer\AttendeeData;
use Maintainerati\Bikeshed\Entity\Attendee;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AttendeeResetCommand extends Command
{
    protected static $defaultName = 'bikeshed:attendee-reset';

    /** @var EntityManagerInterface */
    private $entityManager;
    /** @var ValidatorInterface */
    private $validator;
    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    /** @var SymfonyStyle */
    private $io;
    /** @var AttendeeData */
    private $dto;

    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        parent::__construct(static::$defaultName);
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Create an attendee')
            ->addArgument('email', InputArgument::REQUIRED, 'The attendee\'s account email address')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        $emailAddress = trim($input->getArgument('email'));
        $attendee = $this->entityManager
            ->getRepository(Attendee::class)
            ->findOneBy(['email' => $emailAddress])
        ;
        if (!$attendee) {
            $this->io->warning("Attendee account not found ($emailAddress)");

            return 1;
        }
        $this->dto = AttendeeData::createFromEntity($attendee);

        $this->io->section('Resetting password for attendee: ' . $attendee->getId());
        while (!$this->io->askHidden('Enter new password', $this->getValidator())) {
            continue;
        }
        while (!$this->io->askHidden('Repeat password', $this->getValidator())) {
            continue;
        }
        $password = $this->passwordEncoder->encodePassword($attendee, (string) $this->dto->getPassword());
        $attendee->setPassword($password);

        $this->entityManager->persist($attendee);
        $this->entityManager->flush();

        $this->io->success('Attendee created');

        return 0;
    }

    private function getPassword(Attendee $attendee, string $password): string
    {
        while (!$this->io->askHidden('Enter new password', $this->getValidator())) {
            continue;
        }
        while (!$this->io->askHidden('Repeat password', $this->getValidator())) {
            continue;
        }

        return $this->passwordEncoder->encodePassword($attendee, (string) $password);
    }

    private function getValidator(): callable
    {
        return function (?string $password): bool {
            $vDto = clone $this->dto;
            $vDto->setPassword($password);

            if ($this->dto->getPassword() !== null && $this->dto->getPassword() !== $password) {
                $this->io->error('Passwords must match');

                return false;
            }

            /** @var ConstraintViolationList $violations */
            $violations = $this->validator->validate($vDto);
            if ($violations->count() === 0) {
                $this->dto->setPassword($password);

                return true;
            }
            $errorMessages = [];
            /** @var ConstraintViolation $violation */
            foreach ($violations as $violation) {
                $errorMessages[] = $violation->getMessage();
            }
            $this->io->error($errorMessages);

            return false;
        };
    }
}
