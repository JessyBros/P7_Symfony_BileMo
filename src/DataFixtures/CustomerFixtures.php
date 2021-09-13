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
        $customerSmartfox = new Customer();
        $customerSmartfox->setUsername('Smartfox')
            ->setEmail('contact@smartfox.com')
            ->setPassword($this->passwordEncoder->encodePassword($customerSmartfox, 'SmartfoxPassword'));
        $manager->persist($customerSmartfox);

        $customerKokoBeats = new Customer();
        $customerKokoBeats->setUsername('KokoBeats')
            ->setEmail('contact@kokobeats.com')
            ->setPassword($this->passwordEncoder->encodePassword($customerKokoBeats, 'KokoBeatsPassword'));
        $manager->persist($customerKokoBeats);

        $eurekaci = new Customer();
        $eurekaci->setUsername('Eurekaci')
            ->setEmail('contact@eurekaci.com')
            ->setPassword($this->passwordEncoder->encodePassword($eurekaci, 'admin'));
        $manager->persist($eurekaci);

        $manager->flush();
        $this->addReference('customerSmartfox', $customerSmartfox);
        $this->addReference('customerKokoBeats', $customerKokoBeats);
        $this->addReference('customerEurekaci', $eurekaci);
    }
}
