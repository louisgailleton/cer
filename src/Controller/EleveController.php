<?php

namespace App\Controller;

use App\Entity\Agence;
use App\Entity\Appointment;
use App\Entity\Commande;
use App\Entity\ContenuForfait;
use App\Entity\Disponibilite;
use App\Entity\EvalPre;
use App\Entity\Indisponibilite;
use App\Entity\Lieu;
use App\Entity\LigneCommande;
use App\Entity\Moniteur;
use App\Entity\Panier;
use App\Entity\PorteOuverte;
use App\Entity\Role;
use App\Form\ElevePorteOuverteType;
use App\Form\EvalPre\EvalPreP1Type;
use App\Form\ForfaitType;
use App\Form\ModifMdpType;
use App\Form\PanierType;
use App\Repository\AgenceRepository;
use App\Repository\AppointmentRepository;
use App\Repository\CommandeRepository;
use App\Repository\ContenuForfaitRepository;
use App\Repository\DisponibiliteRepository;
use App\Repository\EvalPreRepository;
use App\Repository\ForfaitRepository;
use App\Repository\IndisponibiliteRepository;
use App\Repository\LieuRepository;
use App\Repository\MoniteurRepository;
use App\Repository\PanierRepository;
use App\Repository\PorteOuverteRepository;
use App\Repository\PrestationRepository;
use App\Repository\RoleRepository;
use App\Repository\TypePrestationRepository;
use DateInterval;
use DateTime;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Message;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Validator\Constraints\Date;
use App\Entity\Eleve;
use App\Entity\Forfait;
use App\Entity\Lycee;
use App\Entity\Utilisateur;
use App\Entity\Piecesjointes;
use App\Form\EleveChoixAgenceType;
use App\Form\EleveChoixForfaitType;
use App\Form\EleveFormulaireType;
use App\Form\EleveInformationsType;
use App\Form\NumNephType;
use App\Form\PiecesJointesType;
use App\Repository\EleveRepository;
use App\Repository\ExamenRepository;
use App\Repository\LyceeRepository;
use App\Repository\SettingsRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class EleveController extends AbstractController
{

    /**
     * @var EleveRepository
     */
    private $repository;
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var Security
     */
    private $security;
    /**
     * @var EvalPreRepository
     */
    private $er;

    public function __construct(EleveRepository $repository, EntityManagerInterface $em, Security $security,  EvalPreRepository $er)
    {
        $this->repository = $repository;
        $this->em = $em;
        $this->security = $security;
        $this->er = $er;
    }

    /**
     * @Route("/eleve", name="eleve.index")
     * @return Response
     */
    public function index(): Response
    {
        $eleve = $this->security->getUser();
        $evalPre = $eleve->getEvalPre();
        if ($eleve->getPorteOuverte() == null) {
            return $this->redirectToRoute('eleve.journeeInformation');
        } else if ($eleve->getFormulaireInscription() != "1") {
            return $this->redirectToRoute('eleve.formulaireInscription');
        } else if (!$evalPre) {
            return $this->redirectToRoute('eleve.evaluation');
        }
        else if ($evalPre->getScoreCode() == null) {
            return $this->redirectToRoute('eleve.evaluation.partie2');
        }
        else if ($evalPre->getScoreConduite() == null) {
            return $this->render('eleve/evaluation/evaluationP3.html.twig', [
                'eleve' => $eleve,
            ]);
        }
        else if($eleve->getForfait() == null) {
            return $this->redirectToRoute('eleve.choixForfait');
        }
        else if($eleve->getContratSigne() != '1') {
            return $this->redirectToRoute('eleve.affichageContrat');
        }
        else if($eleve->getEtatDossier() == 1) {
            return $this->redirectToRoute('eleve.dossier');
        } else {
            return $this->redirectToRoute('eleve.calendar');
        }
    }

    /**
     * @Route("/eleve/journeeInformation", name="eleve.journeeInformation")
     */
    public function journeeInformation(PorteOuverteRepository $pr, Request $request, MailerInterface $mailer)
    {
        $eleve = $this->security->getUser();
        $portesOuvertes = $pr->findBy(array(),array('date' => 'ASC'));

        $form = $this->createForm(ElevePorteOuverteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $idPO = $form->get('idPorteOuverte')->getData();
            $nbPersonne = $form->get('nbPersonne')->getData();

            if($nbPersonne >= 1 && $nbPersonne <= 3) {
                $po = $pr->findOneBy(['id' => $idPO]);
                if($eleve->getPorteOuverte() == null) {
                    $po = $pr->findOneBy(['id' => $idPO]);
                    $email = (new TemplatedEmail())
                        ->from('cergaribaldi@monpermis.fr')
                        ->to($eleve->getMail())
                        ->subject('CER GARIBALDI : Confirmation journée d\'information')
                        ->htmlTemplate("mail/confirmationPo.html.twig")
                        ->context([
                            'eleve' => $eleve,
                            'po' => $po
                        ]);
                    if ($eleve->getMailParent() != null) {
                        $email->cc($eleve->getMailParent());
                    }
                    $mailer->send($email);
                }
                $eleve->setPorteOuverte($po);
                $eleve->setNbPersonnePorteOuverte($nbPersonne);
                $eleve->setPorteOuverteAnnule(null);
                $this->em->persist($eleve);
                $this->em->flush();
                return $this->redirectToRoute('eleve.index');
            } else {
                $this->addFlash( 'danger', 'Le nombre de personne doit être compris entre 1 et 3');
                return $this->redirectToRoute('eleve.journeeInformation');
            }
        }

        return $this->render('eleve/porteOuverte.html.twig', [
            'eleve' => $eleve,
            'portesOuvertes' => $portesOuvertes,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/eleve/annulationPorteOuverte", name="eleve.annulationPorteOuverte")
     * @return Response
     */
    public function annulationPorteOuverte(): Response
    {
        $eleve = $this->security->getUser();
        $eleve->setPorteOuverte(null);
        $eleve->setNbPersonnePorteOuverte(null);
        $this->em->persist($eleve);
        $this->em->flush();

        return $this->redirectToRoute("eleve.journeeInformation");
    }

    /**
     * @Route("eleve/formulaire", name="eleve.formulaireInscription")
     */
    public function formulaireInscription(Request $request)
    {
        $eleve = $this->security->getUser();

        $form = $this->createForm(EleveFormulaireType::class, $eleve, ([
            'eleve' => $eleve
        ]));
        $form
            ->add('code', ChoiceType::class, [
                'required' => true,
                'label' => 'Avez vous votre code ou un numéro NEPH ? <span class="badge badge-danger badge-pill">REQUIS</span>',
                'label_html' => true,
                'choices'  => [
                    'Non' => '0',
                    'Oui' => '1',
                ],
            ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($eleve->getPorteOuverte() == null) {
                return $this->redirectToRoute('eleve.journeeInformation');
            } else {

                $dateNaiss = $form->get('dateNaiss')->getData();
                $dateJour = new DateTime();
                $date100 = new DateTime();
                $date100->sub(new DateInterval('P100Y'));

                if(!checkdate($dateNaiss->format('m'), $dateNaiss->format('d'), $dateNaiss->format('Y'))) {
                    $this->addFlash("danger", "La date de naissance n'est pas valide");
                    return $this->render('eleve/formulaireInscription.html.twig', [
                        'eleve' => $eleve,
                        'form' => $form->createView(),
                    ]);
                }
                else if($dateNaiss < $date100) {
                    $this->addFlash("danger", "La date de naissance est trop ancienne");
                    return $this->render('eleve/formulaireInscription.html.twig', [
                        'eleve' => $eleve,
                        'form' => $form->createView(),
                    ]);
                }
                else if($dateNaiss > $dateJour) {
                    $this->addFlash("danger", "La date de naissance ne peut être supérieure à la date du jour");
                    return $this->render('eleve/formulaireInscription.html.twig', [
                        'eleve' => $eleve,
                        'form' => $form->createView(),
                    ]);
                }
                else {
                    if ($eleve->getStatutSocial() != "Lycéen.ne") {
                        $eleve->setLycee("");
                        $eleve->setLyceeAutre("");
                    } else if ($eleve->getStatutSocial() != "Salarié.e") {
                        $eleve->setMetier("");
                        $eleve->setNomSociete("");
                    }
                    $telParent = $form->get("telParent")->getData();
                    if(strlen($telParent) > 0 && strlen($telParent) != 10){
                        $this->addFlash("danger", "Le numéro de téléphone doit contenir 10 chiffres");
                        return $this->render('eleve/formulaireInformation.html.twig', [
                            'eleve' => $eleve,
                            'form' => $form->createView()
                        ]);
                    }
                    if(!ctype_digit($telParent)) {
                        $this->addFlash("danger", "Le numéro de téléphone ne doit contenir que des chiffres");
                        return $this->render('eleve/formulaireInformation.html.twig', [
                            'eleve' => $eleve,
                            'form' => $form->createView()
                        ]);
                    }

                    $eleve->setFormulaireInscription("1");
                    $eleve->setCode($form->get('code')->getData());
                    $this->em->persist($eleve);
                    $this->em->flush();
                    return $this->redirectToRoute('eleve.index');
                }
            }
        }

        return $this->render('eleve/formulaireInscription.html.twig', [
            'eleve' => $eleve,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/eleve/evaluation", name="eleve.evaluation")
     */
    public function evalPre(Request $request)
    {
        $eleve = $this->security->getUser();
        $evalPre = $eleve->getEvalPre();
        if (isset($evalPre) && $evalPre->getScoreCode() == null) {
            return $this->redirectToRoute('eleve.evaluation.partie2');
        }
        else if (isset($evalPre) && $evalPre->getScoreConduite() == null) {
            return $this->render('eleve/evaluation/evaluationP3.html.twig', [
                'eleve' => $eleve,
            ]);
        } else {
            return $this->render('eleve/evaluation/index.html.twig', [
                'eleve' => $eleve
            ]);
        }
    }

    /**
     * @Route("/eleve/evaluation/partie1", name="eleve.evaluation.partie1")
     */
    public function evalPreP1(Request $request)
    {
        $eleve = $this->security->getUser();
        $evalPre = new EvalPre();

        if ($eleve->getFormulaireInscription() != "1") {
            return $this->redirectToRoute('eleve.formulaireInscription');
        }

        $form = $this->createForm(EvalPreP1Type::class, $evalPre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $evalPre->setEleve($eleve);
            $eleve->setEvalPre($evalPre);
            $this->em->persist($evalPre);
            $this->em->flush();
            return $this->redirectToRoute('eleve.evaluation.partie2');
        }

        return $this->render('eleve/evaluation/evaluationP1.html.twig', [
            'eleve' => $eleve,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/eleve/evaluation/partie2", name="eleve.evaluation.partie2")
     */
    public function evalPreP2(Request $request)
    {
        $eleve = $this->security->getUser();
        $evalPre = $eleve->getEvalPre();
        if (!$evalPre) {
            return $this->redirectToRoute('eleve.evaluation');
        }

        $form = $this->createFormBuilder()
            ->add('p2Q1', ChoiceType::class, [
                'label' => "Q1 - Un panneau d'obligation est représenté par :",
                'choices' => [
                    'A' => 'A',
                    'B' => 'B',
                    'C' => 'C',
                    'D' => 'D',
                ],
                'expanded' => 'true',
                'required' => 'true'
            ])
            ->add('p2Q2', ChoiceType::class, [
                'label' => "Q2 - Dans cette sitation, pour continuer tout droit :",
                'choices' => [
                    'A - Je passe' => "A",
                    'B - Je m\'arrête' => "B",
                ],
                'expanded' => 'true',
                'required' => 'true'
            ])
            ->add('p2Q3', ChoiceType::class, [
                'label' => "Q3 - Nous sommes le 12 du mois. Je me stationne du côté :",
                'choices' => [
                    'A - Impair des immeubles' => "A",
                    'B - Pair des immeubles' => "B",
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
                'required' => 'true'
            ])
            ->add('p2Q7', ChoiceType::class, [
                'label' => "Q7 - Une femme enceinte est exemptée du port de la ceinture :",
                'choices' => [
                    'A - Oui' => "A",
                    'B - Non' => "B",
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
                    'A' => "A",
                    'B' => "B",
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
                'required' => 'true'
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $p2Q1 = $form->get('p2Q1')->getData();
            $p2Q2 = $form->get('p2Q2')->getData();
            $p2Q3 = $form->get('p2Q3')->getData();
            $p2Q4 = $form->get('p2Q4')->getData();
            $p2Q5 = $form->get('p2Q5')->getData();
            $p2Q6 = $form->get('p2Q6')->getData();
            $p2Q7 = $form->get('p2Q7')->getData();
            $p2Q8 = $form->get('p2Q8')->getData();
            $p2Q9 = $form->get('p2Q9')->getData();
            $p2Q10 = $form->get('p2Q10')->getData();
            $score = 0;

            if($p2Q1 == "B") {
                $score++;
            }
            if($p2Q2 == "B") {
                $score++;
            }
            if($p2Q3 == "A") {
                $score++;
            }
            if ($p2Q4 == array('0' => 'C', '1' => 'D')) {
                $score++;
            }
            if($p2Q5 == "B") {
                $score++;
            }
            if($p2Q6 == "C") {
                $score++;
            }
            if($p2Q7 == "B") {
                $score++;
            }
            if ($p2Q8 == array('0' => 'A', '1' => 'D')) {
                $score++;
            }
            if($p2Q9 == "A") {
                $score++;
            }
            if($p2Q10 == "B") {
                $score++;
            }

            $evalPre->setScoreCode($score);
            if($score < 5) {
                $evalPre->setNbHeureTheorique('25');
            } else if ($score < 8) {
                $evalPre->setNbHeureTheorique('10');
            } else {
                $evalPre->setNbHeureTheorique('5');
            }
            $this->em->persist($evalPre);
            $this->em->flush();
            return $this->render('eleve/evaluation/evaluationP3.html.twig', [
                'eleve' => $eleve
            ]);
        }

        return $this->render('eleve/evaluation/evaluationP2.html.twig', [
            'eleve' => $eleve,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/eleve/evaluation/partie3", name="eleve.evaluation.partie3")
     */
    public function evalPreP3(Request $request)
    {
        $eleve = $this->security->getUser();
        $evalPre = $eleve->getEvalPre();
        if(isset($evalPre) && $evalPre->getScoreCode() == null) {
            return $this->redirectToRoute('eleve.evaluation.partie2');
        }

        $form = $this->createFormBuilder()
            ->add('p3Q1', ChoiceType::class, [
                'label' => "Q1 - La pédale du milieu est la pédale :",
                'choices' => [
                    'A - De frein' => 'A',
                    'B - D\'accélération' => 'B',
                    'C - D\'embrayage' => 'C'
                ],
                'expanded' => 'true',
                'required' => 'true'
            ])
            ->add('p3Q2', ChoiceType::class, [
                'label' => "Q2 - Cette commande permet :",
                'choices' => [
                    'A - De régler les feux sur la position automatique' => 'A',
                    'B - De régler la hauteur des feux' => 'B',
                    'C - D\'actionner les feux de croisement' => 'C',
                    'C - D\'alterner feux de croisement et feux de route' => 'D'
                ],
                'expanded' => 'true',
                'multiple' => 'true',
                'required' => 'true'
            ])
            ->add('p3Q3', ChoiceType::class, [
                'label' => "Q3 - Tous les véhicules sont-ils équipés d'une direction assistée ?",
                'choices' => [
                    'A - Oui' => 'A',
                    'B - Non' => 'B'
                ],
                'expanded' => 'true',
                'required' => 'true'
            ])
            ->add('p3Q4', ChoiceType::class, [
                'label' => "Q4 - Lorsque j'appuie sur l'embrayage",
                'choices' => [
                    'A - J\'embraye' => 'A',
                    'B - Je débraye' => 'B'
                ],
                'expanded' => 'true',
                'required' => 'true'
            ])
            ->add('p3Q5', ChoiceType::class, [
                'label' => "Q5 - La pédale de frein s'utilise avec quel pied ?",
                'choices' => [
                    'A - Gauche' => 'A',
                    'B - Droit' => 'B'
                ],
                'expanded' => 'true',
                'required' => 'true'
            ])
            ->add('p3Q6', ChoiceType::class, [
                'label' => "Q6 - Le volant moteur sert :",
                'choices' => [
                    'A - À diriger les roues du véhicule' => 'A',
                    'B - À lier l\'embrayage au moteur' => 'B'
                ],
                'expanded' => 'true',
                'required' => 'true'
            ])
            ->add('p3Q7', ChoiceType::class, [
                'label' => "Q7 - L'embrayage sert à démarrer :",
                'choices' => [
                    'A - Oui' => 'A',
                    'B - Non' => 'B',
                ],
                'expanded' => 'true',
                'required' => 'true'
            ])
            ->add('p3Q8', ChoiceType::class, [
                'label' => "Q8 - L'embrayage sert à s'arrêter :",
                'choices' => [
                    'A - Oui' => 'A',
                    'B - Non' => 'B',
                ],
                'expanded' => 'true',
                'required' => 'true'
            ])
            ->add('installation', HiddenType::class, [
                'required' => 'true'
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $p3Q1 = $form->get('p3Q1')->getData();
            $p3Q2 = $form->get('p3Q2')->getData();
            $p3Q3 = $form->get('p3Q3')->getData();
            $p3Q4 = $form->get('p3Q4')->getData();
            $p3Q5 = $form->get('p3Q5')->getData();
            $p3Q6 = $form->get('p3Q6')->getData();
            $p3Q7 = $form->get('p3Q7')->getData();
            $p3Q8 = $form->get('p3Q8')->getData();
            $installation = $form->get('installation')->getData();
            $scorep3 = 0;

            if($p3Q1 == "A") {
                $scorep3++;
            }
            if($p3Q2 == array('0' => 'B')) {
                $scorep3++;
            }
            if($p3Q3 == "B") {
                $scorep3++;
            }
            if ($p3Q4 == "B") {
                $scorep3++;
            }
            if($p3Q5 == "B") {
                $scorep3++;
            }
            if($p3Q6 == "B") {
                $scorep3++;
            }
            if($p3Q7 == "A" && $p3Q8 == "A") {
                $scorep3++;
            }
            if($installation == "siegeProfondeur,siegeHauteur,inclinaisonDossier,appuieTete,volant,retroInt,retroExt,ceinture") {
                $scorep3++;
            }

            $evalPre->setScoreConduite($scorep3);
            if($scorep3 < 4) {
                $evalPre->setNbHeurePratique('35-54');
            } else if ($scorep3 < 7) {
                $evalPre->setNbHeurePratique('25-35');
            } else {
                $evalPre->setNbHeurePratique('20-25');
            }
            $evalPre->setDateEvaluation(new DateTime());
            $this->em->persist($evalPre);
            $this->em->flush();

            return $this->redirectToRoute('eleve.evaluation.evalPreScore');
        }

        return $this->render('eleve/evaluation/evaluationP3form.html.twig', [
            'eleve' => $eleve,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/eleve/evaluation/score", name="eleve.evaluation.evalPreScore")
     */
    public function evalPreScore(Request $request)
    {
        $eleve = $this->security->getUser();
        $evalPre = $eleve->getEvalPre();
        if(isset($evalPre) && $evalPre->getScoreConduite() == null) {
            return $this->redirectToRoute('eleve.evaluation.partie3');
        }

        return $this->render('eleve/evaluation/affichageScore.html.twig', [
            'eleve' => $eleve,
            'evalPre' => $evalPre
        ]);
    }

    /**
     * @Route("/eleve/choixForfait", name="eleve.choixForfait")
     */
    public function choixForfait(Request $request, ForfaitRepository $fr)
    {
        $eleve = $this->security->getUser();
        $idAgence = $eleve->getAgence();
        $forfaits = $fr->forfaitAgence($idAgence);

        $form = $this->createFormBuilder()
            ->add('forfait', HiddenType::class)
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $evalPre = $eleve->getEvalPre();
            if(isset($evalPre) && $evalPre->getScoreConduite() == null) {
                return $this->redirectToRoute('eleve.evaluation.partie3');
            }
            else {
                $libelleForfait = $form->get('forfait')->getData();
                $forfait = $fr->findOneBy(['libelleforfait' => $libelleForfait]);
                $eleve->setForfait($forfait);

                $this->em->persist($eleve);
                $this->em->flush();
                return $this->redirectToRoute('eleve.affichageContrat');
            }
        }

        return $this->render('eleve/choixForfait.html.twig', [
            'eleve' => $eleve,
            'forfaits' => $forfaits,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/eleve/affichageContrat", name="eleve.affichageContrat")
     */
    public function affichageContrat(Request $request, AgenceRepository $ar, ContenuForfaitRepository $cr, PrestationRepository $pr, EvalPreRepository $er, SessionInterface $session)
    {
        $eleve = $this->security->getUser();
        if($eleve->getForfait() == null) {
            return $this->redirectToRoute('eleve.choixForfait');
        } else {
            $eleve->setContratSigne('1');
            $dateNaiss = $eleve->getDateNaiss()->format('d/m/Y');
            $forfait = $eleve->getForfait();
            $prestations = $cr->contenusForfait($forfait);
            $idAgence = $eleve->getAgence();
            $agence = $ar->find($idAgence);
            $dateJour = date('d/m/Y');
            $dateUnAn = date('d/m/').(date('Y') + 1);
            $prestaHorsForfait = $pr->findAll();
            $evalPre = $er->findOneBy(["eleve" => $eleve]);

            if($eleve->getStatutSocial() == "Salarié.e") {
                $form = $this->createFormBuilder()
                    ->add('developperCompetence', CheckboxType::class, [
                        'label' => "Développer une compétence professionnelle (en lien ou non avec votre poste actuel)",
                        'data' => true,
                        'disabled' => true,
                        'required' => false
                    ])
                    ->add('favoriserMobilite', CheckboxType::class, [
                        'label' => "Favoriser votre mobilité professionnelle",
                        'data' => true,
                        'disabled' => true,
                        'required' => false
                    ])
                    ->add('evolutionPro', CheckboxType::class, [
                        'label' => "Permettre une évolution professionnelle au sein de votre entreprise actuelle ou en dehors de celle-ci",
                        'data' => true,
                        'disabled' => true,
                        'required' => false
                    ])
                    ->add('evolutionEmploi', CheckboxType::class, [
                        'label' => "Permettre de vous adapter à l’évolution de votre emploi",
                        'required' => false
                    ])
                    ->add('qualificationElevee', CheckboxType::class, [
                        'label' => "Acquérir une qualification plus élevée",
                        'required' => false
                    ])
                    ->add('nouveauContrat', CheckboxType::class, [
                        'label' => "En application d’un nouveau contrat de travail ou d’une clause de mobilité géographique votre lieu de travail est maintenant significativement éloigné de votre domicile",
                        'required' => false
                    ])
                    ->add('horaireDecale', CheckboxType::class, [
                        'label' => "Vous serez bientôt amenés à travailler en horaire décalé (notamment la nuit)",
                        'required' => false
                    ])
                    ->add('travailEloigne', CheckboxType::class, [
                        'label' => "Vous êtes amené(e) à exercer des contrats de travail successifs éloignés de votre domicile",
                        'required' => false
                    ])
                    ->getForm();
            } else if ($eleve->getStatutSocial() == "Sans emploi") {
                $form = $this->createFormBuilder()
                    ->add('developperCompetence', CheckboxType::class, [
                        'label' => "Développer une compétence professionnelle (en lien ou non avec votre poste actuel)",
                        'data' => true,
                        'disabled' => true,
                        'required' => false
                    ])
                    ->add('favoriserMobilite', CheckboxType::class, [
                        'label' => "Favoriser votre mobilité professionnelle",
                        'data' => true,
                        'disabled' => true,
                        'required' => false
                    ])
                    ->add('ameliorerAcces', CheckboxType::class, [
                        'label' => "Améliorer vos conditions d’accès à l’emploi",
                        'data' => true,
                        'disabled' => true,
                        'required' => false
                    ])
                    ->add('evolutionEmploi', CheckboxType::class, [
                        'label' => "Permettre de vous adapter à l’évolution de votre emploi",
                        'required' => false
                    ])
                    ->add('qualificationElevee', CheckboxType::class, [
                        'label' => "Acquérir une qualification plus élevée",
                        'required' => false
                    ])
                    ->add('nouveauContrat', CheckboxType::class, [
                        'label' => "En application d’un nouveau contrat de travail ou d’une clause de mobilité géographique votre lieu de travail est maintenant significativement éloigné de votre domicile",
                        'required' => false
                    ])
                    ->add('horaireDecale', CheckboxType::class, [
                        'label' => "Vous serez bientôt amenés à travailler en horaire décalé (notamment la nuit)",
                        'required' => false
                    ])
                    ->add('travailEloigne', CheckboxType::class, [
                        'label' => "Vous êtes amené(e) à exercer des contrats de travail successifs éloignés de votre domicile",
                        'required' => false
                    ])
                    ->getForm();
            } else {
                $form = $this->createFormBuilder()
                    ->getForm();
            }

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $session->set('questionnaire', $form->getData());

                $eleve->setContratSigne('1');
                $this->em->persist($eleve);
                $this->em->flush();
                return $this->redirectToRoute("eleve.telechargerContrat");
            }

            return $this->render('eleve/contrat/affichageContrat.html.twig', [
                'eleve' => $eleve,
                'agence' => $agence,
                'dateNaiss' => $dateNaiss,
                'forfait' => $forfait,
                'dateJour' => $dateJour,
                'dateUnAn' => $dateUnAn,
                'prestations' => $prestations,
                'prestationsHorsForfait' => $prestaHorsForfait,
                'evalPre' => $evalPre,
                'form' => $form->createView()
            ]);
        }
    }

    /**
     * @Route("/eleve/contrat", name="eleve.telechargerContrat")
     * @param AgenceRepository $ar
     * @param ContenuForfaitRepository $cr
     */
    public function telechargerContrat(AgenceRepository $ar, ContenuForfaitRepository $cr, PrestationRepository $pr, SessionInterface $session, EvalPreRepository $er)
    {
        $eleve = $this->security->getUser();
        if($eleve->getForfait() == null) {
            return $this->redirectToRoute('eleve.choixForfait');
        } else {
            $dateNaiss = $eleve->getDateNaiss()->format('d m Y');
            $forfait = $eleve->getForfait();
            $prestations = $cr->contenusForfait($forfait);
            $idAgence = $eleve->getAgence();
            $agence = $ar->find($idAgence);
            $dateJour = date('d/m/Y');
            $dateUnAn = date('d/m/') . (date('Y') + 1);
            $prestaHorsForfait = $pr->findAll();
            $evalPre = $er->findOneBy(["eleve" => $eleve]);

            $questionnaire = $session->get('questionnaire');

            $pdfOptions = new Options();

            // Instantiate Dompdf with our options
            $dompdf = new Dompdf($pdfOptions);

            // Retrieve the HTML generated in our twig file
            $html = $this->renderView('eleve/contrat/telechargementContrat.html.twig', [
                'title' => "Welcome to our PDF Test",
                'eleve' => $eleve,
                'agence' => $agence,
                'dateNaiss' => $dateNaiss,
                'forfait' => $forfait,
                'dateJour' => $dateJour,
                'dateUnAn' => $dateUnAn,
                'prestations' => $prestations,
                'prestationsHorsForfait' => $prestaHorsForfait,
                'evalPre' => $evalPre,
                'questionnaire' => $questionnaire
            ]);


            // Load HTML to Dompdf
            $dompdf->loadHtml($html);

            // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
            $dompdf->setPaper('A4', 'portrait');

            // Render the HTML as PDF
            $dompdf->render();

            // Output the generated PDF to Browser (inline view)
            $dompdf->stream("contrat.pdf", [
                "Attachment" => true
            ]);

            return $this->redirectToRoute("eleve.dossier");
        }
    }

    /**
     * @Route("/eleve/informations", name="eleve.information")
     */
    public function formulaireInformation(Request $request)
    {
        $eleve = $this->security->getUser();

        $form = $this->createForm(EleveInformationsType::class, $eleve, ([
            'eleve' => $eleve
        ]));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $dateNaiss = $form->get('dateNaiss')->getData();
            $dateJour = new DateTime();
            $date100 = new DateTime();
            $date100->sub(new DateInterval('P100Y'));

            if(!checkdate($dateNaiss->format('m'), $dateNaiss->format('d'), $dateNaiss->format('Y'))) {
                $this->addFlash("danger", "La date de naissance n'est pas valide");
                return $this->render('eleve/formulaireInformation.html.twig', [
                    'eleve' => $eleve,
                    'form' => $form->createView(),
                ]);
            }
            else if($dateNaiss < $date100) {
                $this->addFlash("danger", "La date de naissance est trop ancienne");
                return $this->render('eleve/formulaireInformation.html.twig', [
                    'eleve' => $eleve,
                    'form' => $form->createView(),
                ]);
            }
            else if($dateNaiss > $dateJour) {
                $this->addFlash("danger", "La date de naissance ne peut être supérieure à la date du jour");
                return $this->render('eleve/formulaireInformation.html.twig', [
                    'eleve' => $eleve,
                    'form' => $form->createView(),
                ]);
            }
            else {
                if ($eleve->getStatutSocial() != "Lycéen.ne") {
                    $eleve->setLycee("");
                    $eleve->setLyceeAutre("");
                } else if ($eleve->getStatutSocial() != "Salarié.e") {
                    $eleve->setMetier("");
                    $eleve->setNomSociete("");
                }
                $telParent = $form->get("telParent")->getData();
                if(strlen($telParent) > 0 && (strlen($telParent) != 10 || !ctype_digit($telParent))){
                    $this->addFlash("danger", "Le numéro de téléphone doit contenir 10 chiffres");
                    return $this->render('eleve/formulaireInformation.html.twig', [
                        'eleve' => $eleve,
                        'form' => $form->createView()
                    ]);
                }
                /*if(!ctype_digit($telParent)) {
                    $this->addFlash("danger", "Le numéro de téléphone ne doit contenir que des chiffres");
                    return $this->render('eleve/formulaireInformation.html.twig', [
                        'eleve' => $eleve,
                        'form' => $form->createView()
                    ]);
                }*/

                $this->em->persist($eleve);
                $this->em->flush();
                return $this->redirectToRoute('eleve.donnees');
            }
        }

        return $this->render('eleve/formulaireInformation.html.twig', [
            'eleve' => $eleve,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/eleve/mesInformations", name="eleve.donnees")
     */
    public function pageMesInformations(Request $request)
    {
        $eleve = $this->security->getUser();
        if ($eleve->getAdresse() == null) {
            return $this->redirectToRoute('eleve.index');
        }

        return $this->render('eleve/mesInformations.html.twig', [
            'eleve' => $eleve,
        ]);
    }

    /**
     * @Route("/modificationMdp", name="eleve.modifMdp", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function modifMdp(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {

        $eleve = $this->security->getUser();

        $form = $this->createForm(ModifMdpType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ancienMdp = $form->get('mdp')->getData();
            $nouveauMdp = $form->get('nouveauMdp')->getData();
            if (!($passwordEncoder->isPasswordValid($eleve, $ancienMdp))) {
                $message = "Les mots de passe sont différents !";
                return $this->render('eleve/modifMdp.html.twig', [
                    'form' => $form->createView(),
                    'eleve' => $eleve,
                    'message' => $message
                ]);
            } else {
                $eleve->setMdp(
                    $passwordEncoder->encodePassword(
                        $eleve,
                        $nouveauMdp
                    )
                );
                $this->em->persist($eleve);
                $this->em->flush();

                return $this->redirectToRoute('eleve.donnees');
            }
        }
        return $this->render('eleve/modifMdp.html.twig', [
            'form' => $form->createView(),
            'eleve' => $eleve
        ]);
    }

    /**
     * @Route("/numNEPH", name="eleve.numNEPH")
     * @return Response
     */
    public function numNEPH()
    {
        $eleve = $this->security->getUser();
        if($eleve->getCode() == "0" || $eleve->getCode() == null) {
            $eleve->setCode("1");
        } else if ($eleve->getCode() == "1") {
            $eleve->setCode("0");
            $eleve->setNeph("");
        }

        $this->em->persist($eleve);
        $this->em->flush();
        return $this->redirectToRoute("eleve.dossier");
    }

    /**
     * @Route("/eleve/dossier", name="eleve.dossier")
     * @param Request $request
     * @param SluggerInterface $slugger
     * @return Response
     */
    public function dossier(Request $request, SluggerInterface $slugger): Response
    {
        $eleve = $this->security->getUser();
        $piecesJointes = $eleve->getPiecesjointes();

        $formNEPH = $this->createForm(NumNephType::class, $eleve);
        $formNEPH->handleRequest($request);
        if ($formNEPH->isSubmitted() && $formNEPH->isValid()) {
            $this->em->persist($eleve);
            $this->em->flush();

            return $this->redirectToRoute('eleve.index');
        }

        $PJ = new Piecesjointes();
        $form = $this->createForm(PiecesJointesType::class, $PJ);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $fichier = $form->get('nomFichier')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($fichier) {
                $nomOriginal = pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $nomSecurise = $slugger->slug($nomOriginal);
                $nomUnique = $nomSecurise . '-' . uniqid() . '.' . $fichier->guessExtension();
                // Move the file to the directory where brochures are stored
                try {
                    $fichier->move(
                        $this->getParameter('dossierPJ'),
                        $nomUnique
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $PJ->setNomFichier($nomSecurise);
                $PJ->setNomFichierUnique($nomUnique);
                $PJ->setEleve($eleve);
            }

            $this->em->persist($PJ);
            $this->em->flush();
            return $this->redirectToRoute("eleve.dossier");
        }

        return $this->render('eleve/dossier.html.twig', [
            'form' => $form->createView(),
            'formNEPH' => $formNEPH->createView(),
            'eleve' => $eleve,
            'piecesJointes' => $piecesJointes,
            'commentairePJ' => $eleve->getCommentairePJ(),
        ]);
    }

    /**
     * @Route("/eleve/validerDossier", name="eleve.validerDossier")
     */
    public function validerDossier() {
        $eleve = $this->security->getUser();
        $piecesjointes = $eleve->getPiecesJointes();
        $pieceObligatoire = [];
        foreach ($piecesjointes as $pj) {
            if($pj->getTypePJ() == "EPHOTO" && $pj->getEtat() != "1") {
                array_push($pieceObligatoire, $pj);
            }
            if($pj->getTypePJ() == "CNI" && $pj->getEtat() != "1") {
                array_push($pieceObligatoire, $pj);
            }
            if($pj->getTypePJ() == "JUSTIFDOM" && $pj->getEtat() != "1") {
                array_push($pieceObligatoire, $pj);
            }
            if($pj->getTypePJ() == "JDC" && $pj->getEtat() != "1") {
                array_push($pieceObligatoire, $pj);
            }

            if($eleve->getAge() < 18) {
                if($pj->getTypePJ() == "ATTESHEB" && $pj->getEtat() != "1") {
                    array_push($pieceObligatoire, $pj);
                }
            }

        }
        if($eleve->getAge() < 18) {
            if(sizeof($pieceObligatoire) == 5) {
                $eleve->setEtatDossier("2");
                $eleve->setCommentaireEPHOTO("");
                $eleve->setCommentaireCNI("");
                $eleve->setCommentaireJUSTIFDOM("");
                $eleve->setCommentaireATTESHEB("");
                $eleve->setCommentaireJDC("");
                $eleve->setCommentaireAUTREP("");
                $this->em->persist($eleve);
                $this->em->flush();
                return $this->redirectToRoute("eleve.code");
            } else {
                $this->addFlash('danger', 'Vous devez au moins envoyer une e-photo, une carte d\'identité, un justificatif de domicile, une attestation d\'hébergement et une attestation de JDC');
                return $this->redirectToRoute("eleve.dossier");
            }
        } else {
            if(sizeof($pieceObligatoire) == 4) {
                $eleve->setEtatDossier("2");
                $eleve->setCommentaireEPHOTO("");
                $eleve->setCommentaireCNI("");
                $eleve->setCommentaireJUSTIFDOM("");
                $eleve->setCommentaireATTESHEB("");
                $eleve->setCommentaireJDC("");
                $eleve->setCommentaireAUTREP("");
                $this->em->persist($eleve);
                $this->em->flush();
                return $this->redirectToRoute("eleve.code");
            } else {
                $this->addFlash('danger', 'Vous devez au moins envoyer une e-photo, une carte d\'identité, un justificatif de domicile et une attestation de JDC');
                return $this->redirectToRoute("eleve.dossier");
            }
        }
    }

    /**
     * @Route("eleve/supprPJ{id}", name="eleve.supprimerPJ", methods={"DELETE"})
     */
    public function supprPJ(Request $request, Piecesjointes $pj): Response
    {
        if ($this->isCsrfTokenValid('delete' . $pj->getId(), $request->request->get('_token'))) {
            $this->em->remove($pj);
            $this->em->flush();
            $nomPJ = $pj->getNomFichierUnique();
            unlink("piecesjointes/" . $nomPJ);
        }

        return $this->redirectToRoute('eleve.dossier');
    }

    /**
     * @Route("/eleve/code", name="eleve.code")
     */
    public function code()
    {
        $eleve = $this->security->getUser();

        return $this->render('eleve/code.html.twig', [
            'eleve' => $eleve,
        ]);
    }

    /**
     * @Route("/eleve/boutique", name="eleve.boutique")
     */
    public function boutique(TypePrestationRepository $tr, PrestationRepository $pr)
    {
        $eleve = $this->security->getUser();
        $typesPresta = $tr->findAll();
        $prestations = $pr->findAll();
        $panier = $eleve->getPaniers();


        return $this->render('eleve/boutique.html.twig', [
            'eleve' => $eleve,
            'typesPresta' => $typesPresta,
            'prestations' => $prestations,
            'panier' => $panier
        ]);
    }

    /**
     * @Route("/eleve/ajoutPanier", name="eleve.ajoutPanier")
     */
    public function ajoutPanier(PanierRepository $pr, PrestationRepository $prestaRepo)
    {
        $eleve = $this->security->getUser();
        $idProduit = $_POST['produit'];
        $produit = $prestaRepo->find($idProduit);
        if($produit != "") {
            $panier = $pr->findOneBy(['produit' => $produit, 'eleve' => $eleve]);
            if(empty($panier)) {
                $panier = new Panier();
                $panier->setProduit($produit);
                $panier->setQuantite('1');
                $panier->setEleve($eleve);
            } else {
                $panier->setQuantite($panier->getQuantite()+1);
            }
            $this->em->persist($panier);
            $this->em->flush();
        }
        return $this->redirectToRoute('eleve.boutique');
    }

    /**
     * @Route("/eleve/supprPanier", name="eleve.supprPanier")
     */
    public function supprPanier(PanierRepository $pr, PrestationRepository $prestaRepo)
    {
        $eleve = $this->security->getUser();
        $idProduit = $_POST['produit'];
        $produit = $prestaRepo->find($idProduit);
        if($produit != "") {
            $panier = $pr->findOneBy(['produit' => $produit, 'eleve' => $eleve]);
            if(!empty($panier)) {
                if($panier->getQuantite() > 1) {
                    $panier->setQuantite($panier->getQuantite()-1);
                    $this->em->persist($panier);
                } else {
                    $this->em->remove($panier);
                }
            }
            $this->em->flush();
        }
        return $this->redirectToRoute('eleve.boutique');
    }

    /**
     * @Route("/eleve/ajoutCommande", name="eleve.ajoutCommande")
     */
    public function ajoutCommande(PanierRepository $pr, PrestationRepository $prestaRepo)
    {
        $eleve = $this->security->getUser();
        $panier = $eleve->getPaniers();

        $commande = new Commande();
        $commande->setEleve($eleve);
        $commande->setEtat("Paiement en cours");
        $commande->setDateCommande(new DateTime());

        foreach ($panier as $contenuPanier) {
            $ligneCommande = new LigneCommande();
            $ligneCommande->setCommande($commande);
            $ligneCommande->setProduit($contenuPanier->getProduit());
            $ligneCommande->setQuantite($contenuPanier->getQuantite());
            $this->em->persist($ligneCommande);
            $this->em->remove($contenuPanier);
        }
        $this->em->persist($commande);
        $this->em->flush();

        return $this->redirectToRoute('eleve.boutique');
    }

    /**
     * @Route("/eleve/commandes", name="eleve.commandes")
     */
    public function commandes(CommandeRepository $cr)
    {
        $eleve = $this->security->getUser();
        $commandes = $eleve->getCommandes();

        return $this->render('eleve/commandes.html.twig', [
            'eleve' => $eleve,
            'commandes' => $commandes,
        ]);
    }

    /**
     * @Route("/eleve/detailCommande/{id}", name="eleve.detailCommande")
     * @param Commande $commande
     * @return Response
     */
    public function detailCommande(Commande $commande)
    {
        $eleve = $this->security->getUser();
        $lignesCommande = $commande->getLigneCommandes();

        return $this->render('eleve/detailCommande.html.twig', [
            'eleve' => $eleve,
            'commande' => $commande,
            'lignesCommande' => $lignesCommande
        ]);
    }


    /**
     * @Route("/eleve/conduite", name="eleve.calendar")
     *
     */
    public function Calendar(
        IndisponibiliteRepository $indispo,
        AppointmentRepository $AppointmentRepo,
        DisponibiliteRepository $dispoRep,
        MoniteurRepository $moniteurs,
        EleveRepository $elevesRep,
        LieuRepository $lieuRepo,
        SettingsRepository $settingsRepo
    ): Response {
        //recup si l'eleve est inscris en examen

        $eleve = $this->security->getUser();

        //Recup du mode automatique ou non
        $setting = $settingsRepo->find(1);
        $setting = $setting->getAuto();

        //RECUP DE L'eleve
        $AAC = false;
        $eleve = $elevesRep->findOneById($this->getUser()->getId());
        $examens = $eleve->getExamens();
        $examenEleve = [];
        foreach ($examens as $examen) {

            $examenEleve[] = [
                'id' => $examen->getId(),
                'title' => 'Lieu : ' . $examen->getLieu()->getLibelle(),
                'start' => $examen->getStart()->format('Y-m-d H:i:s'),
                'end' => $examen->getEnd()->format('Y-m-d H:i:s'),
                'backgroundColor' => '#FF1493',
                'borderColor' => '#FF1493',
                'textColor' => 'black',
                'display' => 'block',
                'type' => 'exam',
            ];
        }
        $forfait = $eleve->getForfait();
        /*
         * A VOIR COMMENT FAIRE
         * if (isset($forfait) && $forfait->getCode() == 'AAC') {
            $AAC = true;
        }*/

        //RECUP DISPOS DE L'ELEVE CONNECTE
        $dispos = $dispoRep->Eleve($eleve);
        $listeDispos = [];

        foreach ($dispos as $dispo) {

            $listeDispos[] = [

                'id' => $dispo->getId(),
                'title' => 'Dispo',
                'start' => $dispo->getStart()->format('Y-m-d H:i:s'),
                'end' => $dispo->getEnd()->format('Y-m-d H:i:s'),
                'backgroundColor' => '#20a0ff',
                'borderColor' => '#20a0ff',
                'textColor' => '#000000',
                'display' => 'background',
                'type' => 'dispo',

            ];
        }

        $debutPlageHoraire = new DateTime();
        $debutPlageHoraire->setTime(7, 0);

        $finPlageHoraire = new DateTime();
        $finPlageHoraire->modify('+2 days');
        $finPlageHoraire->setTime(20, 0);

        $monos = [];
        $monos = $this->getHeureCommune($debutPlageHoraire, $finPlageHoraire);

        //RECUP RDV DE L'ELEVE CONNECTE
        $appointments = $AppointmentRepo->Eleve($eleve);

        $listeRDVs = [];

        foreach ($appointments as $appointment) {
            $moniteurRDV = $moniteurs->find($appointment->getMoniteur());
            $LieuRDV = $lieuRepo->find($appointment->getLieu());
            $title = 'Lieu : ' . $LieuRDV->getLibelle();
            $color = "#83DE7C";

            if ($appointment->getMotif()) {

                if ($appointment->getMotif()->getId() == 1) {
                    $title = 'ABSENCE';
                    $color = '#e00000';
                }
            }
            if ($appointment->getCancelled()) {
                $etat = 'annule';
                $color = '#FF0000';
            } else {
                $etat = '';
            }

            $listeRDVs[] = [

                'id' => $appointment->getId(),
                'title' => $title,
                'moniteur_id' => $moniteurRDV->getId(),
                'description' => $moniteurRDV->getPrenom() . " " . $moniteurRDV->getNom(),
                'type' => 'rdv',
                'start' => $appointment->getStart()->format('Y-m-d H:i:s'),
                'end' => $appointment->getEnd()->format('Y-m-d H:i:s'),
                'backgroundColor' => $color,
                'borderColor' => $color,
                'textColor' => '#000000',
                'lieu' => $LieuRDV->getId(),
                'etat' => $etat

            ];
        }

        //RECUP INDISPONIBILITES DU MONITEUR
        $events = $indispo->findAll();

        $toutesIndispos = [];
        foreach ($events as $event) {
            $moniteurIndispo = $moniteurs->find($event->getMoniteur());
            $toutesIndispos[] =
                [
                    'id' => $event->getId(),
                    'moniteur' => $event->getMoniteur(),
                    'moniteur_id' => $moniteurIndispo->getId(),
                    'start' => $event->getStart()->format('Y-m-d H:i:s'),
                    'end' => $event->getEnd()->format('Y-m-d H:i:s'),
                    'color' => 'grey',
                    'display' => 'background',
                    'type' => 'indispo',

                ];
        }

        $lieux = $lieuRepo->findAll();
        foreach ($lieux as $lieu) {
            if (!$lieu->getExamen()) {

                if ($lieu->getMoniteur()) {
                    $moniteurLieu = $moniteurs->find($lieu->getMoniteur());
                    $toutLieux[] =
                        [
                            'id' => $lieu->getId(),
                            'moniteur_id' => $moniteurLieu->getId(),
                            'libelle' => $lieu->getLibelle(),
                        ];
                } else {
                    $toutLieux[] =
                        [
                            'id' => $lieu->getId(),
                            'moniteur_id' =>  null,
                            'libelle' => $lieu->getLibelle(),
                        ];
                }
            }
        }

        $tout = array_merge($listeRDVs, $listeDispos, $examenEleve);
        $data = json_encode($tout);
        $toutesIndispos = json_encode($toutesIndispos);
        $toutLieux = json_encode($toutLieux);

        return $this->render('eleve/conduite.html.twig', [
            'data' => $data,
            'eleve' => $eleve,
            'moniteurs' => $monos,
            'indispos' => $toutesIndispos,
            'lieux' => $toutLieux,
            'debut' => $debutPlageHoraire->format('Y-m-d'),
            'fin' => $finPlageHoraire->format('Y-m-d'),
            'auto' => $setting,
            'AAC' => $AAC,
            'dateExamen' => $this->getExamen(),
            'compteur' =>  sprintf('%02d:%02d', (int) $this->getUser()->getCompteurHeure(), fmod($this->getUser()->getCompteurHeure(), 1) * 60) . 'h'

        ]);
    }

    /**
     * @Route("/eleve/newRdv", name="eleve.newRdv", methods={"POST"})
     */
    public function newRdv(SettingsRepository $settingsRepo, Request $request, EleveRepository $eleveRep, MoniteurRepository $moniteurRep, LieuRepository $lieuRep): Response
    {
        $moniteur = $request->get('moniteur');
        $start = $request->get('start');
        $end = $request->get('end');
        $lieu = $request->get('lieu');

        $start = new DateTime($start);
        $end = new DateTime($end);
        $eleve = $eleveRep->find($this->getUser()->getId());

        $setting = $settingsRepo->find(1);
        $setting = $setting->getAuto();
        $AAC = false;
        $forfait = $eleve->getForfait();
        if ($forfait) {
            /*
             * A VOIR COMMENT FAIRE
             * if (isset($forfait) && $forfait->getCode() == 'AAC') {
                $AAC = true;
            }*/

            if ($setting && ($this->getExamen() > $start || $AAC || (int)date_format($start, 'd') < 20 || (int)date_format($start, 'd') > 26)) {
                $t1 = strtotime($end->format('Y-m-d\TH:i:s.u'));
                $t2 = strtotime($start->format('Y-m-d\TH:i:s.u'));
                $diff = $t1 - $t2;
                $nbNouvelleHeures = $diff / (60 * 60);

                //Compter son nombre d'heure dans la semaine
                //si AAC et  nbHeure + nouvelles heures <= 6
                // ou si nbHeure + nouvelles heures <= 2

                $nbHeureSemaine = 0;
                $startEndWeek =  $this->getStartAndEndDate($start->format("W"), $start->format("Y"));
                $appointments = $eleve->getAppointments();
                foreach ($appointments as $appointment) {
                    if ($appointment->getStart() >= new DateTime($startEndWeek['week_start']) && $appointment->getEnd() <= new DateTime($startEndWeek['week_end']) && $appointment->getCancelled() != true) {
                        $t1 = strtotime($appointment->getEnd()->format('Y-m-d\TH:i:s.u'));
                        $t2 = strtotime($appointment->getStart()->format('Y-m-d\TH:i:s.u'));
                        $diff = $t1 - $t2;
                        $nbHeureSemaine += $diff / (60 * 60);
                    }
                }

                if (($AAC && $nbHeureSemaine + $nbNouvelleHeures <= 6) || $nbHeureSemaine + $nbNouvelleHeures <= 2) {

                    if (
                        isset($moniteur) && !empty($moniteur) &&
                        isset($start) && !empty($start) &&
                        isset($end) && !empty($end) &&
                        isset($lieu) && !empty($lieu)

                    ) {
                        $moniteur = $moniteurRep->find($moniteur);
                        $lieu = $lieuRep->find($lieu);
                        if ($lieuRep->Check($moniteur, $lieu)) {

                            $appointment = new Appointment();

                            $appointment->setEleve($eleve);
                            $appointment->setMoniteur($moniteur);
                            $appointment->setStart($start);
                            $appointment->setEnd($end);
                            $appointment->setLieu($lieu);
                            $appointment->setCancelled(false);
                            $appointment->setDone(false);

                            $em = $this->getDoctrine()->getManager();
                            $em->persist($appointment);
                            $em->flush();

                            $indispo = new Indisponibilite();

                            $indispo->setMoniteur($moniteur);
                            $indispo->setStart($start);
                            $indispo->setEnd($end);

                            $em = $this->getDoctrine()->getManager();
                            $em->persist($indispo);
                            $em->flush();
                        }

                        // $this->addFlash(
                        //     'notice',
                        //     'Rendez-vous pris avec succès !'
                        // );
                        // return $this->redirectToRoute('eleve.calendar',array('id' => $donnees->moniteur) );
                    }
                }

                return new Response('Ok', 200);
            } else {
                return new Response('Il est actuellement interdit de prendre rendez-vous par vous meme.', 403);
            }
        } else {
            return new Response('Il est actuellement interdit de prendre rendez-vous par vous meme.', 403);
        }
    }

    /**
     * @Route("/eleve/dupliqueDispo", name="eleve.dupliqueDispo", methods={"POST"})
     */
    public function dupliqueDispo(DisponibiliteRepository $dispoRep, Request $request, EleveRepository $eleveRep)
    {
        $eleve = $eleveRep->find($this->getUser()->getId());
        if ($eleve->getForfait()) {

            $donnees = $request->get('listeDispos');
            $em = $this->getDoctrine()->getManager();
            foreach ($donnees as $donnee) {
                $libre = true;
                if (
                    isset($donnee['start']) && !empty($donnee['start']) &&
                    isset($donnee['end']) && !empty($donnee['end'])
                ) {

                    if (!isset($donnee['id']) || empty($donnee['id'])) {

                        $disponibilite = new Disponibilite();
                    } else {
                        $disponibilite = $dispoRep->find($donnee['id']);
                    }


                    $allDispoEleve = $dispoRep->findBy(array('eleve' => $eleve));

                    $disponibilite->setEleve($eleve);
                    $disponibilite->setStart(new DateTime($donnee['start']));
                    $disponibilite->setEnd(new DateTime($donnee['end']));
                    foreach ($allDispoEleve as $dispo) {
                        if (($disponibilite->getStart() == $dispo->getStart() && $disponibilite->getEnd() == $dispo->getEnd()) || $disponibilite->getStart() < $dispo->getStart() && $disponibilite->getEnd() > $dispo->getStart()) {
                            $libre = false;
                        }
                    }
                    if ($libre) {
                        $em->persist($disponibilite);
                    }
                } else {

                    return new Response('Données incomplètes', 404);
                }
            }
            $em->flush();
            return new Response('Ok', 200);
        } else {
            return new Response('Il est actuellement interdit de prendre rendez-vous par vous meme.', 403);
        }
    }

    /**
     * @Route("/eleve/{id}/majRdv/{cancel}", name="eleve.majRdv", methods={"PUT"})
     */
    public function majRdv(SettingsRepository $settingsRepo,  Appointment $rdv, $cancel, Request $request)
    {
        $eleve = $this->security->getUser();
        if ($eleve->getForfait()) {


            $setting = $settingsRepo->find(1);
            $setting = $setting->getAuto();
            if ($setting) {

                $em = $this->getDoctrine()->getManager();
                if ($rdv) {
                    if ($cancel) {
                        $rdv->setCancelled(true);
                    } else {
                        $rdv->setCancelled(false);

                        $indispo = new Indisponibilite();

                        $indispo->setMoniteur($rdv->getMoniteur());
                        $indispo->setStart(new DateTime($rdv->getStart()->format('Y-m-d\TH:i:s.u')));
                        $indispo->setEnd(new DateTime($rdv->getEnd()->format('Y-m-d\TH:i:s.u')));

                        $em->persist($indispo);
                    }
                }


                $em->persist($rdv);
                $em->flush();

                return new Response('Ok', 200);
            } else {
                return new Response('Interdit', 403);
            }
        } else {
            return new Response('Il est actuellement interdit de prendre rendez-vous par vous meme.', 403);
        }
    }
    /**
     * @Route("/eleve/{id}/deleteIndispo", name="eleve.deleteIndispo", methods={"DELETE"})
     */
    public function deleteIndispo(SettingsRepository $settingsRepo, Indisponibilite $indispo)
    {
        $eleve = $this->security->getUser();
        if ($eleve->getForfait()) {

            $setting = $settingsRepo->find(1);
            $setting = $setting->getAuto();
            if ($setting) {


                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($indispo);
                $entityManager->flush();

                return new Response('Ok', 200);
            } else {
                return new Response('Interdit', 403);
            }
        } else {
            return new Response('Il est actuellement interdit de prendre rendez-vous par vous meme.', 403);
        }
    }

    /**
     * @Route("/eleve/deleteDispo/{id}", name="eleve.deleteDispo", methods={"DELETE"})
     */
    public function deleteDispo(Disponibilite $disponibilite)
    {
        $eleve = $this->security->getUser();
        if ($eleve->getForfait()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($disponibilite);
            $entityManager->flush();

            return new Response('Ok', 200);
        } else {
            return new Response('Il est actuellement interdit de prendre rendez-vous par vous meme.', 403);
        }
    }

    /**
     * @Route("/eleve/newDispo", name="eleve.newDispo", methods={"PUT"})
     */
    public function newDispo(Request $request, DisponibiliteRepository $dispoRepo)
    {
        $eleve = $this->security->getUser();
        if ($eleve->getForfait()) {
            $id = $request->get('id');
            $start = new Datetime($request->get('start'));
            $end = new Datetime($request->get('end'));

            $em = $this->getDoctrine()->getManager();

            if (
                isset($start) && !empty($start) &&
                isset($end) && !empty($end)
            ) {
                if (!isset($id) || empty($id)) {
                    $dispo = new Disponibilite();
                } else {
                    $dispo = $dispoRepo->find($id);
                }
                $dispo->setStart($start);
                $dispo->setEnd($end);
                $dispo->setEleve($eleve);
                $em->persist($dispo);
                $em->flush();
                return new Response('Ok', 200);
            } else {
                return new Response('Infos manquantes', 404);
            }
        } else {
            return new Response('Il est actuellement interdit de prendre rendez-vous par vous meme.', 403);
        }
    }


    /**
     * @Route("/eleve/{id}/deleteRdv", name="eleve.deleteRdv", methods={"DELETE"})
     */
    public function deleteRdv(Appointment $rdv)
    {
        $eleve = $this->security->getUser();
        if ($eleve->getForfait()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($rdv);
            $entityManager->flush();

            return $this->redirectToRoute('eleve.calendar');
        } else {
            return new Response('Il est actuellement interdit de prendre rendez-vous par vous meme.', 403);
        }
    }
    /**
     * @Route("/eleve/heure", name="eleve.heure", methods={"PUT"})
     */
    public function heure(EleveRepository $eleveRepo, Request $request): Response
    {
        $eleve = $this->security->getUser();
        if ($eleve->getForfait()) {

            $incremente = filter_var($request->get('incremente'), FILTER_VALIDATE_BOOLEAN);
            $nbHeure = $request->get('nbHeure');
            $eleve = $eleveRepo->find($this->getUser()->getId());



            if ($incremente == false) {
                $heure = $eleve->getCompteurHeure() - $nbHeure;
            } else if ($incremente == true) {
                $heure = $eleve->getCompteurHeure() + $nbHeure;
            }

            $eleve->setCompteurHeure($heure);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('eleve.calendar');
        } else {
            return new Response('Il est actuellement interdit de prendre rendez-vous par vous meme.', 403);
        }
    }

    /**
     * @Route("/eleve/getAuto", name="eleve.getAuto", methods={"GET"})
     */
    public function getAuto(SettingsRepository $settingsRepo): Response
    {
        $setting = $settingsRepo->find(1);
        $setting = $setting->getAuto();

        return new JsonResponse(array('auto' => $setting));
    }

    /**
     * @Route("/eleve/getCompteur", name="eleve.getCompteur", methods={"GET"})
     */
    public function getCompteur(EleveRepository $eleveRepo): Response
    {

        $compteur = $eleveRepo->findCompteur($this->getUser()->getId());

        return new JsonResponse(array('compteur' => $compteur));
    }

    public function getExamen()
    {
        $eleveRepo = $this->getDoctrine()->getRepository(Eleve::class);
        $eleve = $eleveRepo->find($this->getUser()->getId());
        $dateExam = new Datetime();
        $examens = $eleve->getExamens();
        foreach ($examens as $examen) {
            if ($examen->getStart() > $dateExam) {
                $dateExam = $examen->getStart();
            }
        }
        return  $dateExam;
    }

    /**
     * @Route("/eleve/getAll/{modeRdv}", name="eleve.getAll"), methods={"GET"})
     */
    public function getAll($modeRdv, AppointmentRepository $rdvRepo, MoniteurRepository $monoRepo, DisponibiliteRepository $dispoRepo, EleveRepository $eleveRepo, IndisponibiliteRepository $indispoRepo, LieuRepository $lieuRepo): Response
    {

        $eleve = $eleveRepo->findOneById($this->getUser()->getId());
        if ($eleve->getForfait()) {


            //RECUP RDV DE L'ELEVE CONNECTE
            $appointments = $rdvRepo->Eleve($eleve);

            $listeRDVs = [];

            foreach ($appointments as $appointment) {
                $moniteurRDV = $monoRepo->find($appointment->getMoniteur());
                $LieuRDV = $lieuRepo->find($appointment->getLieu());
                $title = 'Lieu : ' . $LieuRDV->getLibelle();
                $color = "#83DE7C";

                if ($appointment->getMotif()) {

                    if ($appointment->getMotif()->getId() == 1) {
                        $title = 'ABSENCE';
                        $color = '#e00000';
                    }
                }
                if ($appointment->getCancelled()) {
                    $etat = 'annule';
                    $color = '#FF0000';
                } else {
                    $etat = '';
                }

                $listeRDVs[] = [

                    'id' => $appointment->getId(),
                    'title' => $title,
                    'moniteur_id' => $moniteurRDV->getId(),
                    'description' => $moniteurRDV->getPrenom() . " " . $moniteurRDV->getNom(),
                    'type' => 'rdv',
                    'start' => $appointment->getStart()->format('Y-m-d H:i:s'),
                    'end' => $appointment->getEnd()->format('Y-m-d H:i:s'),
                    'backgroundColor' => $color,
                    'borderColor' => $color,
                    'textColor' => '#000000',
                    'display' => $modeRdv ? 'block' : 'background',
                    'lieu' => $LieuRDV->getId(),
                    'etat' => $etat

                ];
            }

            $dispos = $dispoRepo->Eleve($eleve);
            $listeDispos = [];

            foreach ($dispos as $dispo) {

                $listeDispos[] = [

                    'id' => $dispo->getId(),
                    'title' => 'Dispo',
                    'start' => $dispo->getStart()->format('Y-m-d H:i:s'),
                    'end' => $dispo->getEnd()->format('Y-m-d H:i:s'),
                    'backgroundColor' => '#20a0ff',
                    'borderColor' => '#20a0ff',
                    'textColor' => '#000000',
                    'display' => $modeRdv ? 'background' : 'block',
                    'type' => 'dispo',

                ];
            }

            $toutesIndispos = $indispoRepo->findAll();

            $indispos = [];
            foreach ($toutesIndispos as $indispo) {
                $moniteurIndispo = $monoRepo->find($indispo->getMoniteur());
                $indispos[] =
                    [
                        'id' => $indispo->getId(),
                        'moniteur' => $indispo->getMoniteur(),
                        'moniteur_id' => $moniteurIndispo->getId(),
                        'start' => $indispo->getStart()->format('Y-m-d H:i:s'),
                        'end' => $indispo->getEnd()->format('Y-m-d H:i:s'),
                        'color' => 'grey',
                        'display' => 'background',
                        'type' => 'indispo',

                    ];
            }

            $examens = $eleve->getExamens();
            $examenEleve = [];
            foreach ($examens as $examen) {

                $examenEleve[] = [
                    'id' => $examen->getId(),
                    'title' => 'Lieu : ' . $examen->getLieu()->getLibelle(),
                    'start' => $examen->getStart()->format('Y-m-d H:i:s'),
                    'end' => $examen->getEnd()->format('Y-m-d H:i:s'),
                    'backgroundColor' => '#FF1493',
                    'borderColor' => '#FF1493',
                    'textColor' => 'black',
                    'display' => 'block',
                    'type' => 'exam',
                ];
            }
        }
        $eventEleve = array_merge($listeRDVs, $listeDispos, $examenEleve);

        return new JsonResponse(array('eventEleve' => $eventEleve, 'indispos' => $indispos));
    }

    /**
     * @Route("/eleve/getHeureCommune", name="eleve.getHeureCommune", methods={"GET"})
     *
     */
    public function getHeureCommune($debutPlageHoraire = null, $finPlageHoraire = null, Request $request = null)
    {

        $monos = [];

        ($debutPlageHoraire != null) ? $debutPlageHoraire : $debutPlageHoraire = new DateTime($request->get('debutPlageHoraire'));
        ($finPlageHoraire != null) ? $finPlageHoraire : $finPlageHoraire = new DateTime($request->get('finPlageHoraire'));
        $eleveRepo = $this->getDoctrine()->getRepository(Eleve::class);
        $monoRepo = $this->getDoctrine()->getRepository(Moniteur::class);
        $dispoRepo = $this->getDoctrine()->getRepository(Disponibilite::class);
        $indispoRepo = $this->getDoctrine()->getRepository(Indisponibilite::class);

        $eleve = $eleveRepo->findOneById($this->getUser()->getId());
        $dispos = $dispoRepo->Eleve($eleve);
        $moniteurs = $monoRepo->findAll();

        $i = 0;

        foreach ($moniteurs as $moniteur) {

            $indisposMoniteurs = $indispoRepo->Indispo($moniteur);
            $nbHeureCommun = 0;
            $i++;
            foreach ($dispos as $dispo) {

                $finDispo = $dispo->getEnd();
                $debutPDispo = new DateTime($dispo->getStart()->format('Y-m-d\TH:i:s.u'));

                $finPDispo = new DateTime($dispo->getStart()->format('Y-m-d\TH:i:s.u'));
                $debutPDispo->modify('-30 minutes');

                while ($debutPDispo != $finDispo && $finPDispo < $finDispo) {
                    $debutPDispo->modify("+30 minutes");
                    $finPDispo->modify("+30 minutes");

                    if ($debutPDispo >= $debutPlageHoraire && $finPDispo <= $finPlageHoraire) {
                        foreach ($indisposMoniteurs as $indispoMoniteur) {
                            $finIndispo = $indispoMoniteur->getEnd();
                            $debutPIndispo = new DateTime($indispoMoniteur->getStart()->format('Y-m-d\TH:i:s.u'));

                            $finPIndispo = new DateTime($indispoMoniteur->getStart()->format('Y-m-d\TH:i:s.u'));
                            $debutPIndispo->modify('-30 minutes');
                            while ($debutPIndispo != $finIndispo && $finPIndispo < $finIndispo) {
                                $debutPIndispo->modify("+30 minutes");
                                $finPIndispo->modify("+30 minutes");
                                if ($debutPIndispo == $debutPDispo && $finPIndispo == $finPDispo && $nbHeureCommun > 0) {
                                    $nbHeureCommun -= 0.5;
                                }
                            }
                        }
                        $nbHeureCommun += 0.5;
                    }
                }
            }
            $nbHeureCommun =   sprintf('%02d:%02d', (int) $nbHeureCommun, fmod($nbHeureCommun, 1) * 60) . 'h';
            $monos[] =
                [
                    'id' => $moniteur->getId(),
                    'prenom' => $moniteur->getPrenom(),
                    'nom' => $moniteur->getNom(),
                    'nbHeureCommun' => $nbHeureCommun

                ];
        }

        usort($monos, function ($first, $second) {
            return $first['nbHeureCommun'] < $second['nbHeureCommun'];
        });

        return ($request != null) ?  new JsonResponse($monos) : $monos;
    }


    function getStartAndEndDate($week, $year)
    {
        $dto = new DateTime();
        $ret['week_start'] = $dto->setISODate($year, $week)->format('Y-m-d');
        $ret['week_end'] = $dto->modify('+6 days')->format('Y-m-d');
        return $ret;
    }

    /**
     * @Route("/eleve/convocation", name="eleve.convocation", methods={"GET"})
     */
    public function convocation(): JsonResponse
    {
        $eleve = $this->security->getUser();

        $examens = $eleve->getExamens();
        foreach ($examens as $examenEleve) {

            $examen = $examenEleve;
        }
        $heureExam = $examen->getStart();
        $heureExam->modify("-1 hour");


        // heure -1 d'heure examen
        $infoConvocation = [
            "date" => $examen->getStart()->format('d/m/Y'),
            "lieuExamen" => $examen->getLieu()->getLibelle(),
            "agence" => $eleve->getAgence()->getNomAgence(),
            "prenom" => $eleve->getPrenom(),
            "heureExamen" => $heureExam->format('H:i') . 'h'

        ];
        return new JsonResponse($infoConvocation);
    }
}
