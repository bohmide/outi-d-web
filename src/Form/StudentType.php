<?php

namespace App\Form;

use App\Entity\Student;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\PasswordHasher\Type\PasswordTypePasswordHasherExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType; // Assure-toi de bien importer ce type
use Symfony\Component\Validator\Constraints\EqualTo;

class StudentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('firstName')
            ->add('lastName')
            ->add('password', PasswordType::class)
            ->add('password_verif', PasswordType::class)
            
            ->add('datebirth', null, [
                'widget' => 'single_text'
            ])
            ->add('sexe', ChoiceType::class, [
                'choices' => [
                    'Homme' => 'Homme',
                    'Femme' => 'Femme',
                ],
                'expanded' => true, // Use radio buttons
                'multiple' => false, // Only one option can be selected
            ]);
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Student::class,
        ]);
    }
}
