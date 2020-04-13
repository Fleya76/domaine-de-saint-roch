<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Message;
use App\Form\MessageType;
use App\Form\Message1Type;
use App\Repository\MessageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Util\Fonctions;

/**
 * @Route("/message")
 */
class MessageController extends AbstractController
{
    /**
     * @Route("/", name="message_index", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function index(MessageRepository $messageRepository): Response
    {
        return $this->render('message/index.html.twig', [
            'messages' => array_reverse($messageRepository->findAll()),
        ]);
    }

    /**
     * @Route("/{id}/new", name="message_new", methods={"GET","POST"})
     */
    public function new(Request $request, User $user, \Swift_Mailer $mailer): Response
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message, [
            // 'dog' => $user->getDog()
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setSendAt(new \DateTime())
                ->setAuthor($user)
                ->setMessageRead(false)
                ->setSubject('Demande de rendez-vous individuel');
                // ->setDog($user->getDog());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($message);
            $entityManager->flush();

            $mail_admin=Fonctions::getEnv('mail_admin');

            $href = $this->generateUrl('booking_by_user', [
                'id' => $user->getId()
            ], UrlGeneratorInterface::ABSOLUTE_URL);
    
            $lien='<br><a href="'.$href.'">Afficher mes prochains cours du Domaine de Saint-Roch</a>';
            //Envoi du message

            // TODO: Ajouter un lien pour répondre
            $mail = new \Swift_Message($message->getSubject());
            $mail->setFrom($mail_admin);
            $mail->setTo([$user->getEmail() => 'Utilisateur']);
            $mail->setBody("
                <h1>" . $message->getSubject() . "</h1>
                Auteur du message : " . $message->getAuthor() . "
                <br>
                Envoyé le : " . $message->getSendAt()->format('H:i:s d-m-Y ') . "
                <br>
                <br>
                " . $message->getContent() . "
                <br>
                <br>
                " . $message->getAuthor() . "
                ",
                'text/html'
            );
            try {
                $retour=$mailer->send($mail);
            }
            catch (\Swift_TransportException $e) {
                $retour= $e->getMessage();
            }
            $this->addFlash('success', 'Votre demande à bien été prise en compte, un éducateur vous recontactera');
            // TODO: Envoie d'une notif par mail à l'admin
            return $this->redirectToRoute('booking_index');
        }

        return $this->render('message/new.html.twig', [
            'message' => $message,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="message_show", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function show(Message $message): Response
    {
        $message->setMessageRead(true);
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($message);
        $entityManager->flush();

        return $this->render('message/show.html.twig', [
            'message' => $message,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="message_edit", methods={"GET","POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function edit(Request $request, Message $message): Response
    {
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('message_index');
        }

        return $this->render('message/edit.html.twig', [
            'message' => $message,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="message_delete")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function delete(Request $request, Message $message): Response
    {
        $this->addFlash('warning', 'Le message à bien été supprimé');

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($message);
        $entityManager->flush();
    

        return $this->redirectToRoute('message_index');
    }
}
