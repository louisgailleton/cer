<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SecretairePjType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('idEleve', HiddenType::class, [
                'required' => true,
            ])
            ->add('commentaireEphoto', TextType::class, [
                'label' => "E-photo",
                'required' => false,
            ])
            ->add('ephoto', ChoiceType::class, [
                'choices'  => [
                    'Oui' => true,
                    'Non' => false,
                ],
                'expanded' => true,
                'multiple' => false,
                'required' => true,
            ])
            ->add('commentaireCNIEleve', TextType::class, [
                'label' => "CNI élève",
                'required' => false,
            ])
            ->add('cni', ChoiceType::class, [
                'choices'  => [
                    'Oui' => true,
                    'Non' => false,
                ],
                'expanded' => true,
                'multiple' => false,
                'required' => true,
            ])
            ->add('commentaireJustifDom', TextType::class, [
                'label' => "Justif dom",
                'required' => false,
            ])
            ->add('justifdom', ChoiceType::class, [
                'choices'  => [
                    'Oui' => true,
                    'Non' => false,
                ],
                'expanded' => true,
                'multiple' => false,
                'required' => true,
            ])
            ->add('commentaireAttestHeb', TextType::class, [
                'label' => "Attestation hébergement",
                'required' => false,
            ])
            ->add('attestheb', ChoiceType::class, [
                'choices'  => [
                    'Oui' => true,
                    'Non' => false,
                ],
                'expanded' => true,
                'multiple' => false,
                'required' => true,
            ])
            ->add('commentaireJDC', TextType::class, [
                'label' => "Attestation JDC",
                'required' => false,
            ])
            ->add('attestjdc', ChoiceType::class, [
                'choices'  => [
                    'Oui' => true,
                    'Non' => false,
                ],
                'expanded' => true,
                'multiple' => false,
                'required' => true,
            ])
            ->add('commentairePermis', TextType::class, [
                'label' => "Autre permis",
                'required' => false,
            ])
            ->add('autrep', ChoiceType::class, [
                'choices'  => [
                    'Oui' => true,
                    'Non' => false,
                ],
                'expanded' => true,
                'multiple' => false,
                'required' => true,
            ])
            ->add('validerANTS', ChoiceType::class, [
                'label' => "Valider par l'ANTS ?",
                'choices'  => [
                    'Oui' => true,
                ],
                'expanded' => true,
                'multiple' => true,
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
        ]);
    }
}
