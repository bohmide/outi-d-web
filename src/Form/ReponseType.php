<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;


class ReponseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
             ->add('reponse', TextType::class, [
            'label' => 'Réponse',
            'attr' => ['class' => 'form-control']
            ])
            ->add('isCorrect', CheckboxType::class, [
                'label'    => 'Réponse correcte',
                'required' => false,
            ])
        
            ->add('submit', SubmitType::class, [
                'label' => 'ajouter reponse',
                'attr' => ['class' => 'btn btn-success'],
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
