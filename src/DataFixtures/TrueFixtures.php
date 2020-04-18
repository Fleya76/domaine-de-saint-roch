<?php

namespace App\DataFixtures;

use DateInterval;
use Faker\Factory;
use App\Entity\Dog;
use App\Entity\User;
use App\Entity\Place;
use App\Entity\Video;
use App\Entity\Booking;
use App\Entity\Comment;
use App\Entity\Message;
use App\Entity\Category;
use App\Entity\Contract;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class TrueFixtures extends Fixture
{
    
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create("fr_FR");

        $admin = new User();
        $admin->setEmail("fabien.poret@outlook.fr")
            ->setRoles(['ROLE_USER', 'ROLE_SUBSCRIBER', 'ROLE_ADMIN'])
            ->setLastName('Fabien')
            ->setFirstName('PORET')
            ->setImage('jimmy.png')
            ->setPhone('0695060476')
            ->setPassword($this->encoder->encodePassword($admin,'admin76'))
            ->setValidation(true);
        $manager->persist($admin);

        $admin = new User();
        $admin->setEmail("jimmy@domainedesaintroch.com")
            ->setRoles(['ROLE_USER', 'ROLE_SUBSCRIBER', 'ROLE_ADMIN'])
            ->setLastName('Domaine de Saint-Roch')
            ->setFirstName('Jimmy')
            ->setPhone('')
            ->setImage('jimmy.png')
            ->setPassword($this->encoder->encodePassword($admin,'123456789'))
            ->setValidation(true);
        $manager->persist($admin);

        $category1 = new Category();
        $category1->setTitle('Libre');
        $manager->persist($category1);

        $category2 = new Category();
        $category2->setTitle('Renforcement');
        $manager->persist($category2);

        $category3 = new Category();
        $category3->setTitle('Théorie techniques d\'apprentissage');
        $manager->persist($category3);

        $category4 = new Category();
        $category4->setTitle('Mise en pratique');
        $manager->persist($category4);

        $category5 = new Category();
        $category5->setTitle('Refus d\'appât');
        $manager->persist($category5);

        $category6 = new Category();
        $category6->setTitle('Formation théorique sur développement relationnel et étudactif au sein du foyer');
        $manager->persist($category6);

        $category7 = new Category();
        $category7->setTitle('Divers');
        $manager->persist($category7);

        $place1 = new Place();
        $place1->setTitle('Clinique vétérinaire');
        $manager->persist($place1);

        $place2 = new Place();
        $place2->setTitle('Domaine de Saint-Roch');
        $manager->persist($place2);

        $place3 = new Place();
        $place3->setTitle('Extérieure');
        $manager->persist($place3);

        $manager->flush();
    }
}
