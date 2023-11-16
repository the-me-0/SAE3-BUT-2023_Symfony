<?php

namespace App\Form;

use App\Entity\Room;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType as TypeCheckboxType;
use App\Entity\User;

class RoomType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('floor', IntegerType::class, [
                'constraints' => [
                    new Range([
                        'min' => -10,
                        'max' => 300,
                        'minMessage' => 'La valeur doit être supérieure à -10.',
                        'maxMessage' => 'La valeur doit être inférieure à 300.'
                    ]),
                ]
            ])
            ->add('surface', IntegerType::class, [
                'constraints' => [
                    new Range([
                        'min' => 1,
                        'max' => 100,
                        'minMessage' => 'La valeur doit être supérieure à 1.',
                        'maxMessage' => 'La valeur doit être inférieure à 300.'
                    ]),
                ]
            ])
            ->add('nb_windows', IntegerType::class, [
                'constraints' => [
                    new Range([
                        'min' => -10,
                        'max' => 300,
                        'minMessage' => 'La valeur doit être supérieure à 0.',
                        'maxMessage' => 'La valeur doit être inférieure à 100.'
                    ]),
                ]
            ])
            ->add('name', TextType::class, [
                'constraints' => [
                    new Length([
                        'min' => 1,
                        'minMessage' => 'Le nom doit avoir au moins {{ limit }} caractères',
                        'max' => 120,
                        'maxMessage' => 'Le nom ne doit pas dépasser {{ limit }} caractères'
                    ]),
                ],
            ])
            ->add('private', TypeCheckboxType::class, [
                'required' => false,
            ])
            ->add('owner', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'lastName',
                'multiple' => true,
            ])
            //->add('facility') --> Not included. Auto managed by the manager
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Room::class,
        ]);
    }
}
