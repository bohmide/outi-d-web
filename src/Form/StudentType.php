<?php

namespace App\Form;

use App\Entity\Student;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\DateType;


class AddstudentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fn' , TextType::class, [
                'attr' => [
                    'placeholder' => 'Enter the Student\'s first name'
                ],
            ])
            ->add('ln' , TextType::class, [
                'attr' => [
                    'placeholder' => 'Enter the Student\'s first name'
                ],
            ])
            ->add('email' , EmailType::class, [
                'attr' => [
                    'placeholder' => 'Enter the Student\'s email'
                ],
            ])
            ->add('pw' , PasswordType::class, [
                'attr' => [
                    'placeholder' => 'Enter your password'
                ],
            ])
            ->add('pv' , PasswordType::class, [
                'attr' => [
                    'placeholder' => 'Confirm your password'
                ],
            ])
            ->add('datebirth', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('sexe' ,ChoiceType::class,[
                'choices' => [
                    'male'=>'Girl',
                    'female'=>'Boy',
                ],
                'expanded'=>true, //pour afficher les boutons radio
                'multiple'=>false,
                'required'=>true,
            ] )
            ->add('ld',ChoiceType::class, [
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
            'data_class' => Student::class,
        ]);
    }
}
