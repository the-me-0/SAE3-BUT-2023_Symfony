<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class PasswordUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('old_password', PasswordType::class, [
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer votre mot de passe actuel',
                    ]),
                ],
            ])
            ->add('new_password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options'  => [
                    'label' => false,
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez entrer votre nouveau mot de passe ',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                    ],
                ],
                'second_options' => [
                    'label' => false,
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez confirmer votre nouveau mot de passe',
                        ]),
                    ],
                ],
                'invalid_message' => 'Les mots de passe doivent être identiques',
            ])
        ;
    }
}
