<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', TextType::class, ['label' => 'Pseudo :', 'row_attr' => ['class' => 'flexLine']], )
            ->add('campus', EntityType::class, [
    "label" => "Campus :",
    'row_attr' => ['class' => 'flexLine'],
    "class" => Campus::class,
    "choice_label" => "name",
    "placeholder" => "Choisissez votre campus",
    'required' => true,
    'constraints' => [
        new Assert\NotNull(message: 'Veuillez sélectionner un campus', groups: ['edit'])
    ]
])
            ->add('firstname', TextType::class, ['label' => 'Prénom :', 'required' => false, 'row_attr' => ['class' => 'flexLine']])
            ->add('lastname', TextType::class, ['label' => 'Nom :','required' => false, 'row_attr' => ['class' => 'flexLine']])
            ->add('profilePicture', FileType::class, [
                'row_attr' => ['class' => 'flexLine'],
                'label' => 'Photo de profil :',
                'mapped' => false, // Important : pas lié directement à la propriété
                'required' => false,

                'constraints' => [
                    new File([
                        'maxSize' => '4096k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Merci d\'uploader une image valide (JPEG/PNG)',
                    ])
                ],
            ])
            ->add('removePhoto', CheckboxType::class, [
                'row_attr' => ['class' => 'flexLine'],
                'label' => 'Supprimer la photo actuelle :',
                'required' => false,
                'mapped' => false,
            ])->add('phoneNumber', TextType::class, [
                'row_attr' => ['class' => 'flexLine'],
                'label' => 'Téléphone',
                'required' => false,  // si le téléphone est optionnel
                'attr' => [
                    'placeholder' => '09 69 39 40 20',
                    'pattern' => '^\+?[0-9\s\-]{6,15}$' // optionnel : simple regex HTML5 pour le téléphone
                ],
            ])
            ->add('email', EmailType::class, ['label' => 'Email :', 'row_attr' => ['class' => 'flexLine']], )
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'required' => $options['is_new'],
                'first_options'  => [
                    'label' => $options['is_new'] ? 'Mot de passe' : 'Nouveau mot de passe',
                    'row_attr' => ['class' => 'flexLine'],
                    'attr' => ['autocomplete' => 'new-password'],
                ],
                'second_options' => [
                    'label' => 'Confirmez le mot de passe',
                    'row_attr' => ['class' => 'flexLine'],
                    'attr' => ['autocomplete' => 'new-password'],
                ],
                'invalid_message' => 'Les mots de passe ne correspondent pas.',
                'constraints' => $options['is_new'] ? [
                    new NotBlank(['message' => 'Le mot de passe est obligatoire.']),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Le mot de passe doit faire au moins {{ limit }} caractères.',
                        'max' => 4096,
                    ]),
                ] : [
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Le mot de passe doit faire au moins {{ limit }} caractères.',
                        'max' => 4096,
                    ]),
                ],
            ]);


        if ($options['is_admin']) {
            $builder->add('active', CheckboxType::class, [
                'row_attr' => ['class' => 'flexLine'],
                'label' => 'Compte actif',
                'required' => false,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'is_new' => false,
            'validation_groups' => ['edit'],
            'is_admin' => false,
        ]);
    }
}