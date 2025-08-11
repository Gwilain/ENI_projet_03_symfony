<?php

namespace App\Command;

use App\Entity\Etat;
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
    name: 'app:archivate',
    description: 'Archive toutes les sorties ayant eu lieu il y a plus de X jours (30 par défaut).',
)]
class ArchiveOldSortiesCommand extends Command
{
    public function __construct(
        private SortieRepository $sortieRepository,
        private EntityManagerInterface $em,
        private EtatRepository $etatRepository,

    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                'days',
                InputArgument::OPTIONAL,
                'Nombre de jours à partir duquel archiver',
                30 // valeur par défaut
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $days = (int) $input->getArgument('days');
        $dateLimite = new \DateTimeImmutable(sprintf('-%d days', $days));

        $sorties = $this->sortieRepository->findToArchive($dateLimite);

        $etatHistorisee = $this->etatRepository->findOneBy(['code' => Etat::CODE_HISTORISEE]);

        foreach ($sorties as $sortie) {
            $sortie->setEtat($etatHistorisee);
        }

        $this->em->flush();

        $output->writeln(sprintf(
            '%d sorties archivées (plus de %d jours).',
            count($sorties),
            $days
        ));

        return Command::SUCCESS;
    }
}