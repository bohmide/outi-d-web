<?php

namespace App\Form;

use App\Entity\Chapitre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
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
            ]
              ])
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
                    'mapped' => false,
                    'required' => false,
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
