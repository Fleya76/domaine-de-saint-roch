<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class UpdateUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('email', EmailType::class, [
            'label' => 'Votre adresse email'
        ])

        ->add('firstName', TextType::class, [
            'label' => 'Votre prénom'
        ])
        ->add('lastName', TextType::class, [
            'label' => 'Votre nom'
        ])
        ->add('phone', TextType::class, [
            'label' => 'Votre numéro de téléphone'
        ])
        ->add('address', TextType::class, [
            'label' => 'Votre adresse'
        ])
        ->add('postalCode', IntegerType::class, [
            'label' => 'Votre code postal'
        ])
        ->add('city', TextType::class, [
            'label' => 'Votre ville'
        ])
        ->add('image', FileType::class, [
            'mapped' => false,
            'label' => 'Télécharger une image de votre chien',
            'required' => false,
            // Les champs non mappés ne peuvent pas utiliser les annotations pour les validations
            // dans les entités associées, nous devons donc utiliser des contraintes de classe
            // en utilisant le composant  Symfony\Component\Validator\Constraints\File;
            'constraints' => [
                new File([
                    'maxSize' => '5M',
                    'mimeTypes' => [
                        'image/jpeg'
                    ],
                    'mimeTypesMessage' => 'SVP Uploadez un fichier JPEG valide',
                ])
            ],
        ])
    ;
        
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
