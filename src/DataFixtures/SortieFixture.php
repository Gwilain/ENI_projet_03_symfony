<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\User;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SortieFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');
        $verbs = ['Randonnée', 'Atelier', 'Soirée', 'Balade', 'Découverte', 'Initiation', 'Sortie', 'Tournoi', 'Marche', 'Visite'];
        $adjectives= ['dans le parc', 'gourmande', 'au musée', 'entre amis', 'créative', 'nocturne', 'nature', 'sportive', 'musicale', 'au bord de l’eau'];

        for( $i = 0; $i < 60; $i++ ) {
            $sortie = new Sortie();

            $title = $faker->randomElement($verbs) . ' ' . $faker->randomElement($adjectives);
            $sortie->setName( $title );
//            $date = $faker->dateTimeThisYear();
            $date = $faker->dateTimeBetween('first day of January this year', 'last day of December this year');
            $sortie->setDateHeureDebut(  \DateTimeImmutable::createFromMutable($date));

            $minutes = $faker->numberBetween(30, 360);
            $duration = (new \DateTime())->setTime(0, 0)->add(new \DateInterval('PT' . $minutes . 'M'));
            $sortie->setDuree( $duration );

            $hoursBefore = $faker->numberBetween(1, 48);
            $dateLimit = (clone $date)->sub(new \DateInterval('PT' . $hoursBefore . 'H'));
            $sortie->setDateLimiteInscription(  \DateTimeImmutable::createFromMutable($dateLimit));

            $nbInscritMax = $faker->numberBetween(4, 20);
            $sortie->setNbInscriptionMax($nbInscritMax);
            $sortie->setInfosSortie($faker->text);


            $orgaIndex = $faker->numberBetween(0, 9);
            $organisateur = $this->getReference('user_' . $orgaIndex, User::class);
            $sortie->setOrganisateur($organisateur);

            $sortie->addParticipant($organisateur);
            $nbParticipant = $faker->numberBetween(0, $nbInscritMax-1);

            for ($j = 0; $j < $nbParticipant; $j++) {
                $participantIndex = $faker->numberBetween(0, 9);
                $participant =  $this->getReference('user_' . $participantIndex, User::class);

                if (!$sortie->getParticipants()->contains($participant)) {
                    $sortie->addParticipant($participant);
                }
            }

            $rLName = $faker->name();
            $nomLieu = $faker->randomElement([
                    'Salle des fêtes',
                    'Parc municipal',
                    'Maison des associations',
                    'Gymnase',
                    'Espace culturel',
                ]) .  $rLName;

            $lieu = new Lieu();
            $lieu->setName($nomLieu);
            $lieu->setRue($faker->streetAddress);
            $villeId = $faker->numberBetween(0, VilleFixture::$count);

            $lieu->setVille( $this->getReference('ville_' . $villeId, Ville::class) );


            $sortie->setLieu($lieu);

            $manager->persist($lieu);

            $now = new \DateTimeImmutable('today');
            if ($date < $now) {
                $etatIndex = $faker->numberBetween(0, 1);
                $sortie->setEtat($this->getReference('etat_' . $etatIndex, Etat::class));
            }else{
                //etat_4  = Terminées
                $sortie->setEtat($this->getReference('etat_4', Etat::class));
            }

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
            VilleFixture::class,
            UserFixture::class,
            EtatFixture::class,
        ];
    }
}
