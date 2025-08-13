<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Ville;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CampusType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $readonlyAttr = $options['editable'] ? [] : ['readonly' => 'readonly'];

        $builder
            ->add('name', TextType::class, [
                'label' => false,
                'attr' => array_merge(['class' => 'city-name'], $readonlyAttr),
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Campus::class,
            'editable' => false,
        ]);
    }
}
