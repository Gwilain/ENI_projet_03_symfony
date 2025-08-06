<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CampusFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $campuses = ["Nantes", "Rennes", "Quimper", "Niort"];

        foreach ($campuses as $key => $c) {

            $campus = new Campus();
            $campus->setName($c);

            $manager->persist($campus);

            $this->addReference("campus_$key", $campus);
        }

        $manager->flush();
    }
}
