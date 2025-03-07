<?php

namespace App\Form;

use App\Entity\Games;
use App\Entity\Puzzle;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class PuzzleGameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $games = $options['games'];

        $builder
            // Image complète du puzzle (affiché en haut dans la vue)
            ->add('finalImage', FileType::class, [
                'label' => 'Image complète du puzzle',
                'mapped' => false,
                'required' => true,
                
            ])
            // Pièces du puzzle (upload de plusieurs pièces)
            ->add('pieces', CollectionType::class, [
                'mapped' => false,  // We handle this manually in the controller
                'required' => true,
                'entry_type' => FileType::class, // Handle each piece as a file
                'entry_options' => [
                    'label' => false,
                    'required' => true,
                
                ],
                'allow_add' => true,
                'by_reference' => false,  // This makes sure the collection is not bound to the entity directly
            ])
            // Sélection du jeu
            ->add('game', EntityType::class, [
                'class' => Games::class,  // Lier à l'entité Games
                'choice_label' => 'name', // Afficher le nom du jeu dans le formulaire
                'placeholder' => 'Choisir un jeu',
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Puzzle::class,
            'games' => [],  // L'option 'games' pour passer les jeux au formulaire
        ]);
    }
}