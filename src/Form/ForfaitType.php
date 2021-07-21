<?php

namespace App\Form;

use App\Entity\Forfait;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ForfaitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('libelleforfait', null, [
                'label' => "Nom du forfait",
                'required' => true,
            ])
            ->add('prix', null, [
                'label' => "Prix du forfait",
                'required' => true,
            ])
            ->add('contenuForfait', TextareaType::class, [
                'label' => "Contenu du forfait",
                'required' => true,
                'help' => "Une ligne par contenu",
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Forfait::class,
        ]);
    }
}
