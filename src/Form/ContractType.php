<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Contract;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class ContractType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('beginAt', DateType::class, [
                'label' => 'La date de début de votre contrat',
                'widget' => 'single_text',
            ])        
            // ->add('endAt', DateType::class, [
            //     'label' => 'La date de début de votre contrat',
            //     'widget' => 'single_text',
            // ])   
            // ->add('user',EntityType::class,[
            //     'class'=>User::class,
            //     'choice_label' => 'title',
            //     'placeholder' => 'Choix de l\'utilisateur',
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contract::class,
        ]);
    }
}
