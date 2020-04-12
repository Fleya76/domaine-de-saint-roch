<?php

namespace App\EventSubscriber;

use App\Events\RegistrationEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Util\Fonctions;
class RegistrationSubscriber implements EventSubscriberInterface
{
    private $mailer;
    private $urlGenerator;

    //Récupération d'un objet de type Swift_Mailer par injection de dépendance
    //Récupération d'un objet de type UrlGeneratorInterface par injection de dépendance
    //au moment de l'instanciation du Subscriber (abonné)
    public function __construct( \Swift_Mailer $mailer, UrlGeneratorInterface $urlGenerator)
    {
        $this->mailer = $mailer;
        $this->urlGenerator = $urlGenerator;
    }

    public static function getSubscribedEvents()
    {

        return [
            RegistrationEvent::class => 'onCreated',
          ];
    }
    public function onCreated(RegistrationEvent $event)
    {
        $mail_admin=Fonctions::getEnv('mail_admin');
        //Récupération des infos de l'objet comment à partir de l'objet event
        /////////////////////AJOUT LIENS////////////////////////////
        $href1 = $this->urlGenerator->generate('user_validation_id', [
            'id' => $event->getUser()->getId()
        ], UrlGeneratorInterface::ABSOLUTE_URL);
        $href2 = $this->urlGenerator->generate('user_delete', [
            'id' => $event->getUser()->getId(),
            'redirection' => 'user_index'
        ], UrlGeneratorInterface::ABSOLUTE_URL);
        $lien='<br><a href="'.$href1.'">
                    Validation du compte
                </a>
                <br><a href="'.$href2.'">
                    Suppression du compte
                </a>';
        ///////////////////////////////////////////////////////////////
        
        //Envoi du message
        $message = new \Swift_Message('Un utilisateur est en attente de validation');
        $message ->setFrom($mail_admin);
        $message ->setTo(['admin.blog@email.fr'=> 'admin']);
        $message ->setBody("
                    <br><br>
                    ".$lien."
                    ",
                'text/html'
        );
        try {
            $retour=$this->mailer->send($message);
        }
        catch (\Swift_TransportException $e) {
            $retour= $e->getMessage();
        }
    }

}
