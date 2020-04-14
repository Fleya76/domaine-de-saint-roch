<?php 

namespace App\Service;

use DateTime;
use DateInterval;
use App\Entity\Message;
use App\Repository\UserRepository;
use App\Repository\ContractRepository;
use App\Repository\MessageRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Notification extends AbstractController
{
    private $userRepository;
    private $contractRepository;
    private $messageRepository;

    public function __construct(UserRepository $userRepository, ContractRepository $contractRepository, MessageRepository $messageRepository) {
        $this->userRepository = $userRepository;
        $this->contractRepository = $contractRepository;
        $this->messageRepository = $messageRepository;
    }

    public function getUsersCountNotValide()
    {
        return count($this->userRepository->findBy(['validation' => '0']));
    }

    public function getUsersByEndContractSoon()
    {
        //TODO : Code fonctionnel cependant pas optimisé. Trop long à charger
        // $contracts = $this->contractRepository->findAll();
        // $endContracts = [];
        // $dateTime = new DateTime();
        // foreach ($contracts as $contract) {
        //     if ($dateTime < $contract->getEndAt()) {
        //         if($dateTime > $contract->getEndAt()->sub(new DateInterval('P1M')))
        //         {
        //             dump($contract->getEndAt());
        //             array_push($endContracts, $contract);
        //         }
        //     }
        // }
        // return count($endContracts);
        return 2;
    }
    public function getMessageCount()
    {
        //TODO : Code à faire. Trop long à charger

        return count($this->messageRepository->findBy(['messageRead' => '0']));
        // return count($this->messageRepository->findBy(['messageRead' => '0']));
        return 2;
    }


}