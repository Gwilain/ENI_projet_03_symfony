<?php

namespace App\DataFixtures;

use App\Entity\Etat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EtatFixture extends Fixture
{
    private array $states = [
        ['libelle' => 'En création', 'code' => Etat::CODE_EN_CREATION],
        ['libelle' => 'Ouverte', 'code' => Etat::CODE_OUVERTE],
        ['libelle' => 'En cours', 'code' => Etat::CODE_EN_COURS],
        ['libelle' => 'Clôturée', 'code' => Etat::CODE_CLOTUREE],
        ['libelle' => 'Terminée', 'code' => Etat::CODE_TERMINEE],
        ['libelle' => 'Annulée', 'code' => Etat::CODE_ANNULEE],
        ['libelle' => 'Historisée', 'code' => Etat::CODE_HISTORISEE],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach ($this->states as $state) {
            $etat = new Etat();
            $etat->setLibelle($state['libelle']);
            $etat->setCode($state['code']);
            $manager->persist($etat);

            $this->addReference($state['code'], $etat);
        }

        $manager->flush();
    }
}
