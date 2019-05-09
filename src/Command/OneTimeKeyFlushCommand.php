<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Command;

use Doctrine\ORM\EntityManagerInterface;
use Maintainerati\Bikeshed\Repository\OneTimeKeyRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class OneTimeKeyFlushCommand extends Command
{
    protected static $defaultName = 'bikeshed:otk-flush';

    /** @var OneTimeKeyRepository */
    private $repo;
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(OneTimeKeyRepository $repo, EntityManagerInterface $em)
    {
        parent::__construct();
        $this->repo = $repo;
        $this->em = $em;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Flush expired keys')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);

        $expired = $this->repo->findExpired();
        foreach ($expired as $entity) {
            $this->em->remove($entity);
        }
        $this->em->flush();

        $io->success('Done. Keys flushed: ' . \count($expired));
    }
}
