<?php

namespace App\Form;

use App\Entity\Prof;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType; // Assure-toi de bien importer ce type


class ProfType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('firstName')
            ->add('lastName')
            ->add('password', PasswordType::class, [
                'mapped' => true,  // Ce champ est mappé à l'entité Prof
                'required' => true
            ])
            ->add('password_verif', PasswordType::class, [
                'mapped' => false,
                'required' => true,
                
            ])
            
            ->add('interdate', null, [
                'widget' => 'single_text'
            ])
            ->add('intermode', ChoiceType::class, [
                'choices' => [
                    'enligne' => 'enligne',
                    'présentiel' => 'présentiel',
                ],
                'expanded' => true, // Use radio buttons
                'multiple' => false, // Only one option can be selected
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Prof::class,
        ]);
    }
    
}
