<?php

namespace App\Form;

use App\Entity\Chapitre;
use App\Entity\Cours;
use App\Entity\Quiz;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ChapitreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_chapitre')
            ->add('contenu', null, [
                'label' => 'Titre du fichier',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez un titre pour le fichier...'
            ]])
            ->add('contenuText', TextareaType::class, [
                'required' => false,
                'label' => ' du chapitre',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 8, // Permet d'afficher un grand champ de texte
                    'placeholder' => 'Entrez le contenu du format text ici...'
                ]
                ])
            ->add('file', FileType::class, [
            'label' => 'Fichier ',
            'mapped' => false, // Ne pas mapper directement sur l'entité
            'required' => false,
            'constraints' => [
                new File([
                    'maxSize' => '5M',
                    'mimeTypes' => [
        'application/pdf', // PDF
        'application/msword', // DOC
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // DOCX
        'application/vnd.ms-powerpoint', // PPT
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' // PPTX
                    ],
                    'mimeTypesMessage' => 'Veuillez uploader un  fichier PDF, DOC, DOCX, PPT ou PPTX valide.',
                ])
            ],
        ])
            ->add('submit', SubmitType::class, [
                'label' => 'ajouter chapitre',
                'attr' => ['class' => 'btn btn-success'],
            ]);

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Chapitre::class,
        ]);
    }
}
