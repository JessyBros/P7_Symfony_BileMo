<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CustomerFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $customer = new Customer();
        $customer->setUsername("Smartfox")
            ->setEmail("contact@smartfox.com")
            ->setPassword($this->passwordEncoder->encodePassword($customer,'smartfoxPassword'));
        $manager->persist($customer);

        $manager->flush();
        $this->addReference('customerSmartfox', $customer);
    }
}
