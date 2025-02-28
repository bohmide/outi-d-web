<?php

namespace App\Form;

use App\Entity\Chapitre;
use App\Entity\Quiz;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Validator\Constraints as Assert;

class QuizType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        // ->add('chapitre', HiddenType::class, [
        //     'mapped' => false, // Important ! On le traitera manuellement
        // ])

            ->add('titre', TextType::class, [
                'label' => 'Titre du quiz',
                'required' => true, // Rend le champ obligatoire
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez le titre du quiz',
                    ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => [
                    'class' => 'btn btn-primary',
                ],
            ]);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Quiz::class,
        ]);
    }
}
