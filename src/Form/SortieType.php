<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('dateHeureDebut', null, [
                'widget' => 'single_text',
            ])
            ->add('dateLimiteInscription', null, [
                'widget' => 'single_text',
            ])
            ->add('duree')
            ->add('nbInscriptionMax')
            ->add('infosSortie')
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'choice_label' => 'id',
            ])
            ->add('Etat', EntityType::class, [
                'class' => Etat::class,
                'choice_label' => 'id',
            ])
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'id',
            ])
            ->add('organisateur', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('participants', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
