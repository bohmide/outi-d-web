<?php

namespace App\Form;

use App\Entity\Evenements;
use App\Entity\EventGenre;
use App\Entity\Sponsors;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EvenementsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_event')
            ->add('description')
            ->add('date_event', null, [
                'widget' => 'single_text',
            ])
            ->add('nbr_members')
            ->add('image_path')
            ->add('genre', EntityType::class, [
                'class' => EventGenre::class,
                'choice_label' => 'id',
            ])
            ->add('sponsors', EntityType::class, [
                'class' => Sponsors::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evenements::class,
        ]);
    }
}
