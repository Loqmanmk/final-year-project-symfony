<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullName', TextType::class, [
                'label' => 'Nom complet',
                'attr' => [
                    'autocomplete' => 'name',
                    'minlength' => 3,
                    'maxlength' => 120,
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => [
                    'autocomplete' => 'email',
                    'maxlength' => 180,
                ],
            ])
            ->add('address', TextareaType::class, [
                'label' => 'Adresse',
                'required' => true,
                'attr' => [
                    'autocomplete' => 'street-address',
                    'minlength' => 5,
                    'rows' => 3,
                ],
            ])
            ->add('phone', TextType::class, [
                'label' => 'Telephone',
                'required' => true,
                'attr' => [
                    'autocomplete' => 'tel',
                    'pattern' => '\+?[0-9\s\-]{8,20}',
                    'title' => 'Exemple: +212600111222',
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'invalid_message' => 'Les mots de passe doivent etre identiques.',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir un mot de passe.']),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caracteres.',
                        'max' => 4096,
                    ]),
                    new Regex([
                        'pattern' => '/^(?=.*[A-Za-z])(?=.*\d).+$/',
                        'message' => 'Le mot de passe doit contenir au moins une lettre et un chiffre.',
                    ]),
                ],
                'first_options' => [
                    'label' => 'Mot de passe',
                    'attr' => [
                        'autocomplete' => 'new-password',
                        'minlength' => 8,
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirmer le mot de passe',
                    'attr' => [
                        'autocomplete' => 'new-password',
                        'minlength' => 8,
                    ],
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
