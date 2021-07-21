<?php

namespace App\Form\EvalPre;

use App\Entity\EvalPre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EvalPreP1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('permis', ChoiceType::class, [
                'label' => 'Avez vous déjà obtenu un permis',
                'expanded' => 'true',
                'multiple' => 'false',
                'choices' => [
                    'B' => 'B',
                    'AM' => 'AM',
                    'A' => 'A',
                ],
                'required' => 'true'
            ])
            ->add('expConduite', ChoiceType::class, [
                'label' => "Avez vous déjà conduit une voiture",
                'choices' => [
                    '-5h' => '-5',
                    '+5h' => '+5'
                ],
                'expanded' => 'true',
                'required' => 'true'
            ])
            ->add('ou', ChoiceType::class, [
                'label' => "Où ?",
                'choices' => [
                    'chemin' => 'chemin',
                    'route' => 'route',
                    'ville' => 'ville'
                ],
                'expanded' => 'true',
                'multiple' => 'true',
                'required' => 'true'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EvalPre::class,
        ]);
    }
}
