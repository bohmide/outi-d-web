<?php

namespace App\Form;

use App\Entity\QuizKids;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuizKidsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('question', TextType::class, [
            'label' => 'Question'
            
        ])
        ->add('options', TextareaType::class, [
            'label' => 'Options (comma separated)',
            'mapped' => false, // Ne mappe pas directement à l'entité
            'required' => true,
        ])
        ->add('correctAnswer', TextType::class, [
            'label' => 'Correct Answer'
        ])
        
        ->add('level', ChoiceType::class, [
            'choices' => [
                'Easy' => 'easy',
                'Medium' => 'medium',
                'Hard' => 'hard',
            ],
            'label' => 'Level'
        ])
        ->add('genre', ChoiceType::class, [
            'choices' => [
                'Culture génerale' => 'Culture génerale',
                'Géographie' => 'Géographie',
                'Sport' => 'Sport',
                'Sur les animaux' => 'Sur les animaux',
                'Scientifique' => 'Scientifique',
                'Dessin-animé' => 'Dessin-animé',
                
            ],
            'label' => 'Genre'
            
        ])
        ->add('mediaFile', FileType::class, [
            'label' => 'Upload Media (image/video)',
            'mapped' => false,
            'required' => true
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => QuizKids::class,
        ]);
    }
}
