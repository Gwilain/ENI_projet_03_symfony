<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\User;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add("name",TextType::class, ["label"=>'Nom de la sortie','row_attr' => ['class' => 'flexLine'],])
            ->add('dateHeureDebut', DateTimeType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'row_attr' => ['class' => 'flexLine'],
            ])
            ->add('dateLimiteInscription', DateTimeType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'row_attr' => ['class' => 'flexLine'],
            ])

            ->add('nbInscriptionMax', IntegerType::class, [
                'label' => 'Nombre de places :',
                'row_attr' => ['class' => 'flexLine'],
                'data' => 2,
            ] )
            ->add('duree', TimeType::class, [
                'label' => 'Durée (en min.) :',
                'row_attr' => ['class' => 'flexLine'],
                'placeholder'=>"00"
            ])



            ->add('infosSortie')
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'choice_label' => 'name',
            ])
            ->add('Etat', EntityType::class, [
                'class' => Etat::class,
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
