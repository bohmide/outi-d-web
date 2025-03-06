<?php

namespace App\Form;

use App\Entity\Question;
use App\Entity\Quiz;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('question')
            ->add('type', ChoiceType::class, [
                'label' => 'Type de question',
                'choices' => [
                    'Choix unique' => 'choix_unique',   // clé = valeur qui sera stockée
                    'Choix multiple' => 'choix_multiple',
                ],
                'expanded' => false,  // false pour une liste déroulante, true pour des boutons radio
                'multiple' => false, // false pour une seule sélection
            ])
                
            ->add('submit', SubmitType::class, [
                'label' => 'ajouter question',
                'attr' => ['class' => 'btn btn-success'],
            ]);        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }
}
