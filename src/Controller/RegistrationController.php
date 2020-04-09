<?php

namespace App\Controller;

use App\Entity\User;
use App\Events\RegistrationEvent;
use App\Form\RegistrationType;
use App\Security\LoginFormAuthenticator;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator, EventDispatcherInterface $eventDispatcher): Response
    {

        // TODO: Si un utilisateur s'inscrit et est en attente de validation, Jimmy reçoit une notification par mail ou SMS

        
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        dump($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $user->setValidation(false);
            // $user->addDog($dog);
            // $user->addDog($user->getDog());
            $user->setRoles(["ROLE_USER", "ROLE_NOT_SUBSCRIBER"]);

            $entityManager = $this->getDoctrine()->getManager();
            // $entityManager->persist($dog);
            $entityManager->persist($user);
            $entityManager->flush();

            // $eventDispatcher->dispatch( new RegistrationEvent($user));
            // do anything else you need here, like send an email

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
