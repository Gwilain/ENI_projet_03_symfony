<?php

namespace App\Form;

use App\Entity\Campus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
            ])->add('sortiesQue', ChoiceType::class, [
                'label' => false,
                'required' => false,
                'expanded' => true,
                'multiple' => true,
                'choices' => [
                    "Sorties que j'organise" => 'organise',
                    "Sorties auxquelles je suis inscrit/e" => 'inscrit',
                    "Sorties auxquelles je ne suis pas inscrit/e" => 'pasInscrit',
                    "Sorties terminées" => 'terminee'
                ],
                'attr' => [
                    'class' => 'checkbox-inline'
                ]
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
