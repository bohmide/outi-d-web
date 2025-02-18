<?php

namespace App\Form;

use App\Entity\Parents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\DateType;


class AddparentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fnp' , TextType::class, [
                'attr' => [
                    'placeholder' => 'Enter the parent\'s first name'
                ],
            ])
            ->add('lnp', TextType::class, [
                'attr' => [
                    'placeholder' => 'Enter the parent\'s Last name'
                ],
            ])
            ->add('emailp' , EmailType::class, [
                'attr' => [
                    'placeholder' => 'Enter the parent\'s email'
                ],
            ])
            ->add('pwp' , PasswordType::class, [
                'attr' => [
                    'placeholder' => 'Enter your password'
                ],
            ])
            ->add('pvp' , PasswordType::class, [
                'attr' => [
                    'placeholder' => 'Confirm your password'
                ],
            ])
            ->add('fnch', TextType::class, [
                'attr' => [
                    'placeholder' => 'Enter the Child\'s first name'
                ],
            ])
            ->add('lnch', TextType::class, [
                'attr' => [
                    'placeholder' => 'Enter the child\'s last name'
                ],
            ])
            ->add('dbch', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('sch' ,ChoiceType::class,[
                'choices' => [
                    'male'=>'Boy',
                    'female'=>'Girl',
                ],
                'expanded'=>true, //pour afficher les boutons radio
                'multiple'=>false,
                'required'=>true,
            ] )
            ->add('ldch',ChoiceType::class, [
                'choices' => [
                    'yes' => 'Yes',
                    'no' => 'No',
                ],
                'expanded' => true, // Pour afficher les boutons radio
                'multiple' => false, // Un seul choix possible
                'required' => true,
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Parents::class,
        ]);
    }
}
