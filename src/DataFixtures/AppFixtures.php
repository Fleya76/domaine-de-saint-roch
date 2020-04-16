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

class AppFixtures extends Fixture
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
            ->setPhone('0695060476')
            ->setPassword($this->encoder->encodePassword($admin,'admin76'))
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

        for ($e=0; $e < 70; $e++) { 
            $booking = new Booking();
            $booking->setTitle($faker->sentence($nbWords = 6, $variableNbWords = true))
                ->setBeginAt($faker->dateTimeBetween('-3 months'));

            $booking->setEndAt($faker->dateTimeInInterval($booking->getBeginAt(), $interval = '+ 2 hours'));

            if($e>=60){
                $booking->setCategory($category1);
                $booking->setPlace($place1);
            }elseif($e>=50) {
                $booking->setCategory($category2);
                $booking->setPlace($place1);
            }elseif($e>=40) {
                $booking->setCategory($category3);
                $booking->setPlace($place1);
            }elseif($e>=30) {
                $booking->setCategory($category4);
                $booking->setPlace($place2);
            }elseif($e>=20) {
                $booking->setCategory($category5);
                $booking->setPlace($place2);
            }elseif($e>=10) {
                $booking->setCategory($category6);
                $booking->setPlace($place2);
            }else{
                $booking->setCategory($category7);
                $booking->setPlace($place3);
            }
            
            $manager->persist($booking);

            for ($u=0; $u < 10; $u++) { 
                $user = new User();

                $user->setEmail($faker->email())
                    ->setLastName($faker->lastName())
                    ->setFirstName($faker->firstNameMale())
                    ->setPhone($faker->phoneNumber())
                    ->setAddress($faker->streetAddress())
                    ->setImage('jimmy.png')
                    ->setPostalCode(14100)
                    ->setCity($faker->city())
                    ->setPassword($this->encoder->encodePassword($user,'admin76'))
                    ->addBooking($booking);

                    if($u < 5) {
                        $user->setRoles(['ROLE_USER','ROLE_SUBSCRIBER'])
                            ->setValidation(true);
                    }else{
                        $user->setRoles(['ROLE_USER','ROLE_NOT_SUBSCRIBER'])
                            ->setValidation(false);
                    }

                $manager->persist($user);
    
                $dog = new Dog();
                $dog->setName($faker->lastName())
                    ->setBreed('Malinois')
                    ->setUser($user);
                $manager->persist($dog);
                
                $contract = new Contract();
                $contract->setBeginAt($faker->dateTimeBetween('-3 months'))
                    ->setUser($user);
                $contract->setEndAt($faker->dateTimeInInterval($contract->getBeginAt(), $interval = '+ 1 year'));
                $manager->persist($contract);
    
                $message = new Message();
                $message->setSubject("Demande de rendez-vous")
                    ->setSendAt(new \DateTime())
                    ->setContent($faker->paragraph($nbSentences = 15, $variableNbSentences = true))
                    ->setAuthor($user)
                    ->setDog($dog)
                    ->setMessageRead(false);
                $manager->persist($message);
            }
        }
        for ($v=0; $v < 20; $v++) { 
            $video = new Video();
            $video->setTitle($faker->sentence($nbWords = 6, $variableNbWords = true))
                ->setPath("5e94c0380e29c.mp4")
                ->setCreatedAt($faker->dateTimeBetween('-3 months'))
                ->setContent($faker->paragraph($nbSentences = 3, $variableNbSentences = true));

                if($v>=20){
                    $video->setCategory($category1);
                }elseif($v>=15) {
                    $video->setCategory($category2);
                }elseif($v>=10) {
                    $video->setCategory($category3);
                }elseif($v>=5) {
                    $video->setCategory($category4);
                }else{
                    $video->setCategory($category5);
                }
            $manager->persist($video);
            for ($c=0; $c < 3; $c++) { 
                $comment = new Comment();
                $comment->setAuthor($user)
                     ->setVideo($video)
                     ->setCreatedAt(new DateTime())
                     ->setContent($faker->paragraph($nbSentences = 1, $variableNbSentences = true));
                $manager->persist($comment);
    
             }
        }

        $manager->flush();
    }
}
