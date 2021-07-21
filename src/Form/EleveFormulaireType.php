<?php

namespace App\Form;

use App\Entity\Eleve;
use App\Entity\StatutSocial;
use App\Entity\Forfait;
use Doctrine\DBAL\Types\DateType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EleveFormulaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $eleve = $options['eleve'];
        $champsRequis = '<span class="badge badge-danger badge-pill">REQUIS</span>';
        $builder
            ->add('prenom', null, [
                'required' => true,
                'label' => 'Prénom ' . $champsRequis,
                'label_html' => true,
                'data' => $eleve->getPrenom()
            ])
            ->add('autrePrenoms', null, [
                'required' => false,
                'label' => 'Autre prénoms',
                'data' => $eleve->getAutrePrenoms()
            ])
            ->add('nom', null, [
                'required' => true,
                'label' => 'Nom ' . $champsRequis,
                'label_html' => true,
                'data' => $eleve->getNom()
            ])
            ->add('nomUsage', null, [
                'required' => false,
                'label' => 'Nom d\'usage',
                'data' => $eleve->getNomUsage()
            ])
            ->add('dateNaiss', null, [
                'required' => true,
                'label' => 'Date de naissance ' . $champsRequis,
                'label_html' => true,
                'widget' => 'single_text',
                'data' => $eleve->getDateNaiss()
            ])
            ->add('telephone', null, [
                'required' => true,
                'label' => 'Téléphone ' . $champsRequis,
                'label_html' => true,
            ])
            ->add('telParent', null, [
                'required' => false,
                'label' => 'Téléphone parent'
            ])
            ->add('mailParent', null, [
                'required' => false,
                'label' => 'Mail parent'
            ])
            ->add('adresse', null, [
                'required' => true,
                'label' => 'Adresse ' . $champsRequis,
                'label_html' => true,
            ])
            ->add('ville', null, [
                'required' => true,
                'label' => 'Ville ' . $champsRequis,
                'label_html' => true,
            ])
            ->add('cp', null, [
                'required' => true,
                'label' => 'Code postal ' . $champsRequis,
                'label_html' => true,
            ])
            ->add('paysNaiss', null, [
                'required' => true,
                'label' => 'Pays de naissance ' . $champsRequis,
                'label_html' => true,
            ])
            ->add('depNaiss', null, [
                'required' => true,
                'label' => 'Département de naissance ' . $champsRequis,
                'label_html' => true,
            ])
            ->add('villeNaiss', null, [
                'required' => true,
                'label' => 'Commune de naissance ' . $champsRequis,
                'label_html' => true,
            ])
            ->add('lunette', ChoiceType::class, [
                'label' => 'Portez-vous des lunettes ? ' . $champsRequis,
                'label_html' => true,
                'required' => true,
                'choices' => Eleve::LUNETTE
            ])
            ->add('statutSocial', EntityType::class, [
                'class' => 'App\Entity\StatutSocial',
                'label' => 'Statut social ' . $champsRequis,
                'label_html' => true,
                'required' => true
            ])
            ->add('lycee', EntityType::class, [
                'class' => 'App\Entity\Lycee',
                'required' => false,
                'label' => 'Lycée ' . $champsRequis,
                'label_html' => true,
                'placeholder' => 'Choississez votre lycée'
            ])
            ->add('lyceeAutre', null, [
                'required' => false,
                'label' => 'Autre lycée'
            ])
            ->add('metier', null, [
                'required' => false,
                'label' => 'Métier ' . $champsRequis,
                'label_html' => true,
            ])
            ->add('nomSociete', null, [
                'required' => false,
                'label' => 'Nom de l\'employeur ' . $champsRequis,
                'label_html' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Eleve::class,
            'eleve' => null
        ]);
    }
}
