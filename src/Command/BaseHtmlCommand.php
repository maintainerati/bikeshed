<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Twig\Environment;

class BaseHtmlCommand extends Command
{
    protected static $defaultName = 'bikeshed:extract-base-html';

    /** @var Environment */
    private $twig;
    ///** @var ParameterBagInterface */
    //private $params;
    ///** @var PageBuilder */
    //private $pageBuilder;
    ///** @var TeamPages */
    //private $teamPages;

    //public function __construct(Environment $twig, ParameterBagInterface $params, PageBuilder $pageBuilder, TeamPages $teamPages, string $name = null)
    public function __construct(Environment $twig, string $name = null)
    {
        parent::__construct($name);
        $this->twig = $twig;
        //$this->params = $params;
        //$this->pageBuilder = $pageBuilder;
        //$this->teamPages = $teamPages;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Generate the site base HTML for analysis by Webpack')
            ->addArgument('target', InputArgument::OPTIONAL, 'HTML file to output to')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);
        $target = $input->getArgument('target') ?? 'var/base.html';
        //if (Path::isRelative($target)) {
        //    $target = Path::makeAbsolute($target, $this->params->get('kernel.project_dir'));
        //}
        $fs = new Filesystem();
        $fs->dumpFile($target, $this->twig->render('homepage/index.html.twig', [
            //'page'         => $this->pageBuilder->get('about/index.md'),
            //'team'         => $this->teamPages->get(),
            'add_critical' => false,
        ]));

        $io->success(sprintf('HTML output dumped to %s', $target));
    }
}
