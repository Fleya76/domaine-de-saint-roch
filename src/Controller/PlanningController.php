<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PlanningController extends AbstractController
{
    /**
     * @Route("/", name="planning")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function index()
    {
        // https://fullcalendar.io/

        return $this->render('planning/index.html.twig', [
            'controller_name' => 'PlanningController',
        ]);
    }
}
