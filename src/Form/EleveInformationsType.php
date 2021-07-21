<?php

namespace App\Form;

use App\Entity\Eleve;
use App\Repository\ForfaitRepository;
use App\Repository\StatutSocialRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class   EleveInformationsType extends AbstractType
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
            ->add('mail', null, [
                'required' => true,
                'label' => 'Mail ' . $champsRequis,
                'label_html' => true,
                'data' => $eleve->getMail()
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
                'data' => $eleve->getTelephone()
            ])
            ->add('telParent', null, [
                'required' => false,
                'label' => 'Téléphone parent',
                'data' => $eleve->getTelParent(),
                'empty_data' => new EmptyString(),
            ])
            ->add('mailParent', null, [
                'required' => false,
                'label' => 'Mail parent',
                'data' => $eleve->getMailParent(),
                'empty_data' => new EmptyString(),
            ])
            ->add('adresse', null, [
                'required' => true,
                'label' => 'Adresse ' . $champsRequis,
                'label_html' => true,
                'data' => $eleve->getAdresse()
            ])
            ->add('ville', null, [
                'required' => true,
                'label' => 'Ville ' . $champsRequis,
                'label_html' => true,
                'data' => $eleve->getVille()
            ])
            ->add('cp', null, [
                'required' => true,
                'label' => 'Code postal ' . $champsRequis,
                'label_html' => true,
                'data' => $eleve->getCp()
            ])
            ->add('paysNaiss', null, [
                'required' => true,
                'label' => 'Pays de naissance ' . $champsRequis,
                'label_html' => true,
                'data' => $eleve->getPaysNaiss()
            ])
            ->add('depNaiss', null, [
                'required' => true,
                'label' => 'Département de naissance ' . $champsRequis,
                'label_html' => true,
                'data' => $eleve->getDepNaiss()
            ])
            ->add('villeNaiss', null, [
                'required' => true,
                'label' => 'Ville de naissance ' . $champsRequis,
                'label_html' => true,
                'data' => $eleve->getVilleNaiss()
            ])
            ->add('lunette', ChoiceType::class, [
                'label' => 'Portez-vous des lunettes ? ' . $champsRequis,
                'label_html' => true,
                'required' => true,
                'choices' => Eleve::LUNETTE,
                'data' => $eleve->getLunette()
            ])
            ->add('statutSocial', EntityType::class, [
                'class' => 'App\Entity\StatutSocial',
                'required' => true,
                'choice_value' => 'libelleStatutSocial',
                'label' => 'Statut social ' . $champsRequis,
                'label_html' => true,
                'data' => $eleve->getStatutSocial()
            ])
            ->add('lycee', EntityType::class, [
                'class' => 'App\Entity\Lycee',
                'choice_value' => 'nom',
                'required' => true,
                'label' => 'Lycée ' . $champsRequis,
                'label_html' => true,
                'data' => $eleve->getLycee(),
                'empty_data' => new EmptyString(),
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('l')
                        ->orderBy('l.nom', 'DESC');
                },
            ])
            ->add('lyceeAutre', null, [
                'required' => false,
                'label' => 'Autre lycée',
                'data' => $eleve->getLyceeAutre(),
                'empty_data' => new EmptyString()
            ])
            ->add('metier', null, [
                'required' => false,
                'label' => 'Métier ' . $champsRequis,
                'label_html' => true,
                'data' => $eleve->getMetier(),
                'empty_data' => new EmptyString()
            ])
            ->add('nomSociete', null, [
                'required' => false,
                'label' => 'Nom de l\'employeur ' . $champsRequis,
                'label_html' => true,
                'data' => $eleve->getNomSociete(),
                'empty_data' => new EmptyString()
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
