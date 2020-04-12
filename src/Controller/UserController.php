<?php

namespace App\Controller;

use App\Entity\Message;
use DateTime;
use App\Entity\User;
use App\Form\MessageType;
use App\Form\UpdateUserType;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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
            'dateTime' => new DateTime()
        ]);
    }

    /**
     * @Route("/validation", name="user_validation")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function validationView(UserRepository $userRepository)
    {    
        return $this->render('user/validation.html.twig', [
            'dateTime' => new DateTime(),
            'users' => $userRepository->findBy(['validation' => '0'])
        ]);
    }

    /**
     * @Route("/validation/{id}", name="user_validation_id")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function validationAction(User $user, UserRepository $userRepository, \Swift_Mailer $mailer)
    {    
        $entityManager = $this->getDoctrine()->getManager();
        
        $user->setValidation(1);
        $user->setRoles(["ROLE_USER", "ROLE_SUBSCRIBER"]);

        $href = $this->generateUrl('booking_index', [
          ], UrlGeneratorInterface::ABSOLUTE_URL);
  
        $lien='<br><a href="'.$href.'">Afficher le calendrier du Domaine de Saint-Roch</a>';
        //Envoi du message
        $message = new \Swift_Message('Jimmy GRESSENT à validé votre profil');
        $message->setFrom('admin.blog@email.fr');
        $message->setTo([$user->getEmail() => 'Utilisateur']);
        $message->setBody("
            <h1>Bonjour " . $user . "</h1>
            Vous pouvez dès maintenant consulter l'ensemble des cours dispensé par le Domaine de Saint-Roch.
            <br>
            <br>Lien vers le calendrier : ". $lien ."
            
            ",
            'text/html'
        );
        try {
            $retour=$mailer->send($message);
        }
        catch (\Swift_TransportException $e) {
            $retour= $e->getMessage();
        }

        $entityManager->persist($user);
        $entityManager->flush();
        $this->addFlash('success', $user->getFirstName() . ' ' .$user->getLastName() . ' à bien été validé.');
        return $this->redirectToRoute('user_validation');
    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     * @Security("user===userConnect or is_granted('ROLE_ADMIN')")
     */
    public function show(User $userConnect): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $userConnect,
            'dateTime' => new DateTime()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     * @Security("user===userConnect and is_granted('ROLE_SUBSCRIBER') or is_granted('ROLE_ADMIN')", statusCode=499,message="Vou devez être connecté et être propriétaire du profil pour le modifier ou supprimer")
     */
    public function edit(Request $request, User $userConnect): Response
    {
        $form = $this->createForm(UpdateUserType::class, $userConnect);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', $userConnect->getFirstName() . ' ' . $userConnect->getLastName() . ' à bien été modifié.');
            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $userConnect,
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
            $this->addFlash('warning', $user->getFirstName() . ' ' . $user->getLastName() . ' à bien été supprimé.');
            $entityManager->remove($user);
            $entityManager->flush();
        
        return $this->redirectToRoute($redirection);
    }

    /**
     * @Route("/{id}/contact", name="user_contact")
     */
    public function contact(User $user, Request $request)
    {
        $message = new Message();
    
        $form = $this->createForm(MessageType::class, $message);
        dump($form);
        // $form = $this->createForm(UpdateUserType::class, $userConnect);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $this->getDoctrine()->getManager()->flush();
            // $this->addFlash('success', $userConnect->getFirstName() . ' ' . $userConnect->getLastName() . ' à bien été modifié.');
            // return $this->redirectToRoute('user_index');
        }

        return $this->render('user/contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
