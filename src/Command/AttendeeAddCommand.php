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

class AttendeeAddCommand extends Command
{
    protected static $defaultName = 'bikeshed:attendee-add';

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
            ->addArgument('handle', InputArgument::REQUIRED, 'A user ID')
            ->addArgument('email', InputArgument::REQUIRED, 'A valid email address to be used as the login ID')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->dto = new AttendeeData();

        $handle = $input->getArgument('handle');
        $emailAddress = $input->getArgument('email');

        $this->dto->setId($handle);
        $this->dto->setEmail($emailAddress);

        if ($this->entityManager->getRepository(Attendee::class)->find($emailAddress)) {
            $this->io->warning("Attendee with the email address of '$emailAddress' already exists");

            return 1;
        }

        $this->io->section("Creating attendee: $handle");

        while (!$this->io->askHidden("Enter new password for $handle", $this->getValidator())) {
            continue;
        }
        while (!$this->io->askHidden('Repeat password', $this->getValidator())) {
            continue;
        }

        $attendee = $this->createEntity($this->dto->getId());
        $password = $this->passwordEncoder->encodePassword($attendee, (string) $this->dto->getPassword());
        $attendee
            ->setEmail($this->dto->getEmail())
            ->setPassword($this->dto->getPassword())
            ->setPasswordExpires(new \DateTime('now'))
            ->setRoles(['ROLE_USER'])
        ;
        $attendee->setPassword($password);

        $this->entityManager->persist($attendee);
        $this->entityManager->flush();

        $this->io->success('Attendee created');

        return 0;
    }

    private function createEntity(string $uuid): Attendee
    {
        $attendee = new Attendee();
        $rc = new \ReflectionClass($attendee);
        $rp = $rc->getProperty('id');
        $rp->setAccessible(true);
        $rp->setValue($rc, $uuid);

        return $attendee;
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
