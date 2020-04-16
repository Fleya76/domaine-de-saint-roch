<?php

namespace App\Controller;

use App\Entity\Dog;
use App\Entity\User;
use App\Form\DogType;
use App\Form\Dog1Type;
use App\Repository\DogRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Security as SymfonySecurity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/dog")
 */
class DogController extends AbstractController
{
    private $security;
    public function __construct(SymfonySecurity $security)
    {
        $this->security = $security;
    }

    // /**
    //  * @Route("/", name="dog_index", methods={"GET"})
    //  * @Security("is_granted('ROLE_ADMIN')")
    //  */
    // public function index(DogRepository $dogRepository): Response
    // {
    //     return $this->render('dog/index.html.twig', [
    //         'dogs' => $dogRepository->findAll(),
    //     ]);
    // }

    /**
     * @Route("/{user}/new", name="dog_new", methods={"GET","POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function new(Request $request, User $user): Response
    {
        $dog = new Dog();
        $form = $this->createForm(DogType::class, $dog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dog->setUser($user);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($dog);
            $entityManager->flush();

            return $this->redirectToRoute('user_show', [
                'id' => $user->getId()
            ]);
        }

        return $this->render('dog/new.html.twig', [
            'dog' => $dog,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{userConnect}/{id}", name="dog_show", methods={"GET"})
     * @Security("user===userConnect or is_granted('ROLE_ADMIN')")
     */
    public function show(Dog $dog, User $userConnect): Response
    {
        return $this->render('dog/show.html.twig', [
            'dog' => $dog,
        ]);
    }

    /**
     * @Route("/{userConnect}/{id}/edit", name="dog_edit", methods={"GET","POST"})
     * @Security("user===userConnect or is_granted('ROLE_ADMIN')")
     */
    public function edit(Request $request, Dog $dog, User $userConnect): Response
    {
        $form = $this->createForm(DogType::class, $dog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_show', [
                'id' => $dog->getUser()->getId()
            ]);
        }

        return $this->render('dog/edit.html.twig', [
            'dog' => $dog,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{userConnect}/{{id}", name="dog_delete")
     * @Security("user===userConnect or is_granted('ROLE_ADMIN')")
     */
    public function delete(Request $request, Dog $dog, User $userConnect): Response
    {
     
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($dog);
        $entityManager->flush();


        return $this->redirectToRoute('user_show', [
            'id' => $dog->getUser()->getId()
        ]);
    }
}
