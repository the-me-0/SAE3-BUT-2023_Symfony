<?php

namespace App\Form;

use App\Entity\Objective;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;


class ObjectiveFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        // Build the form with each field and its constraints :
            // - temperature : between -50 and 50
            // - humidity : between 0 and 100
            // - eCO2 : between 0 and 10000
        // each constraints has a message to display if the value is not in the range
        // each one have its gap field

        $builder
            ->add('temperature', IntegerType::class, [
                'constraints' => [
                    new Range([
                        'min' => -50,
                        'max' => 50,
                        'minMessage' => 'La valeur doit être supérieure à -50.',
                        'maxMessage' => 'La valeur doit être inférieure à 50.'
                    ]),
                ],
            ])
            ->add('gapTemperature', IntegerType::class, [
                'constraints' => [
                    new Range([
                        'min' => 0,
                        'max' => 100,
                        'minMessage' => 'La valeur doit être supérieure à 0.',
                        'maxMessage' => 'La valeur doit être inférieure à 100.'
                    ]),
                ],
            ])
            ->add('humidity', IntegerType::class, [
                'constraints' => [
                    new Range([
                        'min' => 0,
                        'max' => 100,
                        'minMessage' => 'La valeur doit être supérieure à 0.',
                        'maxMessage' => 'La valeur doit être inférieure à 100.'
                    ]),
                ],
            ])
            ->add('gapHumidity', IntegerType::class, [
                'constraints' => [
                    new Range([
                        'min' => 0,
                        'max' => 100,
                        'minMessage' => 'La valeur doit être supérieure à 0.',
                        'maxMessage' => 'La valeur doit être inférieure à 100.'
                    ]),
                ],
            ])
            ->add('eCO2', IntegerType::class, [
                'constraints' => [
                    new Range([
                        'min' => 0,
                        'max' => 10000,
                        'minMessage' => 'La valeur doit être supérieure à 0.',
                        'maxMessage' => 'La valeur doit être inférieure à 10000.'
                    ]),
                ],
            ])
            ->add('gapECO2', IntegerType::class, [
                'constraints' => [
                    new Range([
                        'min' => 0,
                        'max' => 100,
                        'minMessage' => 'La valeur doit être supérieure à 0.',
                        'maxMessage' => 'La valeur doit être inférieure à 100.'
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Objective::class,
        ]);
    }
}
