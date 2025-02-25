<?php

namespace App\Form;

use App\Entity\Parents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType; // Assure-toi de bien importer ce type


class ParentsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('firstName')
            ->add('lastName')
            ->add('password', PasswordType::class)
            
            ->add('password_verif', PasswordType::class, [
               
      
    ])
            ->add('firstName_child', null)
            ->add('lastName_child', null)
            ->add('birthday_child', null, [
                'widget' => 'single_text'
            ])
            ->add('sexe_child', null)
            ->add('learningDifficulties_Child', null)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Parents::class,
        ]);
    }
}
