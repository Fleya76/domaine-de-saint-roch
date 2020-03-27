<?php 

namespace App\Service;

use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UsersCount extends AbstractController
{
    public function getUsersCountNotValide(UserRepository $userRepository)
    {
        // TODO: Afficher les utilisateurs non validé dans la navbar
        return count($userRepository->findBy(['validation' => '0']));
    }

    // public function getUsersByEndContractSoon(UserRepository $userRepository)
    // {
    //     return count($userRepository->findBy(['validation' => '0']));
    // }

}