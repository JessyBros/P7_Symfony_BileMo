<?php

namespace App\DataFixtures;

use App\Entity\Phone;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class PhoneFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        for ($i = 1; $i < 26; ++$i) {
            $phone = new Phone();
            $phone->setName(sprintf('Phone %d', $i))
                ->setDescription($faker->text(100))
                ->setPrice($faker->randomFloat(2, 90, 700))
                ->setColor($faker->colorName())
                ->setSize($faker->randomFloat(1, 8.9, 22.9))
                ->setWeight($faker->numberBetween(112, 300));
            $manager->persist($phone);
        }

        $manager->flush();
    }
}
