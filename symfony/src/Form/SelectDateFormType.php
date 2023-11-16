<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;

use DateTime;

class SelectDateFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', DateType::class, [
                'data' => new \DateTime("now"),
                'widget' => 'single_text',
                'html5' => false, // To select only one date
                'attr' => [
                    'class' => 'js-datepicker'
                ],
                'label' => 'Sélectionnez une date'
            ]);
    }
}
