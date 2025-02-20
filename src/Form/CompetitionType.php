<?php

namespace App\Form;

use App\Entity\Competition;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompetitionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom_comp', TextType::class, [
            'label' => 'Nom de la Compétition'
        ])
        ->add('nom_entreprise', TextType::class, [
            'label' => 'Entreprise Organisatrice'
        ])
        ->add('date_debut', DateType::class, [
            'label' => 'Date de Début',
            'widget' => 'single_text',
        ])
        ->add('date_fin', DateType::class, [
            'label' => 'Date de Fin',
            'widget' => 'single_text',
        ])
        ->add('description', TextareaType::class, [
            'label' => 'Description'
        ])
        ->add('fichierFile', FileType::class, [
            'label' => 'Fichier à Télécharger (Image, PDF, Excel)',
            'required' => false, // Rendre le champ obligatoire
            'attr' => [
                'class' => 'file-upload-input', // Classe CSS personnalisée
                'accept' => 'image/*, .pdf, .xlsx, .xls', // Types de fichiers acceptés
                'multiple' => true, // Autoriser ou non plusieurs fichiers
            ],
        ]);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Competition::class,
        ]);
    }
}
