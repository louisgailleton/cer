<?php

namespace App\Form;

use App\Entity\Eleve;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EleveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $champsRequis = '<span class="badge badge-danger badge-pill">REQUIS</span>';
        $builder
            ->add('mail', null, [
                'label' => 'Mail élève ' . $champsRequis,
                'label_html' => true,
                'required' => true
            ])->add('telephone', null, [
                'label' => 'Téléphone élève ' . $champsRequis,
                'label_html' => true,
                'required' => true
            ])
            ->add('mdp', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe ne sont pas identiques',
                'required' => true,
                'first_options' => [
                    'label' => 'Mot de passe ' . $champsRequis,
                    'label_html' => true,
                ],
                'second_options' => [
                    'label' => 'Confirmer le mot de passe ' . $champsRequis,
                    'label_html' => true,
                ],
            ])
            ->add('nom', null, [
                'label' => 'Nom ' . $champsRequis,
                'label_html' => true,
                'required' => true
            ])
            ->add('prenom', null, [
                'label' => 'Prénom ' . $champsRequis,
                'label_html' => true,
                'required' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Eleve::class,
        ]);
    }
}
