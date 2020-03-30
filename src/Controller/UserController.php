<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="user_index", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/overview.html.twig', [
            'users' => $userRepository->findBy(['validation' => '1']),
            
        ]);
    }

    /**
     * @Route("/validation", name="user_validation")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function validationView(UserRepository $userRepository)
    {    
        // TODO: Si un utilisateur est validé il reçoit une notification par mail ou SMS

        return $this->render('user/validation.html.twig', [
            'controller_name' => 'UserController',
            'users' => $userRepository->findBy(['validation' => '0'])
        ]);
    }

    /**
     * @Route("/validation/{id}", name="user_validation_id")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function validationAction(User $user, UserRepository $userRepository)
    {    
        // TODO: Si un utilisateur est validé il reçoit une notification par mail ou SMS
        $entityManager = $this->getDoctrine()->getManager();
        
        $user->setValidation(1);
        $user->setRoles(["ROLE_USER", "ROLE_SUBSCRIBER"]);
        // $entityManager->persist($dog);
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('user_validation');
    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     * @Security("is_granted('ROLE_USER') or is_granted('ROLE_ADMIN')", statusCode=499,message="Vou devez être connecté et être propriétaire du profil pour le modifier ou supprimer")
     */
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}/{redirection}", name="user_delete")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function delete(Request $request, User $user, $redirection = ''): Response
    {
            $entityManager = $this->getDoctrine()->getManager();
            foreach($user->getDog() as $dog){
                $entityManager->remove($dog);
            }
            foreach($user->getContract() as $contract){
                $entityManager->remove($contract);
            }
            $entityManager->remove($user);
            $entityManager->flush();
        
        return $this->redirectToRoute($redirection);
    }
}
