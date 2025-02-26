<?php

namespace App\Form;

use App\Entity\Parents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ParentsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class)
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('password', PasswordType::class)
            ->add('password_verif', PasswordType::class)

            ->add('firstName_child', TextType::class)
            ->add('lastName_child', TextType::class)

            // For DateType, widget option is allowed
            ->add('birthday_child', DateType::class, [
                'widget' => 'single_text',
                'html5' => true,
            ])

            // For choices like gender, using ChoiceType is recommended
            ->add('sexe_child', ChoiceType::class, [
                'choices' => [
                    'Male' => 'male',
                    'Female' => 'female',
                ],
                'expanded' => true, // Radio buttons
                'multiple' => false,
            ])

            ->add('learningDifficulties_Child', ChoiceType::class, [
                'choices' => [
                    'oui' => 'oui',
                    'non' => 'non',
                ],
                'expanded' => true, // Radio buttons
                'multiple' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Parents::class,
        ]);
    }
}