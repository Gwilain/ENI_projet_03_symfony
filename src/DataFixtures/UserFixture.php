<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends Fixture
{

    public function __construct(
        private readonly UserPasswordHasherInterface $hasher
    )
    {
    }


    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_CA');

        $user = new User();

        $user->setEmail("admin@eni.fr");
        $user->setPseudo("adminet");
        $user->setFirstName("bobby");
        $user->setLastname("McFerrin");
        $user->setEventAdmin(true);
        $user->setActive(true);


        $user->setRoles([ "ROLE_ADMIN"]);
        $user->setPassword( $this->hasher->hashPassword( $user, "123456") );
        $manager->persist($user);

        for( $i = 0; $i < 10; $i++ ) {
            $user2 = new User();
            $user2->setEmail("user".$i."@eni.fr");
            $user2->setPseudo("user".$i);
            $user2->setFirstName($faker->firstName);
            $user2->setLastname($faker->lastName());
            $user2->setRoles([ "ROLE_USER"]);
            $user2->setEventAdmin($faker->boolean(25));

            $user2->setRoles([ "ROLE_USER"]);
            $user2->setPassword( $this->hasher->hashPassword( $user2, "123456") );
            $user2->setActive(true);
            $manager->persist($user2);
            //$this->addReference("user".$i, $user2);

        }

        $manager->flush();
    }
}
