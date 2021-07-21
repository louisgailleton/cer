<?php

namespace App\Form;

use App\Entity\Eleve;
use DateTime;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SecretaireEleveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $eleve = $options['eleve'];
        if(isset($eleve) && !empty($eleve)) {
            $dateNaiss = $eleve->getDateNaiss();
            $dateNaiss = strtotime($dateNaiss);
            $dateNaiss = date('Y-m-d', $dateNaiss);
        } else {
            $dateNaiss = new DateTime('0000-00-00');
        }

        $builder
            ->add('id', HiddenType::class, [
                'required' => true,
            ])
            ->add('prenom', null, [
                'required' => true,
                'label' => 'Prénom',
                'data' => $eleve ? $eleve->getPrenom() : '',
            ])
            ->add('nom', null, [
                'required' => true,
                'label' => 'Nom',
                'data' => $eleve ? $eleve->getNom() : '',
            ])
            ->add('mail', null, [
                'required' => true,
                'label' => 'Mail',
                'data' => $eleve ? $eleve->getMail() : '',
            ])
            ->add('telephone', null, [
                'required' => true,
                'label' => 'Téléphone',
                'data' => $eleve ? $eleve->getTelephone() : '',
            ])
            ->add('autrePrenoms', null, [
                'label' => 'Autre prénoms',
                'required' => false,
                'data' => $eleve ? $eleve->getAutrePrenoms() : '',
                'empty_data' => new EmptyString()
            ])
            ->add('nomUsage', null, [
                'label' => 'Nom d\'usage',
                'required' => false,
                'data' => $eleve ? $eleve->getNumUsage() : '',
                'empty_data' => new EmptyString()
            ])
            ->add('dateNaiss', DateType::class, [
                'required' => true,
                'label' => 'Date de naissance',
                'widget' => 'single_text',
                'data' => $dateNaiss
            ])
            ->add('telParent', null, [
                'required' => false,
                'label' => 'Téléphone parent',
                'empty_data' => new EmptyString(),
                'data' => $eleve ? $eleve->getTelParent() : '',
            ])
            ->add('adresse', null, [
                'required' => true,
                'label' => 'Adresse',
                'data' => $eleve ? $eleve->getAdresse() : '',
            ])
            ->add('ville', null, [
                'required' => true,
                'label' => 'Ville',
                'data' => $eleve ? $eleve->getVille() : '',
            ])
            ->add('cp', null, [
                'required' => true,
                'label' => 'Code postal',
                'data' => $eleve ? $eleve->getCp() : '',
            ])
            ->add('paysNaiss', null, [
                'required' => true,
                'label' => 'Pays de naissance',
                'data' => $eleve ? $eleve->getPaysNaiss() : '',
            ])
            ->add('depNaiss', null, [
                'required' => true,
                'label' => 'Département de naissance',
                'data' => $eleve ? $eleve->getDepNaiss() : '',
            ])
            ->add('villeNaiss', null, [
                'required' => true,
                'label' => 'Ville de naissance',
                'data' => $eleve ? $eleve->getVilleNaiss() : '',
            ])
            ->add('lunette', ChoiceType::class, [
                'label' => 'L\'élève a-t-il des lunettes ?',
                'required' => true,
                'choices' => Eleve::LUNETTE,
                'data' => $eleve ? $eleve->getLunette() : '',
            ])
            ->add('statutSocial', EntityType::class, [
                'class' => 'App\Entity\StatutSocial',
                'required' => true,
                'choice_value' => 'libelleStatutSocial',
                'label' => 'Statut social',
                'data' => $eleve ? $eleve->getStatutSocial() : '',
            ])
            ->add('lycee', EntityType::class, [
                'class' => 'App\Entity\Lycee',
                'choice_value' => 'nom',
                'required' => false,
                'label' => 'Lycée',
                'data' => $eleve ? $eleve->getLycee() : '',
            ])
            ->add('lyceeAutre', null, [
                'required' => false,
                'label' => 'Autre lycée',
                'empty_data' => new EmptyString(),
                'data' => $eleve ? $eleve->getLyceeAutre() : '',
            ])
            ->add('nomSociete', null, [
                'required' => false,
                'label' => 'Raison sociale société',
                'empty_data' => new EmptyString(),
                'data' => $eleve ? $eleve->getNomSociete() : '',
            ])
            ->add('metier', null, [
                'required' => false,
                'label' => 'Métier',
                'empty_data' => new EmptyString(),
                'data' => $eleve ? $eleve->getMetier() : '',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'eleve' => null
        ]);
    }
}
