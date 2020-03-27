<?php

namespace App\Form;

use App\Entity\Place;
use App\Entity\Booking;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class BookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Le nom de l\'événement',
            ])
            ->add('beginAt', DateTimeType::class, [
                'label' => 'La date de début de votre événement',
                'widget' => 'single_text',
                // 'input'  => 'datetime_immutable',
                // 'attr' => ['class' => 'form-control'],
            ])            
            ->add('endAt', DateTimeType::class, [
                'label' => 'La date de début de votre événement',
                'widget' => 'single_text',
                // 'input'  => 'datetime_immutable'
                // 'attr' => ['class' => 'form-control'],
            ])            
            ->add('category',EntityType::class,[
                'class'=>Category::class,
                'label' => 'Le type d\'événement',
                'choice_label' => 'title',
                'placeholder' => 'Choix de la catégorie',
            ])
            ->add('place',EntityType::class,[
                'class'=>Place::class,
                'label' => 'L\'emplacement de l\'événement',
                'choice_label' => 'title',
                'placeholder' => 'Choix de l\'endroit',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
