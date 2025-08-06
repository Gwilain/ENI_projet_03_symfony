<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', TextType::class, ['label' => 'Pseudo', 'row_attr' => ['class' => 'flexLine']], )
            ->add('campus', EntityType::class, [
    "label" => "Campus",
    'row_attr' => ['class' => 'flexLine'],
    "class" => Campus::class,
    "choice_label" => "name",
    "placeholder" => "Choisissez votre campus",
    'required' => true,
    'constraints' => [
        new Assert\NotNull(message: 'Veuillez sélectionner un campus', groups: ['edit'])
    ]
])
            ->add('firstname', TextType::class, ['label' => 'Prénom', 'required' => false, 'row_attr' => ['class' => 'flexLine']])
            ->add('lastname', TextType::class, ['label' => 'Nom','required' => false, 'row_attr' => ['class' => 'flexLine']])
            ->add('email', EmailType::class, ['label' => 'Email', 'row_attr' => ['class' => 'flexLine']], )
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'required' => false, // pour ne pas forcer le changement à chaque modif
                'first_options'  => [
                    'label' => 'Nouveau mot de passe',
                    'row_attr' => ['class' => 'flexLine'],
                    'attr' => ['autocomplete' => 'new-password'],
                ],
                'second_options' => [
                    'label' => 'Confirmez le mot de passe',
                    'row_attr' => ['class' => 'flexLine'],
                    'attr' => ['autocomplete' => 'new-password'],
                ],
                'invalid_message' => 'Les mots de passe ne correspondent pas.',
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Le mot de passe doit faire au moins {{ limit }} caractères.',
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => ['edit'],  // Ajoutez cette ligne
        ]);
    }
}