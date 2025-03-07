<?php

namespace App\Form;

use App\Entity\Games;
use App\Entity\MemoryCard;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MemoryCardType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {$builder
        ->add('images', FileType::class, [
            'label' => 'Sélectionner des images pour le jeu de Memory',
            'mapped' => false, // Ce champ n'est pas mappé à une propriété
            'required' => true,
            'multiple' => true, // Permet de sélectionner plusieurs fichiers
            'attr' => [
                'class' => 'form-control bg-dark',
                'accept' => 'image/*'
            ],
        ])
        ->add('game', EntityType::class, [
            'class' => Games::class,  // Lier à l'entité Games
            'choice_label' => 'name', // Afficher le nom du jeu dans le formulaire
            'placeholder' => 'Choisir un jeu',
            'required' => true,
        ]);
}

public function configureOptions(OptionsResolver $resolver)
{
    $resolver->setDefaults([
        'data_class' => MemoryCard::class,
    ]);
}
}