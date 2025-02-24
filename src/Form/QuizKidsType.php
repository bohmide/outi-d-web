<?php

namespace App\Form;

use App\Entity\QuizKids;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class QuizKidsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('question', TextType::class, [
            'label' => 'Question',
            'required' => true,
            
        ])
        ->add('options', CollectionType::class, [
            'entry_type' => TextType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'prototype' => true,
            'by_reference' => false,
            'label' => false,
            'attr' => ['class' => 'options-collection'],
            'entry_options' => ['attr' => ['class' => 'form-control']],
            'required' => true,
            
        ])
        ->add('correctAnswer', TextType::class, [
            'label' => 'Correct Answer',
            'required' => true,
        ])
        
        ->add('level', ChoiceType::class, [
            'choices' => [
                'Easy' => 'easy',
                'Medium' => 'medium',
                'Hard' => 'hard',
            ],
            'label' => 'Level',
            'required' => true,
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
            'label' => 'Genre',
            'required' => true,
            
        ])
        ->add('mediaFile', FileType::class, [
            'label' => 'Upload Media (image/video)',
            'mapped' => false,
            'required' => false
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => QuizKids::class,
        ]);
    }
}
