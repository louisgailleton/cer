<?php

namespace App\Form;

use App\Entity\Prestation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrestationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', null, [
                "label" => "Nom",
                "required" => true
            ])
            ->add('prix', null, [
                "label" => "Prix",
                "required" => true
            ])
            ->add('detail', TextareaType::class, [
                "label" => "Détail",
                "required" => true,
                "help" => "Une ligne par détail"
            ])
            ->add('type', EntityType::class, [
                'class' => 'App\Entity\TypePrestation',
                'required' => true,
                'choice_value' => 'nom',
                'label' => 'Type prestation',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Prestation::class,
        ]);
    }
}
