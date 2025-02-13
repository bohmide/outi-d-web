<?php

namespace App\Form;

use App\Entity\Evenements;
use App\Entity\EventGenre;
use App\Entity\Sponsors;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints\NotBlank;

class EvenementsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_event')
            ->add('description')
            // ->add('date_event', null, [
            //     'widget' => 'single_text',
            //     'constraints' => [
            //         new NotBlank([
            //             'message' => 'Please select a date.',
            //         ]),
            //     ],
            // ])
            ->add('date_event', DateType::class, [
                'widget' => 'single_text',
                'data' => new \DateTime(),
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please select a date.',
                    ]),
                ],
                'required' => true,  // Ensure this is required
            ])
            ->add('nbr_members')
            ->add('genre', EntityType::class, [
                'class' => EventGenre::class,
                'choice_label' => 'nom_genre',
            ])
            ->add('sponsors', EntityType::class, [
                'class' => Sponsors::class,
                'choice_label' => 'nom_sponsor',
                'multiple' => true,
            ])
            ->add('image_file', FileType::class, [
                'label' => 'Upload Image',
                'mapped' => false, // This is important! It tells Symfony not to map this field to the entity directly.
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
                        'mimeTypesMessage' => 'Please upload a valid image (JPEG, PNG, WEBP)',
                    ])
                ],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evenements::class,
        ]);
    }
}
