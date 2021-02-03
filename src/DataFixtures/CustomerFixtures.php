<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CustomerFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $customer = new Customer();
        $customer->setName("Smartfox")
            ->setEmail("contact@smartfox.com");
        $manager->persist($customer);

        $manager->flush();
        $this->addReference('customerSmartfox', $customer);
    }
}
