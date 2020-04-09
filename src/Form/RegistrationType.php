<?php

namespace App\Form;

use App\Entity\Dog;
use App\Entity\User;
use App\Form\DogType;
use App\Entity\Contract;
use App\Form\ContractType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Votre adresse email'
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe ne sont pas identiques.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Votre mot de passe'],
                'second_options' => ['label' => 'Confirmez votre mot de passe'],
            ])

            // ->add('confirm_password', PasswordType::class, [
            //     'label' => 'Confirmez votre mot de passe'
            // ])
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
            ->add('dog', CollectionType::class, [
                'label' => false,
                'entry_type' => DogType::class,
                // 'entry_options' => ['label' => false],
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true,
                'delete_empty' => true,
            ])
            ->add('contract', CollectionType::class, [
                'label' => false,
                'entry_type' => ContractType::class,
                // 'entry_options' => ['label' => false],
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true,
                'delete_empty' => true,
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
