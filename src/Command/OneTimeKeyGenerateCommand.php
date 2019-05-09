<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Command;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Maintainerati\Bikeshed\Entity\OneTimeKey;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class OneTimeKeyGenerateCommand extends Command
{
    protected static $defaultName = 'bikeshed:otk-generate';

    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Generate a batch of registration keys')
            ->addOption('count', null, InputOption::VALUE_OPTIONAL, 'Number of keys to generate', 200)
            ->addOption('expiry', null, InputOption::VALUE_OPTIONAL, 'Expiry date of keys (max +1 month) (YY-MM-DD)', (new \DateTime('+1 day'))->format('Y-m-d'))
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);
        $count = (int) $input->getOption('count');
        $expiry = $input->getOption('expiry');

        for ($i = 0; $i < $count; ++$i) {
            $entity = new OneTimeKey(new DateTimeImmutable($expiry));
            $this->em->persist($entity);
        }
        $this->em->flush();

        $io->success('Done. Keys expire ' . $expiry);
    }
}
