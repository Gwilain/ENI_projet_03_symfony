<?php

namespace App\DataFixtures;

use App\Entity\Etat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EtatFixture extends Fixture
{
    private $states = ["En création", "ouverte", "Cloturée", "En cours", "Terminée", "Annulée", "Historisée"];

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker = \Faker\Factory::create('fr_FR');

        foreach ($this->states as $i => $state) {
            $etat = new Etat();
            $etat->setLibelle($state);
            $manager->persist($etat);

            $this->addReference('etat_' . $i, $etat);
        }

        $manager->flush();
    }
}
