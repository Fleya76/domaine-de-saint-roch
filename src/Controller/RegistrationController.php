<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\FileUploader;
use App\Form\RegistrationType;
use App\Events\RegistrationEvent;
use App\Security\LoginFormAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\EventDispatcher\EventDispatcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator, FileUploader $fileUploader, EventDispatcherInterface $eventDispatcher): Response
    {
        
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $file=$form['image']->getData();           
            if($file){
                $res=$fileUploader->upload($file);
                if(is_string($res)){
                    $user->setImage($res);
                }else{
                    $message=$res->getMessage();
                    $user->setImage('');
                }
            }else{
                $user->setImage(''); 
            }
            

            $user->setValidation(false);
            // $user->addDog($dog);
            // $user->addDog($user->getDog());
            $user->setRoles(["ROLE_USER", "ROLE_NOT_SUBSCRIBER"]);

            $entityManager = $this->getDoctrine()->getManager();
            // $entityManager->persist($dog);
            $entityManager->persist($user);
            $entityManager->flush();

            $eventDispatcher->dispatch( new RegistrationEvent($user));
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
