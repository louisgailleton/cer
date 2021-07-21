<?php

namespace App\Form;

use App\Entity\Lycee;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LyceeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', null, [
                'label' => "Nom Lycée",
                'required' => true,
            ])
            ->add('adresse', null, [
                'label' => "Adresse Lycée",
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Lycee::class,
        ]);
    }
}
