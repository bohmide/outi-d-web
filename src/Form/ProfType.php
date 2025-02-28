<?php

namespace App\Form;

use App\Entity\Prof;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\DateType;


class ProfType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fnpr', TextType::class, [
                'attr' => [
                    'placeholder' => 'Enter the professor\'s first name'
                ],
            ])
            ->add('lnpr', TextType::class, [
                'attr' => [
                    'placeholder' => 'Enter the professor\'s last name'
                ],
            ])
            ->add('emailpr', EmailType::class, [
                'attr' => [
                    'placeholder' => 'Enter the professor\'s email'
                ],
            ])
            ->add('pwpr', PasswordType::class, [
                'attr' => [
                    'placeholder' => 'Enter your password'
                ],
            ])
            ->add('pvpr', PasswordType::class, [
                'attr' => [
                    'placeholder' => 'Confirm your password'
                ],
            ])
            ->add('interdate', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('intermode',ChoiceType::class, [
                'choices' => [
                    'online' => 'Online',
                    'face to face' => 'Face to face',
                ],
                'expanded' => true, // Pour afficher les boutons radio
                'multiple' => false, // Un seul choix possible
                'required' => true,
            ]);  
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Prof::class,
        ]);
    }
}
