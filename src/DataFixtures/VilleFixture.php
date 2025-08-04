<?php

namespace App\DataFixtures;

use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class VilleFixture extends Fixture
{
    public static int $count = 0;

    public function load(ObjectManager $manager): void
    {
        $villes = [
            ["nom" => "Nantes", "cp" => "44000"],
            ["nom" => "Rennes", "cp" => "35000"],
            ["nom" => "Quimper", "cp" => "29000"],
            ["nom" => "Niort", "cp" => "79000"],
        ];

        foreach ($villes as $i => $data) {
            $ville = new Ville();
            $ville->setName($data['nom']);
            $ville->setCodePostal($data['cp']);

            $manager->persist($ville);

            $this->addReference("ville_" . $i, $ville);
        }
        $count = $i;

        $manager->flush();
    }
}
