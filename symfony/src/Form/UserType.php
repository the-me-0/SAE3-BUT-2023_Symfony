<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;




class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'constraints' => [
                    new Length([
                        'min' => 1,
                        'minMessage' => 'Le nom doit avoir au moins {{ limit }} caractères',
                        'max' => 120,
                        'maxMessage' => 'Le nom ne doit pas dépasser {{ limit }} caractères'
                    ]),
                ],
            ])
            ->add('lastName', TextType::class, [
                'constraints' => [
                    new Length([
                        'min' => 1,
                        'minMessage' => 'Le nom doit avoir au moins {{ limit }} caractères',
                        'max' => 120,
                        'maxMessage' => 'Le nom ne doit pas dépasser {{ limit }} caractères'
                    ]),
                ],
            ])
            ->add('description', TextareaType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
