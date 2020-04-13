<?php

namespace App\Form;

use App\Entity\Dog;
use App\Entity\Message;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageType extends AbstractType
{
    // protected $user;
 
    // public function __construct(User $user)
    // {
    //     $this->user = $user;
    // }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // $dog = $options['dog'];
        // dump($options);

        //TODO: Récupérer l'utilisateur connecté
        //TODO: Récupérer les chiens de l'utilisateur.
        $builder
            // ->add('subject')
            // ->add('sendAt')
            // ->add('dog', EntityType::class, [
            //     'class'=> Dog::class,
            //     'label' => 'Rendez-vous pour votre chien',
            //     'choice_label' => 'name',
            //     'placeholder' => 'Nom du chien',
            //     // 'choices' => $this->user->getDog()
            // ])
            ->add('content', TextareaType::class, [
                'label' => "Votre message"
            ])
            // ->add('author')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
    }
}
