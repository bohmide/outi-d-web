<?php

namespace App\Form;

use App\Entity\Competition;
use App\Entity\Organisation;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CompetitionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom_comp', TextType::class, [
            'label' => 'Nom de la Compétition',
            'attr' => ['class' => 'form-control'],
            'required' => true, // Désactive la validation HTML5
      
        ])
        ->add('nom_entreprise', TextType::class, [
            'label' => 'Entreprise Organisatrice',
            'attr' => ['class' => 'form-control'],
            'required' => true, // Désactive la validation HTML5
        ])
        ->add('date_debut', DateType::class, [
            'label' => 'Date de Début',
            'widget' => 'single_text',
            'required' => true,
        ])
        ->add('date_fin', DateType::class, [
            'label' => 'Date de Fin',
            'widget' => 'single_text',
            'required' => true,
        ])
        ->add('description', TextareaType::class, [
            'label' => 'Description',
            'attr' => ['class' => 'form-control'],
            'required' => true,
        ])
        ->add('fichierFile', FileType::class, [
            'label' => 'Fichier à Télécharger (Image, PDF, Excel)',
             // Ne pas chercher ce champ dans l'entité
            'required' => false, // Symfony ne force pas la saisie, on gère ça dans l'entité
            'attr' => [
                'class' => 'file-upload-input',
                'accept' => 'image/*, .pdf, .xlsx, .xls',
            ],
            'constraints' => [
                new File([
                    'maxSize' => '5M',
                    'mimeTypes' => ['image/jpeg', 'image/png', 'application/pdf'],
                    'mimeTypesMessage' => 'Veuillez uploader un fichier valide (JPG, PNG, PDF).',
                    'maxSizeMessage' => 'Le fichier ne doit pas dépasser 5MB.',
                ]),
            ],
        
        ])
        
        
        ->add('organisation', EntityType::class, [
            'class' => Organisation::class,
            'choice_label' => 'nomOrganisation', 
            'label' => 'Organisation Associée',
            'placeholder' => 'Sélectionnez une organisation',
            'required' => true,
        ]);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Competition::class,
        ]);
    }
}
