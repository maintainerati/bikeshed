<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Command;

use DateTimeImmutable;
use Maintainerati\Bikeshed\Entity\OneTimeKey;
use Maintainerati\Bikeshed\Repository\OneTimeKeyRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class OneTimeKeyListCommand extends Command
{
    protected static $defaultName = 'bikeshed:otk-list';

    /** @var OneTimeKeyRepository */
    private $repo;

    public function __construct(OneTimeKeyRepository $repo)
    {
        parent::__construct();
        $this->repo = $repo;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('List available one-time keys')
            ->addOption('expiry', null, InputOption::VALUE_OPTIONAL, 'Keys that expire before this date (YY-MM-DD)', (new \DateTime('+1 month'))->format('Y-m-d'))
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);
        $expiry = $input->getOption('expiry');

        $lastDate = null;
        $rows = [];
        $entities = $this->repo->findUntilDate(new DateTimeImmutable($expiry));
        /** @var OneTimeKey $entity */
        foreach ($entities as $entity) {
            if ($lastDate && $lastDate !== $entity->getValidUntil()) {
                $rows[] = new TableSeparator();
            }
            $rows[] = [$entity->getOneTimeKey(), $entity->getValidUntil()->format('Y-m-d')];
            $lastDate = $entity->getValidUntil();
        }
        $io->table(['Key', 'Valid until'], $rows);

        $io->success('Done. Keys expired: ' . $expiry);
    }
}
