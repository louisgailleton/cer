<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\PorteOuverte;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PorteOuverteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $po = $options['po'];
        $builder
            ->add('date', DateType::class, [
                'label' => 'Date',
                'widget' => 'single_text',
                'required' => true

            ])
            ->add('heureDebut', TimeType::class, [
                'label' => "Heure de début",
                'required' => true
            ])
            ->add('heureFin', TimeType::class, [
                'label' => "Heure de fin",
                'required' => true
            ])
            ->add('nbPlace', null, [
                'label' => 'Nombre de place max',
                'required' => true
            ])
            ->add('lieu', EntityType::class, [
                'class' => 'App\Entity\Lieu',
                'label' => 'Lieu',
                'required' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PorteOuverte::class,
            'po' => null
        ]);
    }
}
