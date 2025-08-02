<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Sortie;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SortieFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        $verbs = ['Randonnée', 'Atelier', 'Soirée', 'Balade', 'Découverte', 'Initiation', 'Sortie', 'Tournoi', 'Marche', 'Visite'];
        $nouns = ['dans le parc', 'gourmande', 'au musée', 'entre amis', 'créative', 'nocturne', 'nature', 'sportive', 'musicale', 'au bord de l’eau'];

        for( $i = 0; $i < 10; $i++ ) {
            $sortie = new Sortie();

            $title = $faker->randomElement($verbs) . ' ' . $faker->randomElement($nouns);
            $sortie->setName( $title );
            $date = $faker->dateTimeThisYear();
            $sortie->setDateHeureDebut(  \DateTimeImmutable::createFromMutable($date));

            $minutes = $faker->numberBetween(30, 360);
            $duration = (new \DateTime())->setTime(0, 0)->add(new \DateInterval('PT' . $minutes . 'M'));
            $sortie->setDuree( $duration );

            $hoursBefore = $faker->numberBetween(1, 48);
            $dateLimit = (clone $date)->sub(new \DateInterval('PT' . $hoursBefore . 'H'));
            $sortie->setDateLimiteInscription(  \DateTimeImmutable::createFromMutable($dateLimit));

            $sortie->setNbInscriptionMax($faker->numberBetween(2, 15));
            $sortie->getInfosSortie($faker->text);
            /*$etatIndex = $faker->numberBetween(0, 6);
            $sortie->setEtat($this->getReference('etat_' . $etatIndex, Etat::class));*/

            $etatIndex = $faker->numberBetween(0, 6);
            $sortie->setEtat($this->getReference('etat_' . $etatIndex, Etat::class));

            $orgaIndex = $faker->numberBetween(0, 9);
            $organisateur = $this->getReference('user_' . $orgaIndex, User::class);
            $sortie->setOrganisateur($organisateur);

            $sortie->setCampus( $organisateur->getCampus() );
            //$campusIndex = $faker->numberBetween(0, 3);

            $manager->persist($sortie);

        }



        $manager->flush();
    }


    public function getDependencies(): array
    {
        return [
            CampusFixture::class,
            UserFixture::class,
            EtatFixture::class,
        ];
    }
}
