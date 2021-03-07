<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 12; ++$i) {
            $user = new User();
            $user->setName($faker->name())
                ->setEmail($faker->email())
                ->setNumber($faker->serviceNumber())
                ->setCustomer($this->getReference('customerSmartfox'));
            $manager->persist($user);
        }
        for ($i = 0; $i < 7; ++$i) {
            $user = new User();
            $user->setName($faker->name())
                ->setEmail($faker->email())
                ->setNumber($faker->serviceNumber())
                ->setCustomer($this->getReference('customerKokoBeats'));
            $manager->persist($user);
        }

        $manager->flush();
    }
}
