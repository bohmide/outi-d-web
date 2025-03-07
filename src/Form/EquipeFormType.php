<?php

namespace App\Form;

use App\Entity\Competition;
use App\Entity\Equipe;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class EquipeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nomEquipe', TextType::class, [
            'label' => 'Nom de l\'équipe',
            'attr' => ['class' => 'form-control', 'placeholder' => 'Entrez le nom de l\'équipe']
        ])
        ->add('ambassadeur', TextType::class, [
            'label' => 'Nom de l\'ambassadeur',
            'attr' => ['class' => 'form-control', 'placeholder' => 'Entrez votre nom']
        ])
        ->add('membres', CollectionType::class, [
            'label' => false,
            'entry_type' => EmailType::class, // Chaque membre est un champ e-mail
            'entry_options' => [
                'attr' => ['class' => 'form-control ', 'placeholder' => 'E-mail du membre'],
            ],
            'allow_add' => true, // Permet d'ajouter des champs dynamiquement
            'allow_delete' => true, // Permet de supprimer des champs dynamiquement
            'prototype' => true, // Nécessaire pour JavaScript
            'by_reference' => false, // Important pour que les modifications soient prises en compte
            'error_bubbling' => false, // Assure que les erreurs sont attachées au champ lui-même
        ]);
       
       

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Equipe::class,
        ]);
    }
}
