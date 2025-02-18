<?php

namespace App\Form;

use App\Entity\Cours;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Certification;

class CoursType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom', TextType::class, [
            'label' => 'Nom du cours',
            'required' => true,
        ])
        // ->add('fichier', FileType::class, [
        //     'label' => 'Fichier du cours (PDF, DOCX, etc.)',
        //     'mapped' => false, // Pas relié directement à l'entité Cours
        //     'required' => true,
        //     'constraints' => [
        //         new File([
        //             'maxSize' => '5M',
        //             'mimeTypes' => ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
        //             'mimeTypesMessage' => 'Veuillez uploader un fichier PDF ou DOCX valide.',
        //         ])
        //     ],
        ->add('dateCreation', DateType::class, [
            'widget' => 'single_text',
            'label' => 'Date de création',
            'required' => true,
            'disabled' => true, // L'utilisateur ne peut pas modifier 
        ])
        // ->add('is_certifie', CheckboxType::class, [
        //     'label'    => 'Ce cours est certifié ?',
        // 'required' => false,
        // 'mapped'   => false, // Ne modifie pas directement l'entité
        // 'data'     => $options['data']->isCertifie(), // Affiche l'état actuel 
        // ])

        // ->add('certification', ChoiceType::class, [
        //     'choices' => [
        //         'Certification A' => 'Certification A',
        //         'Certification B' => 'Certification B',
        //         'Certification C' => 'Certification C',
        //     ],
        //     'placeholder' => 'Choisir une certification (optionnel)',
        //     'required' => false,
        // ])

        ->add('certification', EntityType::class, [
            'class' => Certification::class,
            'choice_label' => 'nom_certification', // Assurez-vous que 'nom' est un champ valide de l'entité Certification
            'placeholder' => 'Choisir une certification (optionnel)',
            'required' => false,
        ])
        ->add('etat', ChoiceType::class, [
            'label' => 'Niveau de difficulté',
            'choices' => [
                'Facile' => 'Facile',
                'Moyen' => 'Moyen',
                'Avancé' => 'Avancé',
            ],
            'placeholder' => 'Choisir un niveau de difficulté', // Optionnel
            'required' => true, // Ou false si vous voulez que ce soit optionnel
        ])
        ->add('submit', SubmitType::class, [
            'label' => 'Déposer le cours',
            'attr' => ['class' => 'btn btn-success'],
        ]);
}

public function configureOptions(OptionsResolver $resolver): void
{
    $resolver->setDefaults([
        'data_class' => Cours::class,
    ]);
}
}
