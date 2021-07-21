<?php

namespace App\Form;

use App\Entity\EvalPre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EvalPreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('permis', ChoiceType::class, [
                'label' => 'Avez vous déjà obtenu un permis',
                'expanded' => 'true',
                'multiple' => 'false',
                'choices' => [
                    'B' => 'B',
                    'AM' => 'AM',
                    'A' => 'A',
                ],
                'required' => 'true'
            ])
            ->add('expConduite', ChoiceType::class, [
                'label' => "Avez vous déjà conduit une voiture",
                'choices' => [
                    '-5h' => '-5',
                    '+5h' => '+ '
                ],
                'expanded' => 'true',
                'required' => 'true'
            ])
            ->add('ou', ChoiceType::class, [
                'label' => "Où ?",
                'choices' => [
                    'chemin' => 'chemin',
                    'route' => 'route',
                    'ville' => 'ville'
                ],
                'expanded' => 'true',
                'multiple' => 'true',
                'required' => 'true'
            ])

            ->add('p2Q1', ChoiceType::class, [
                'label' => "Q1 - Un panneau d'obligation est représenté par :",
                'choices' => [
                    'A' => 0,
                    'B' => 1,
                    'C' => 2,
                    'D' => 3,
                ],
                'expanded' => 'true',
                'multiple' => 'true',
                'required' => 'true'
            ])
            ->add('p2Q2', ChoiceType::class, [
                'label' => "Q2 - Dans cette sitation, pour continuer tout droit :",
                'choices' => [
                    'A - Je passe' => false,
                    'B - Je m\'arrête' => true,
                ],
                'expanded' => 'true',
                'required' => 'true'
            ])
            ->add('p2Q3', ChoiceType::class, [
                'label' => "Q3 - Nous sommes le 12 du mois. Je me stationne du côté :",
                'choices' => [
                    'A - Impair des immeubles' => true,
                    'B - Pair des immeubles' => false,
                ],
                'expanded' => 'true',
                'required' => 'true'
            ])
            ->add('p2Q4', ChoiceType::class, [
                'label' => "Q4 - 2 verres d'alcool représente une alcoolémie d'environ :",
                'choices' => [
                    'A - 0.25 g/l sang' => 'A',
                    'B - 0.50 mg/l air' => 'B',
                    'C - 0.50 g/l sang' => 'C',
                    'D - 0.25 mg/l air' => 'D',
                ],
                'expanded' => 'true',
                'multiple' => 'true',
                'required' => 'true'
            ])
            ->add('p2Q5', ChoiceType::class, [
                'label' => "Q5 - Ayant obtenu mon permis il y a un an, je suis limité sur cette route hors agglomération à :",
                'choices' => [
                    'A - 70' => 'A',
                    'B - 80' => 'B',
                    'C - 90' => 'C',
                ],
                'expanded' => 'true',
                'multiple' => 'true',
                'required' => 'true'
            ])
            ->add('p2Q6', ChoiceType::class, [
                'label' => "Q6 - La conduite avec kit main libre est :",
                'choices' => [
                    'A - Autorisée' => 'A',
                    'B - Tolérée' => 'B',
                    'C - Interdite' => 'C',
                ],
                'expanded' => 'true',
                'multiple' => 'true',
                'required' => 'true'
            ])
            ->add('p2Q7', ChoiceType::class, [
                'label' => "Q7 - Une femme enceinte est exemptée du port de la ceinture :",
                'choices' => [
                    'A - Oui' => false,
                    'B - Non' => true,
                ],
                'expanded' => 'true',
                'required' => 'true'
            ])
            ->add('p2Q8', ChoiceType::class, [
                'label' => "Q8 - Avec des pneumatiques lisses :",
                'choices' => [
                    'A - Je risque une amende' => 'A',
                    'B - Je diminue ma distance de freinage' => 'B',
                    'C - J\'aurais une meilleure adhérence' => 'C',
                    'D - Je risque l\'aquaplanning' => 'D',
                ],
                'expanded' => 'true',
                'multiple' => 'true',
                'required' => 'true'
            ])
            ->add('p2Q9', ChoiceType::class, [
                'label' => "Q9 - L'orifice de remplissage de l'huile moteur se situe en :",
                'choices' => [
                    'A' => true,
                    'B' => false,
                ],
                'expanded' => 'true',
                'required' => 'true'
            ])
            ->add('p2Q10', ChoiceType::class, [
                'label' => "Q10 - Dans le cadre d'un déménagement, pour mettre à jour la carte grise de mon véhicule, je dispose d'un délai de :",
                'choices' => [
                    'A - 15 jours' => 'A',
                    'B - 1 mois' => 'B',
                    'C - 2 mois' => 'C'
                ],
                'expanded' => 'true',
                'multiple' => 'true',
                'required' => 'true'
            ])
            ->add('p3Q1', ChoiceType::class, [
                'label' => "Q1 - La pédale du milieu est la pédale :",
                'choices' => [
                    'A - De frein' => 'A',
                    'B - D\'accélération' => 'B',
                    'C - D\'embrayage' => 'C'
                ],
                'expanded' => 'true',
                'multiple' => 'true',
                'required' => 'true'
            ])
            ->add('p3Q2', ChoiceType::class, [
                'label' => "Q2 - Cette commande permet :",
                'choices' => [
                    'A - De régler les feux sur la position automatique' => 'A',
                    'B - De régler la hauteur des deux' => 'B',
                    'C - D\'actionner les feux de croisement' => 'C',
                    'C - D\'alterner feux de croisement et feux de route' => 'C'
                ],
                'expanded' => 'true',
                'multiple' => 'true',
                'required' => 'true'
            ])
            ->add('p3Q3', ChoiceType::class, [
                'label' => "Q3 - Tous les véhicules sont-ils équipés d'une direction assistée ?",
                'choices' => [
                    'A - Oui' => true,
                    'B - Non' => false
                ],
                'expanded' => 'true',
                'required' => 'true'
            ])
            ->add('p3Q4', ChoiceType::class, [
                'label' => "Q4 - Lorsque j'appuie sur l'embrayage",
                'choices' => [
                    'A - J\'embraye' => false,
                    'B - Je débraye' => true
                ],
                'expanded' => 'true',
                'required' => 'true'
            ])
            ->add('p3Q5', ChoiceType::class, [
                'label' => "Q5 - La pédale de frein s'utilise avec quel pied ?",
                'choices' => [
                    'A - Gauche' => false,
                    'B - Droit' => true
                ],
                'expanded' => 'true',
                'required' => 'true'
            ])
            ->add('p3Q6', ChoiceType::class, [
                'label' => "Q6 - Le volant moteur sert :",
                'choices' => [
                    'A - À diriger les roues du véhicule' => true,
                    'B - À lier l\'embrayage au moteur' => false
                ],
                'expanded' => 'true',
                'required' => 'true'
            ])
            ->add('p3Q7', ChoiceType::class, [
                'label' => "Q7 - L'embrayage sert à démarrer :",
                'choices' => [
                    'A - Oui' => true,
                    'B - Non' => false,
                ],
                'expanded' => 'true',
                'required' => 'true'
            ])
            ->add('p3Q8', ChoiceType::class, [
                'label' => "Q8 - L'embrayage sert à s'arrêter :",
                'choices' => [
                    'A - Oui' => true,
                    'B - Non' => false,
                ],
                'expanded' => 'true',
                'required' => 'true'
            ])
            ->add('installation', HiddenType::class, [
                'required' => 'true'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EvalPre::class,
        ]);
    }
}
