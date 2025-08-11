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
    name: 'app:close-sorties',
    description: 'Cloture les sorties dont la date limite d\'inscription est dépassée',
)]
class ClotureSortiesCommand extends Command
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
        $io = new SymfonyStyle($input, $output);
        $now = new \DateTimeImmutable('now');

        // Récupérer les sorties à clôturer
        $sorties = $this->sortieRepository->findSortiesDateLimiteDepassee($now);

        if (empty($sorties)) {
            $io->info('Aucune sortie à clôturer.');
            return Command::SUCCESS;
        }

        //$etatCloturee = $this->em->getRepository(Etat::class)->findOneBy(['code' => Etat::CODE_CLOTUREE]);
        $etatCloturee = $this->etatRepository->findOneBy(['code' => Etat::CODE_CLOTUREE]);


        foreach ($sorties as $sortie) {
            $sortie->setEtat($etatCloturee);
        }

        $this->em->flush();

        $io->success(sprintf(
            '%d sorties clôturées (date limite dépassée).',
            count($sorties)
        ));

        return Command::SUCCESS;


    }

}