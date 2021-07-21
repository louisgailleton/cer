<?php

namespace App\Form;

use App\Entity\Piecesjointes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class PiecesJointesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('typePJ', HiddenType::class, [
                'required' => true
            ])
            ->add('nomFichier', FileType::class, [
                'label' => 'Fichier',
                'mapped' => false,
                'required' => true,
                'help' => "Les fichiers doivent être de type 'pdf', 'png' ou 'jpg' et ne pas dépasser 5Mo.",
                'constraints' => [
                    new File([
                        'maxSize' => '5000k',
                        'mimeTypes' => [
                            'application/pdf',
                            'image/jpeg',
                            'image/png'
                        ],
                        'mimeTypesMessage' => 'Le fichier doit-être de type pdf, jpd ou png',
                    ])
                ],

            ])
            ->add('eleve', HiddenType::class, [
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Piecesjointes::class,
        ]);
    }
}
