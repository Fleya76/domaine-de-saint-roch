<?php

namespace App\Controller;

use DateTime;
use DateInterval;
use App\Entity\User;
use App\Entity\Contract;
use App\Form\ContractType;
use App\Form\Contract1Type;
use App\Form\NewContractType;
use App\Repository\ContractRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Security as SymfonySecurity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/contract")
 */
class ContractController extends AbstractController
{
    private $security;
    public function __construct(SymfonySecurity $security)
    {
        $this->security = $security;
    }


    /**
     * @Route("/", name="contract_index", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function index(ContractRepository $contractRepository): Response
    {
        //TODO : Code fonctionnel cependant pas optimisé. Trop long à charger
        $contracts = $contractRepository->findAll();
        $endContracts = [];
        $dateTime = new DateTime();
        foreach ($contracts as $contract) {
            if ($dateTime < $contract->getEndAt()) {
                if($dateTime > $contract->getEndAt()->sub(new DateInterval('P1M')))
                {
                    dump($contract->getEndAt());
                    array_push($endContracts, $contract);
                }
            }
        }
        return $this->render('contract/overview.html.twig', [
            'contracts' => $endContracts,
        ]);
    }

    
    /**
     * @Route("/new/{id}", name="contract_new", methods={"GET","POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function new(User $user, Request $request): Response
    {
        $contract = new Contract();
        $form = $this->createForm(ContractType::class, $contract);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->addContract($contract);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_show', array(
                'id' => $user->getId())
            );
        }

        return $this->render('contract/new.html.twig', [
            'contract' => $contract,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="contract_show", methods={"GET"})
     */
    public function show(Contract $contract): Response
    {
        return $this->render('contract/show.html.twig', [
            'contract' => $contract,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="contract_edit", methods={"GET","POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function edit(Request $request, Contract $contract): Response
    {
        $form = $this->createForm(ContractType::class, $contract);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('contract_index');
        }

        return $this->render('contract/edit.html.twig', [
            'contract' => $contract,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="contract_delete", methods={"DELETE"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function delete(Request $request, Contract $contract): Response
    {
        if ($this->isCsrfTokenValid('delete'.$contract->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($contract);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_show', array(
            'id' => $contract->getUser()->getId()
        ));
    }
}
