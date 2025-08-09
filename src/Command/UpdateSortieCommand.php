<?php

namespace App\Command;

use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'UpdateSortie',
    description: 'Update Sortie status if the event is finished from over a month or if the limit inscription date is over',
)]
class UpdateSortieCommand extends Command
{
    public function __construct(
        private SortieRepository $sortieRepo,
        private EtatRepository $etatRepo,
        private EntityManagerInterface $em

    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('nbDays', 'd', InputOption::VALUE_NONE, 'Option description')
            ->addArgument('nbDays', InputArgument::OPTIONAL, 'number of days after the Sortie ie over', 30)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
