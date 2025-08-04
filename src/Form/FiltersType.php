<?php

namespace App\Form;

use App\Entity\Campus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->setMethod('GET')
            ->add('campus', EntityType::class, [
                "label"=>"Campus",
//                'row_attr' => ['class' => 'flexLine'],
                "class"=>Campus::class,
                "choice_label"=>"name",
                "placeholder"=>"Choisissez votre campus"
            ])
            ->add('search', TextType::class, [
                'required' => false,
                'label' => 'Recherche',
                'attr' => ['placeholder' => 'Titre contient...']
            ])

            ->add('dateDebut', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
                'label' => 'Entre'
            ])
            ->add('dateFin', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
                'label' => 'et'
            ])
            ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
