<?php

namespace App\Form;

use App\Entity\Facility;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;



class FacilityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Le nom doit avoir au moins {{ limit }} caractères',
                        'max' => 120,
                        'maxMessage' => 'Le nom ne doit pas dépasser {{ limit }} caractères'
                    ]),
                ],
            ])
            ->add('sector', ChoiceType::class, [
                'choices' => [
                    'IUT' => 'IUT',
                    'Université' => 'Université',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Facility::class,
        ]);
    }
}
