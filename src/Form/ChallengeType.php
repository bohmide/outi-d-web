<?php

namespace App\Form;

use App\Entity\Challenge;
use App\Entity\QuizKids;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChallengeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du Challenge',
                'attr' => ['class' => 'form-control mb-4']
            ])
            ->add('quizzes', EntityType::class, [
                'class' => QuizKids::class,    // L'entité à récupérer
                'choice_label' => 'question', // Ce qui sera affiché dans le choix
                'multiple' => true,           // Permet de sélectionner plusieurs quiz
                'expanded' => true,           // Affiche des cases à cocher
                'by_reference' => false,      // Indique à Doctrine de gérer correctement la relation
                'label' => 'Sélectionner les QuizKids',
            ]);
    }

    private function getQuizKidsChoices(array $quizKids): array
    {
        $choices = [];
        foreach ($quizKids as $quiz) {
            $choices[$quiz->getQuestion()] = $quiz->getId(); // Utiliser le titre de la question pour l'affichage
        }
        return $choices;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Challenge::class,
            'quizKids' => [], // Liste des QuizKids à passer dans le formulaire
        ]);
    }
}