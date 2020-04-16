<?php

namespace App\Form;

use App\Entity\Video;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class VideoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de la vidéo'
            ])
            ->add('path', FileType::class, [
                'label' => 'Télécharger la vidéo sur le serveur',
                'data_class' => null,
                'constraints' => [
                    new File([
                        'maxSize' => '100M',
                        'mimeTypes' => [
                            'video/mp4'
                        ],
                        'mimeTypesMessage' => 'SVP Uploadez un fichier MP4 valide',
                    ])
                ],
            ])
            ->add('category',EntityType::class,[
                'class'=>Category::class,
                'label' => 'Le type d\'événement',
                'choice_label' => 'title',
                'placeholder' => 'Choix de la catégorie',
            ])
            // ->add('createdAt')
            ->add('content', TextareaType::class, [
                'label' => 'Description de la vidéo'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Video::class,
        ]);
    }
}
