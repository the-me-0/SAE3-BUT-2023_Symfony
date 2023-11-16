<?php

namespace App\Form;

use App\Entity\Sensor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class SensorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('num')
            ->add('description', TextareaType::class, ['required' => false])
            ->add('tag')
            // ->add('room') --> now auto managed by the Sensor manager
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sensor::class,
        ]);
    }
}
