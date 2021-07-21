<?php

namespace App\Form;

use App\Entity\Indisponibilite;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IndisponibiliteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('start', DateTimeType::class, [
                'widget' => 'single_text',
                'hours'=> [7,8,9,10,11,12,13,14,15,16,17,18,19,20],
                'minutes'=> [00,10,20,30,40,50],
                'label'=> 'Début',

             
            ])
            ->add('end', DateTimeType::class, [
                'widget' => 'single_text',
                'hours'=> [7,8,9,10,11,12,13,14,15,16,17,18,19,20],
                'minutes'=> [00,10,20,30,40,50],
                'label'=> 'Fin'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Indisponibilite::class,
        ]);
    }
}
