<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Message;
use App\Data\SearchData;
use App\Form\SearchType;
use App\Form\MessageType;
use App\Form\UpdateUserType;
use App\Service\FileUploader;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
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
     * @Route("/", name="user_index")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function index(UserRepository $userRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $data = new SearchData();
        $data->page = $request->get('page', 1);
        $form = $this->createForm(SearchType::class, $data);
        
        $form->handleRequest($request);
        $users = $userRepository->findSearch($data);

        return $this->render('user/overview.html.twig', [
            // 'users' => array_reverse($userRepository->findBy(['validation' => '1'])),
            'users' => $users,
            'dateTime' => new DateTime(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/validation", name="user_validation")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function validationView(UserRepository $userRepository)
    {    
        // TODO : Ajouter une pagination

        
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

        $href = $this->generateUrl('video_index', [
          ], UrlGeneratorInterface::ABSOLUTE_URL);
  
        $lien='<br><a href="'.$href.'">Domaine de Saint-Roch</a>';
        //Envoi du message
        $message = new \Swift_Message('Jimmy GRESSENT à validé votre profil');
        $message->setFrom('admin.blog@email.fr');
        $message->setTo([$user->getEmail() => 'Utilisateur']);
        $message->setBody("
            <h1>Bonjour " . $user . "</h1>
            Vous pouvez dès maintenant consulter l'ensemble des cours proposé par le Domaine de Saint-Roch.
            <br>
            <br>Lien vers les vidéos : ". $lien ."
            
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
     * @Security("user===userConnect or is_granted('ROLE_ADMIN')", statusCode=499,message="Vous devez être connecté et être propriétaire du profil pour le modifier ou supprimer.")
     */
    public function edit(Request $request, User $userConnect, FileUploader $fileUploader): Response
    {
        $form = $this->createForm(UpdateUserType::class, $userConnect);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $file=$form['image']->getData();           
            if($file){
                $res=$fileUploader->upload($file);
                if(is_string($res)){
                    $userConnect->setImage($res);
                }else{
                    $message=$res->getMessage();
                    $userConnect->setImage('');
                }
            }else{
                $userConnect->setImage(''); 
            }

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', $userConnect->getFirstName() . ' ' . $userConnect->getLastName() . ' à bien été modifié.');
            return $this->redirectToRoute('user_show', [
                'id' => $userConnect->getId()
            ]);
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
}
