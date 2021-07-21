<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Entity\Eleve;
use App\Entity\Examen;
use App\Entity\Forfait;
use App\Entity\Indisponibilite;
use App\Entity\Lycee;
use App\Entity\Moniteur;
use App\Entity\IndispoType;
use App\Entity\PlaceExamen;
use App\Entity\Prestation;
use App\Form\ForfaitType;
use App\Form\LyceeType;
use App\Form\PrestationType;
use App\Form\SecretaireEleveType;
use App\Form\SecretairePjType;
use App\Repository\AppointmentRepository;
use App\Repository\DisponibiliteRepository;
use App\Repository\EleveRepository;
use App\Repository\ExamenRepository;
use App\Repository\ForfaitRepository;
use App\Repository\IndisponibiliteRepository;
use App\Repository\IndispoTypeRepository;
use App\Repository\LieuRepository;
use App\Repository\LyceeRepository;
use App\Repository\MoniteurRepository;
use App\Repository\PlaceExamenRepository;
use App\Repository\PrestationRepository;
use App\Repository\SettingsRepository;
use App\Repository\ShortListRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

use function PHPSTORM_META\type;

class SecretaireController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var Security
     */
    private $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    /**
     * @Route("/secretaire", name="secretaire.index")
     */
    public function index(SettingsRepository $settingsRepo, AppointmentRepository $rdvRepo, IndispoTypeRepository $indispoTypeRepo, MoniteurRepository $monoRepo, EleveRepository $eleveRepo, IndisponibiliteRepository $indispoRepo, ExamenRepository $examenRepo): Response
    {


        $setting = $settingsRepo->find(1);
        $setting = $setting->getAuto();

        $moniteurs = $monoRepo->findAll();
        $monos = [];
        foreach ($moniteurs as $moniteur) {
            $monos[] =
                [
                    'id' => $moniteur->getId(),
                    'username' => $moniteur->getPrenom(),
                    'title' => $moniteur->getPrenom(),
                    'color' => $moniteur->getColor(),

                ];
        }

        $indispos = $indispoRepo->findAll();
        $toutesIndispos = [];

        foreach ($indispos as $indispo) {

            $moniteur = $monoRepo->find($indispo->getMoniteur());
            $toutesIndispos[] =
                [
                    'id' => $indispo->getId(),
                    'moniteur' => $indispo->getMoniteur(),
                    'moniteur_id' => $moniteur->getId(),
                    'resourceId' => $moniteur->getId(),
                    'start' => $indispo->getStart()->format('Y-m-d H:i:s'),
                    'end' => $indispo->getEnd()->format('Y-m-d H:i:s'),
                    'color' => $indispo->getPreExam() ? '#FF69B4' : 'grey',
                    'display' => 'background',
                    'type' => $indispo->getPreExam() ? 'preExam' : 'indispo',

                ];
        }

        $indispoTypes = $indispoTypeRepo->findAll();
        $toutesIndispoTypes = [];

        foreach ($indispoTypes as $indispoType) {

            $moniteur = $monoRepo->find($indispoType->getMoniteur());

            $toutesIndispoTypes[] =
                [
                    'id' => $indispoType->getId(),
                    'moniteur' => $indispoType->getMoniteur(),
                    'moniteur_id' => $moniteur->getId(),
                    'resourceId' => $moniteur->getId(),
                    'daysOfWeek' => $indispoType->getDay(),
                    'startTime' => $indispoType->getStart()->format('H:i'),
                    'endTime' => $indispoType->getEnd()->format('H:i'),
                    'color' => 'grey',
                    'display' => 'background',
                    'type' => 'indispoType',

                ];
        }


        $toutRdvs = [];
        $rdvs = $rdvRepo->findAll();
        foreach ($rdvs as $rdv) {;
            $moniteur = $monoRepo->find($rdv->getMoniteur());

            $eleve = $eleveRepo->find($rdv->getEleve());

            if ($rdv->getCancelled()) {

                $title = $eleve->getPrenom() . ' ' . $eleve->getNom() . ' ABSENT';
                $color = '#e00000';
                $absence = true;
                $type = 'absence';
            } else {
                $absence = false;
                $type = 'rdv';
                $color = $moniteur->getColor();
                $title = strtoupper($eleve->getNom()) . ' ' . $eleve->getPrenom();
            }
            $toutRdvs[] =
                [
                    'id' => $rdv->getId(),
                    'resourceId' => $moniteur->getId(),
                    'title' => $title,
                    'eleve_id' => $eleve->getId(),
                    'moniteur_id' => $moniteur->getId(),
                    'compteur' => $eleve->getCompteurHeure(),
                    'type' => $type,
                    'lieu' =>  $rdv->getLieu()->getLibelle(),
                    'lieu_id' =>  $rdv->getLieu()->getId(),
                    'start' => $rdv->getStart()->format('Y-m-d H:i:s'),
                    'end' => $rdv->getEnd()->format('Y-m-d H:i:s'),
                    'backgroundColor' => $color,
                    'borderColor' => $color,
                    'textColor' => '#000000',
                    'absence' => $absence
                ];
        }
        $toutExamens = [];
        $preExamens = [];
        $examens = $examenRepo->findAll();
        foreach ($examens as $examen) {
            $moniteur = $monoRepo->find($examen->getMoniteur());

            $toutExamens[] =
                [
                    'id' => $examen->getId(),
                    'resourceId' => $moniteur->getId(),
                    'title' => $examen->getLieu()->getLibelle(),
                    'lieu' => $examen->getLieu()->getLibelle(),
                    'lieu_id' => $examen->getLieu()->getId(),
                    'nbPlace' => $examen->getNbPlaces(),
                    'numero' => $examen->getNumero(),
                    'moniteur_id' => $moniteur->getId(),
                    'type' => 'exam',
                    'start' => $examen->getStart()->format('Y-m-d H:i:s'),
                    'end' => $examen->getEnd()->format('Y-m-d H:i:s'),
                    'backgroundColor' => '#FF1493',
                    'borderColor' => '#FF1493',
                    'textColor' => 'black',
                ];
        }


        $totalIndispo = $indispoRepo->createQueryBuilder('a')
            ->select('count(a.id)')
            ->getQuery()
            ->getSingleScalarResult();
        $tout = array_merge($toutRdvs, $toutesIndispos, $toutExamens, $preExamens, $toutesIndispoTypes);
        $data = json_encode($tout);
        $username  = array_column($monos, 'username');
        array_multisort($username, SORT_ASC, $monos);

        return $this->render('secretaire/index.html.twig', [
            'tout' => $data,
            'moniteurs' => $monos,
            'auto' => $setting,
            'totalIndispo' => $totalIndispo
        ]);
    }

    /**
     * @Route("/secretaire/deleteIndispo/{id}", name="secretaire.deleteIndispo", methods={"DELETE"})
     */
    public function deleteIndispo(Indisponibilite $indispo, IndisponibiliteRepository $indispoRepo)
    {
        $indisponibilite = $indispoRepo->find($indispo);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($indisponibilite);
        $entityManager->flush();

        return new Response('Ok', 200);
    }

    /**
     * @Route("/secretaire/{id}/deleteExam", name="secretaire.deleteExam", methods={"DELETE"})
     */
    public function deleteExam(Examen $exam, IndisponibiliteRepository $indispoRepo)
    {
        $plageTrajet = new DateTime($exam->getStart()->format('Y-m-d H:i:s'));
        $plageTrajet->modify("-40 minutes");
        $debutIndispo = $indispoRepo->findOneBy(array("start" => $plageTrajet, "preExam" => true));
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($exam);
        $entityManager->remove($debutIndispo);
        $entityManager->flush();

        return new Response('Ok', 200);
    }


    /**
     * @Route("/secretaire/deleteIndispoType/{id}", name="secretaire.deleteIndispoType", methods={"DELETE"})
     */
    public function deleteIndispoType(IndispoType $indispoType, IndispoTypeRepository $indispoTypeRepo)
    {
        $indisponibilite = $indispoTypeRepo->find($indispoType);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($indisponibilite);
        $entityManager->flush();

        return new Response('Ok', 200);
    }
    /**
     * @Route("/secretaire/{id}/deleteRdv", name="secretaire.deleteRdv", methods={"DELETE"})
     */
    public function deleteRdv(Appointment $rdv)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($rdv);
        $entityManager->flush();

        return new Response('Ok', 200);
    }

    /**
     * @Route("/secretaire/newIndispo", name="secretaire.newIndispo", methods={"PUT"})
     */
    public function newIndispo(Request $request, MoniteurRepository $monoRepo, IndisponibiliteRepository $indispoRepo)
    {

        $id = $request->get('id');
        $moniteur = $request->get('moniteur');
        $start = new Datetime($request->get('start'));
        $end = new Datetime($request->get('end'));

        $em = $this->getDoctrine()->getManager();

        if (
            isset($moniteur) && !empty($moniteur) &&
            isset($start) && !empty($start) &&
            isset($end) && !empty($end)

        ) {

            $moniteur = $monoRepo->find($moniteur);

            if (!isset($id) || empty($id)) {
                $indispo = new Indisponibilite();
            } else {

                $indispo = $indispoRepo->find($id);
            }
            $indispo->setMoniteur($moniteur);
            $indispo->setStart($start);
            $indispo->setEnd($end);
            $em->persist($indispo);
            $em->flush();
        }
        return new Response('Ok', 200);
    }

    /**
     * @Route("/secretaire/newIndispoType", name="secretaire.newIndispoType", methods={"PUT"})
     */
    public function newIndispoType(Request $request, MoniteurRepository $monoRepo, IndispoTypeRepository $indispoTypeRepo)
    {
        $donnees = $request->get('listeIndispoTypes');
        foreach ($donnees as $donnee) {
            if (!isset($donnee['id']) || empty($donnee['id'])) {
                $indispoType = new IndispoType();
            } else {
                $indispoType = $indispoTypeRepo->find($donnee['id']);
            }
            $moniteur = $monoRepo->find($donnee['moniteur']);

            $indispoType->setMoniteur($moniteur);
            $indispoType->setDay($donnee['day']);
            $indispoType->setStart(new DateTime($donnee['start']));
            $indispoType->setEnd(new DateTime($donnee['end']));


            $em = $this->getDoctrine()->getManager();
            $em->persist($indispoType);
        }
        $em->flush();
        return new Response('Ok', 200);
    }

    /**
     * @Route("/secretaire/{id}/majRdv", name="secretaire.majRdv", methods={"PUT"})
     */
    public function majRdv(Appointment $rdv): Response
    {
        $rdv->setCancelled(true);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();
        return new Response('Ok', 200);
    }

    /**
     * @Route("/secretaire/heureEleve", name="secretaire.heureEleve", methods={"PUT"})
     */
    public function heure(EleveRepository $eleveRepo, Request $request): Response
    {
        $eleve = $request->get('eleve');
        $incremente = filter_var($request->get('incremente'), FILTER_VALIDATE_BOOLEAN);

        $nbHeure = $request->get('nbHeure');
        if (
            isset($eleve) && !empty($eleve) &&
            isset($incremente) &&
            isset($nbHeure) && !empty($nbHeure)

        ) {

            $eleve = $eleveRepo->find($eleve);
            if ($incremente == false) {
                $heure = $eleve->getCompteurHeure() - $nbHeure;
            } else if ($incremente == true) {
                $heure = $eleve->getCompteurHeure() + $nbHeure;
            }

            $eleve->setCompteurHeure($heure);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
        }
        return new Response('Ok', 200);
    }


    /**
     * @Route("/secretaire/auto", name="secretaire.auto", methods={"PUT"})
     */
    public function auto(SettingsRepository $settingsRepo, Request $request): Response
    {


        $donnee = $request->get('auto');

        if (
            isset($donnee) && !empty($donnee)
        ) {

            $setting = $settingsRepo->find(1);
            if ($donnee == 'true') {
                $auto = 1;
            } else {
                $auto = 0;
            }

            $setting->setAuto($auto);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
        } else {

            return new Response('Données incomplètes', 404);
        }
        return new Response('Ok', 200);
    }

    /**
     * @Route("/secretaire/getIndispo", name="secretaire.getIndispo", methods={"GET"})
     */
    public function getIndispo(IndisponibiliteRepository $indispoRepo): Response
    {

        $totalIndispo = $indispoRepo->createQueryBuilder('a')

            ->select('count(a.id)')
            ->getQuery()
            ->getSingleScalarResult();

        return new Response($totalIndispo);
    }


    /**
     * @Route("/secretaire/getAll/{modeDispo}", name="secretaire.getAll"), methods={"GET"})
     */
    public function getAll($modeDispo, IndispoTypeRepository $indispoTypeRepo, ExamenRepository $examenRepo, AppointmentRepository $rdvRepo, MoniteurRepository $monoRepo, EleveRepository $eleveRepo, IndisponibiliteRepository $indispoRepo): Response
    {

        $indispos = $indispoRepo->findAll();
        $toutesIndispos = [];


        foreach ($indispos as $indispo) {

            $moniteur = $monoRepo->find($indispo->getMoniteur());
            $toutesIndispos[] =
                [
                    'id' => $indispo->getId(),
                    'moniteur' => $indispo->getMoniteur(),
                    'moniteur_id' => $moniteur->getId(),
                    'resourceId' => $moniteur->getId(),
                    'start' => $indispo->getStart()->format('Y-m-d H:i:s'),
                    'end' => $indispo->getEnd()->format('Y-m-d H:i:s'),
                    'color' => $indispo->getPreExam() ? '#FF69B4' : 'grey',
                    'display' => $modeDispo ? 'block' : 'background',
                    'type' => $indispo->getPreExam() ? 'preExam' : 'indispo',


                ];
        }

        $toutRdvs = [];
        $rdvs = $rdvRepo->findAll();
        foreach ($rdvs as $rdv) {


            $eleveRdv = $eleveRepo->find($rdv->getEleve());
            $moniteur = $monoRepo->find($rdv->getMoniteur());
            if ($rdv->getCancelled()) {

                $title = $eleveRdv->getPrenom() . ' ' . $eleveRdv->getNom() . ' ABSENT';
                $color = '#e00000';
                $absence = true;
                $type = 'absence';
            } else {
                $absence = false;
                $type = 'rdv';
                $color = $moniteur->getColor();
                $title = strtoupper($eleveRdv->getNom()) . ' ' . $eleveRdv->getPrenom();
            }
            $moniteur = $monoRepo->find($rdv->getMoniteur());
            $eleve = $eleveRepo->find($rdv->getEleve());
            $toutRdvs[] =
                [
                    'id' => $rdv->getId(),
                    'resourceId' => $moniteur->getId(),
                    'title' => $title,
                    'eleve_id' => $eleve->getId(),
                    'moniteur_id' => $moniteur->getId(),
                    'compteur' => $eleve->getCompteurHeure(),
                    'type' => $type,
                    'lieu' =>  $rdv->getLieu()->getLibelle(),
                    'lieu_id' =>  $rdv->getLieu()->getId(),
                    'start' => $rdv->getStart()->format('Y-m-d H:i:s'),
                    'end' => $rdv->getEnd()->format('Y-m-d H:i:s'),
                    'backgroundColor' => $color,
                    'borderColor' => $color,
                    'textColor' => '#000000',
                    'absence' => $absence,
                    'display' => $modeDispo ? 'background' : 'block',
                ];
        }

        $toutExamens = [];

        $examens = $examenRepo->findAll();
        foreach ($examens as $examen) {
            $moniteur = $monoRepo->find($examen->getMoniteur());

            $toutExamens[] =
                [
                    'id' => $examen->getId(),
                    'resourceId' => $moniteur->getId(),
                    'title' => $examen->getLieu()->getLibelle(),
                    'lieu' => $examen->getLieu()->getLibelle(),
                    'lieu_id' => $examen->getLieu()->getId(),
                    'nbPlace' => $examen->getNbPlaces(),
                    'numero' => $examen->getNumero(),
                    'moniteur_id' => $moniteur->getId(),
                    'type' => 'exam',
                    'start' => $examen->getStart()->format('Y-m-d H:i:s'),
                    'end' => $examen->getEnd()->format('Y-m-d H:i:s'),
                    'backgroundColor' => '#FF1493',
                    'borderColor' => '#FF1493',
                    'textColor' => 'black',
                    'display' => $modeDispo ? 'background' : 'block',

                ];
        }

        $indispoTypes = $indispoTypeRepo->findAll();
        $toutesIndispoTypes = [];

        foreach ($indispoTypes as $indispoType) {

            $moniteur = $monoRepo->find($indispoType->getMoniteur());

            $toutesIndispoTypes[] =
                [
                    'id' => $indispoType->getId(),
                    'moniteur' => $indispoType->getMoniteur(),
                    'moniteur_id' => $moniteur->getId(),
                    'resourceId' => $moniteur->getId(),
                    'daysOfWeek' => $indispoType->getDay(),
                    'startTime' => $indispoType->getStart()->format('H:i'),
                    'endTime' => $indispoType->getEnd()->format('H:i'),
                    'color' => 'grey',
                    'display' => 'background',
                    'type' => 'indispoType',

                ];
        }


        $tout = array_merge($toutRdvs, $toutesIndispos, $toutExamens, $toutesIndispoTypes);
        // $data = json_encode($tout);

        return new JsonResponse(array('tout' => $tout));
    }

    /**
     * @Route("/secretaire/getDispo", name="secretaire.getDispo", methods={"GET"})
     */
    public function getDispo(DisponibiliteRepository $dispoRepo, EleveRepository $eleveRepo, Request $request): Response
    {
        $eleves = [];

        $start = $request->get('start');
        $end = $request->get('end');
        $start = date_create_from_format('D M d Y H:i:s e+', $start);
        $end = date_create_from_format('D M d Y H:i:s e+', $end);

        if (
            isset($start) && !empty($start) &&
            isset($end) && !empty($end)

        ) {
            $dispos = $dispoRepo->Dispo($start);
            foreach ($dispos as $dispo) {
                $duree = 0;
                $finDispo = $dispo->getEnd();
                $debutPDispo = new DateTime($dispo->getStart()->format('Y-m-d\TH:i:s.u'));

                $finPDispo = new DateTime($dispo->getStart()->format('Y-m-d\TH:i:s.u'));
                $debutPDispo->modify('-30 minutes');

                while ($debutPDispo != $finDispo && $finPDispo < $finDispo) {
                    $debutPDispo->modify("+30 minutes");
                    $finPDispo->modify("+30 minutes");

                    if ($debutPDispo >= $start && $finPDispo <= $end) {
                        $duree += 0.5;
                    }
                }

                $eleve = $eleveRepo->find($dispo->getEleve());
                $HaPoser = $eleve->getHAPoser();
                if ($duree > 0 && $eleve->getCompteurHeure() > 0) {
                    $duree =  sprintf('%02d:%02d', (int) $duree, fmod($duree, 1) * 60);
                    $eleves[] =
                        [
                            'id' => $eleve->getId(),
                            'nom' => $eleve->getNom(),
                            'prenom' => $eleve->getPrenom(),
                            'haposer' => $HaPoser,
                            'tel' => $eleve->getTelephone(),
                            'compteur' => $eleve->getCompteurHeure(),
                            'duree' => $duree

                        ];
                }
            }
        }
        $duree  = array_column($eleves, 'duree');
        $name  = array_column($eleves, 'nom');
        array_multisort($duree, SORT_DESC, $name, SORT_ASC, $eleves);
        return new JsonResponse(array('eleves' => $eleves));
    }

    /**
     * @Route("/secretaire/getLieux", name="secretaire.getLieux", methods={"GET"})
     */
    public function getLieux(LieuRepository $lieuRepo, MoniteurRepository $monoRepo,  Request $request): Response
    {

        $lieux = [];
        if ($request->get('type') == "moniteur") {
            $moniteur = $monoRepo->find($request->get('id'));
            $listeLieux =  $lieuRepo->LieuxMoniteur($moniteur);
            foreach ($listeLieux as $lieu) {

                $lieux[] =
                    [
                        'id' => $lieu->getId(),
                        'libelle' => $lieu->getLibelle(),

                    ];
            }
        } else if ($request->get('type') == "exam") {
            $listeLieux =  $lieuRepo->LieuxExamen();
            foreach ($listeLieux as $lieu) {

                $lieux[] =
                    [
                        'id' => $lieu->getId(),
                        'libelle' => $lieu->getLibelle(),

                    ];
            }
        }

        return new JsonResponse(array('lieux' => $lieux));
    }

    /**
     * @Route("/secretaire/getIndispoType", name="secretaire.getIndispoType", methods={"GET"})
     */
    public function getIndispoType(IndispoTypeRepository $indispoTypeRepo, MoniteurRepository $monoRepo,  Request $request): Response
    {

        $indisposType = [];
        $moniteur = $monoRepo->find($request->get('id'));
        $listeIndisposType =  $indispoTypeRepo->indispoTypeMoniteur($moniteur);
        foreach ($listeIndisposType as $indispo) {
            $indisposType[] =
                [
                    'id' => $indispo->getId(),
                    'start' => $indispo->getStart(),
                    'end' => $indispo->getEnd(),
                    'jour' => $indispo->getDay(),

                ];
        }
        return new JsonResponse(array('indisposType' => $indisposType));
    }



    /**
     * @Route("/secretaire/checkRdvInIndispo/{id}", name="secretaire.checkRdvInIndispo", methods={"GET"})
     */
    public function checkRdvInIndispo(Moniteur $moniteur, Request $request, AppointmentRepository $rdvRepo, EleveRepository $eleveRepo): Response
    {
        $listeIndispoTypes = $request->get('listeIndispoTypes');
        $listesRdv = [];
        $listeRdvMoniteurs = $rdvRepo->findBy(array("moniteur" => $moniteur));
        $alreadyIn = false;

        foreach ($listeRdvMoniteurs as $listeRdvMoniteur) {
            if ($listeRdvMoniteur->getStart() >= new DateTime()) {
                foreach ($listeIndispoTypes as $listeIndispoType) {
                    $listeIndispoType['start'] = new DateTime($listeIndispoType['start']);
                    $listeIndispoType['end'] = new DateTime($listeIndispoType['end']);
                    if (
                        strtotime($listeRdvMoniteur->getStart()->format('H:i')) == strtotime($listeIndispoType['start']->format('H:i'))
                        || strtotime($listeRdvMoniteur->getEnd()->format('H:i')) == strtotime($listeIndispoType['end']->format('H:i'))

                        || (strtotime($listeRdvMoniteur->getStart()->format('H:i')) > strtotime($listeIndispoType['start']->format('H:i'))
                            && strtotime($listeRdvMoniteur->getStart()->format('H:i')) < strtotime($listeIndispoType['end']->format('H:i')))

                        || (strtotime($listeRdvMoniteur->getEnd()->format('H:i')) < strtotime($listeIndispoType['end']->format('H:i'))
                            && strtotime($listeRdvMoniteur->getEnd()->format('H:i')) > strtotime($listeIndispoType['start']->format('H:i')))
                    ) {
                        foreach ($listesRdv as $rdv) {
                            if ($rdv['id'] == $listeRdvMoniteur->getId()) {
                                $alreadyIn = true;
                            }
                        }
                        if (!$alreadyIn) {
                            $listesRdv[] =
                                [
                                    'id' => $listeRdvMoniteur->getId(),
                                    'eleve_id' => $listeRdvMoniteur->getEleve()->getId(),
                                    'start' => $listeRdvMoniteur->getStart()->format('Y-m-d H:i:s'),
                                    'end' => $listeRdvMoniteur->getEnd()->format('Y-m-d H:i:s'),
                                    'date' => $listeRdvMoniteur->getStart()->format('d/m/Y H:i') . "h -" . $listeRdvMoniteur->getEnd()->format('H:i') . "h",
                                    'eleve' => strtoupper($listeRdvMoniteur->getEleve()->getNom()) . " " . $listeRdvMoniteur->getEleve()->getPrenom(),
                                    'telEleve' => $listeRdvMoniteur->getEleve()->getTelephone(),
                                ];
                        }
                    }
                }
            }
        }
        return new JsonResponse($listesRdv);
    }
    /**
     * @Route("/secretaire/getExam", name="secretaire.getExam", methods={"GET"})
     */
    public function getExam(EleveRepository $eleveRepo): Response
    {

        $elevesExam = [];

        $eleves = $eleveRepo->findAll();
        foreach ($eleves as $eleve) {
            foreach ($eleve->getExamens() as $examen) {
                if (new DateTime($examen->getStart()->format('Y-m-d')) > new DateTime(date('Y-m-d'))) {
                    if ($examen->getEleves() && $eleve->getCompteurHeure() > 0) {

                        $elevesExam[] =
                            [
                                'id' => $eleve->getId(),
                                'nom' => $eleve->getNom(),
                                'prenom' => $eleve->getPrenom(),
                                'haposer' => $eleve->getHAPoser(),
                                'date' => $examen->getStart()->format('d/m/Y'),
                                'tel' => $eleve->getTelephone(),
                                'compteur' => $eleve->getCompteurHeure(),

                            ];
                    }
                }
            }
        }
        $name  = array_column($elevesExam, 'nom');
        array_multisort($name, SORT_ASC, $elevesExam);
        return new JsonResponse(array('elevesExam' => $elevesExam));
    }

    /**
     * @Route("/secretaire/getSl", name="secretaire.getSl", methods={"GET"})
     */
    public function getSl(ShortListRepository $slRepo): Response
    {

        $elevesSl = [];

        $shortlists = $slRepo->findAll();
        foreach ($shortlists as $shortlist) {
            $eleves = $shortlist->getEleves();

            foreach ($eleves as $eleve) {
                $HaPoser =  $eleve->getHAPoser();
                if ($eleve->getCompteurHeure() > 0) {

                    $elevesSl[] =
                        [
                            'id' => $eleve->getId(),
                            'nom' => $eleve->getNom(),
                            'haposer' => $HaPoser,
                            'prenom' => $eleve->getPrenom(),
                            'tel' => $eleve->getTelephone(),
                            'compteur' => $eleve->getCompteurHeure(),

                        ];
                }
            }
        }


        $name  = array_column($elevesSl, 'nom');
        array_multisort($name, SORT_ASC, $elevesSl);

        return new JsonResponse(array('elevesSl' => $elevesSl));
    }


    /**
     * @Route("/secretaire/newRdv", name="secretaire.newRdv", methods={"PUT"})
     */
    public function newRdv(Request $request, EleveRepository $eleveRep, MoniteurRepository $moniteurRep, LieuRepository $lieuRep, AppointmentRepository $appointmentRepo, IndisponibiliteRepository $indispoRepo): Response
    {

        $id = $request->get('id');
        $eleve = $request->get('eleve');
        $moniteur = $request->get('moniteur');
        $start = new Datetime($request->get('start'));
        $end = new Datetime($request->get('end'));
        $lieu = $request->get('lieu');

        $em = $this->getDoctrine()->getManager();

        if (
            isset($eleve) && !empty($eleve) &&
            isset($moniteur) && !empty($moniteur) &&
            isset($start) && !empty($start) &&
            isset($end) && !empty($end) &&
            isset($lieu) && !empty($lieu)

        ) {

            $eleve = $eleveRep->find($eleve);
            $moniteur = $moniteurRep->find($moniteur);

            $lieu = $lieuRep->find($lieu);
            if ($lieuRep->Check($moniteur, $lieu)) {

                if (!isset($id) || empty($id)) {
                    $appointment = new Appointment();
                    $indispo = new Indisponibilite();
                } else {

                    $appointment = $appointmentRepo->find($id);
                    $indispo = $indispoRepo->findOneBy(array('start' => $appointment->getStart(), 'end' => $appointment->getEnd(), 'moniteur' => $appointment->getMoniteur()));
                }

                $appointment->setEleve($eleve);
                $appointment->setMoniteur($moniteur);
                $appointment->setStart($start);
                $appointment->setEnd($end);
                $appointment->setLieu($lieu);
                $appointment->setCancelled(false);
                $appointment->setDone(false);

                $em->persist($appointment);
                $indispo->setMoniteur($moniteur);
                $indispo->setStart($start);
                $indispo->setEnd($end);

                $em->persist($indispo);
                $em->flush();
            }
        }



        return new Response('Ok', 200);
    }


    /**
     * @Route("/secretaire/searchEleve", name="secretaire.searchEleve", methods={"GET"})
     */
    public function searchAction(Request $request, EleveRepository $eleveRepo)
    {
        $requestString = $request->get('q');
        $eleves =  $eleveRepo->findEleveByString($requestString);
        if (!$eleves) {
            $result = "Aucun élève";
        } else {
            $result = $this->getRealEleves($eleves);
        }

        return new Response(json_encode($result));
    }

    public function getRealEleves($eleves)
    {
        $realEleves = [];
        foreach ($eleves as $eleve) {

            $realEleves[] =
                [
                    'id' => $eleve->getId(),
                    'nom' => $eleve->getNom(),
                    'prenom' => $eleve->getPrenom(),
                ];
        }

        return $realEleves;
    }

    /**
     * @Route("/secretaire/replaceEleve", name="secretaire.replaceEleve", methods={"PUT"})
     */
    public function replaceEleve(Request $request, EleveRepository $eleveRepo, AppointmentRepository $rdvRepo)
    {
        $event = $request->get('event');
        $eleve = $request->get('eleve');

        $event = $rdvRepo->find($event);
        $eleve = $eleveRepo->find($eleve);

        $event->setEleve($eleve);
        $event->setMotif(null);
        $event->setCancelled(false);
        $em = $this->getDoctrine()->getManager();
        $em->persist($event);
        $em->flush();


        return new Response('Ok', 200);
    }

    /**
     * @Route("/secretaire/{id}/getInfoEleve/{modeDispo}", name="secretaire.getInfoEleve", methods={"GET"})
     */
    public function getInfoEleve(Eleve $eleve, $modeDispo, DisponibiliteRepository $dispoRepo, ExamenRepository $examenRepo, IndisponibiliteRepository $indisponibiliteRepository, MoniteurRepository $monoRepo, ShortListRepository $slRepo, AppointmentRepository $appointmentRepo, EleveRepository $eleveRepo): Response
    {
        $infosEleve = [];
        $forfaitsEleve = [];
        $monoShortlist = [];
        $events = [];
        $examensEleve = [];

        $shortlists = $slRepo->findByEleve($eleve);
        foreach ($shortlists as $shortlist) {
            $monoShortlist[] =
                [
                    'moniteur' => $shortlist->getMoniteurs()->getPrenom(),
                ];
        }

        $forfait = $eleve->getForfait();
        if ($forfait) {

            $forfaitsEleve[] =
                [
                    'forfaits' => $forfait->getLibelleforfait(),
                ];
        }

        $infosEleve[] =
            [
                'id' => $eleve->getId(),
                'nom' => $eleve->getNom(),
                'prenom' => $eleve->getPrenom(),
                'compteur' => $eleve->getCompteurHeure(),
                'haposer' => $eleve->getHAPoser(),

            ];

        $dispos = [];
        $dispos = $dispoRepo->Eleve($eleve);

        foreach ($dispos as $dispo) {
            $events[] =   [
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

        $indispos = $indisponibiliteRepository->findAll();
        foreach ($indispos as $indispo) {

            $moniteur = $monoRepo->find($indispo->getMoniteur());
            $events[] =
                [
                    'id' => $indispo->getId(),
                    'moniteur' => $indispo->getMoniteur(),
                    'moniteur_id' => $moniteur->getId(),
                    'resourceId' => $moniteur->getId(),
                    'start' => $indispo->getStart()->format('Y-m-d H:i:s'),
                    'end' => $indispo->getEnd()->format('Y-m-d H:i:s'),
                    'color' => $indispo->getPreExam() ? '#FF69B4' : 'grey',
                    'display' => $modeDispo ? 'block' : 'background',
                    'type' => $indispo->getPreExam() ? 'preExam' : 'indispo',


                ];
        }

        $rdvs = $appointmentRepo->findAll();
        foreach ($rdvs as $rdv) {
            $eleveRdv = $eleveRepo->find($rdv->getEleve());
            $moniteur = $monoRepo->find($rdv->getMoniteur());
            if ($rdv->getCancelled()) {

                $title = $eleveRdv->getPrenom() . ' ' . $eleveRdv->getNom() . ' ABSENT';
                $color = '#e00000';
                $absence = true;
                $type = 'absence';
            } else {
                $absence = false;
                $type = 'rdv';
                $color = $moniteur->getColor();
                $title = strtoupper($eleveRdv->getNom()) . ' ' . $eleveRdv->getPrenom();
            }
            $moniteur = $monoRepo->find($rdv->getMoniteur());
            $eleve = $eleveRepo->find($rdv->getEleve());
            $events[] =
                [
                    'id' => $rdv->getId(),
                    'resourceId' => $moniteur->getId(),
                    'title' => $title,
                    'eleve_id' => $eleve->getId(),
                    'moniteur_id' => $moniteur->getId(),
                    'compteur' => $eleve->getCompteurHeure(),
                    'type' => $type,
                    'lieu' =>  $rdv->getLieu()->getLibelle(),
                    'lieu_id' =>  $rdv->getLieu()->getId(),
                    'start' => $rdv->getStart()->format('Y-m-d H:i:s'),
                    'end' => $rdv->getEnd()->format('Y-m-d H:i:s'),
                    'backgroundColor' => $color,
                    'borderColor' => $color,
                    'textColor' => '#000000',
                    'absence' => $absence,
                    'display' => $modeDispo ? 'background' : 'block',

                ];
        }

        $examens = $examenRepo->findAll();
        foreach ($examens as $examen) {
            $moniteur = $monoRepo->find($examen->getMoniteur());
            $examensEleve[] =
                [
                    'dateExamen' => $examen->getStart(),
                    'lieuExamen' => $examen->getLieu()->getLibelle()
                ];
            $events[] =
                [
                    'id' => $examen->getId(),
                    'resourceId' => $moniteur->getId(),
                    'title' => $examen->getLieu()->getLibelle(),
                    'lieu' => $examen->getLieu()->getLibelle(),
                    'lieu_id' => $examen->getLieu()->getId(),
                    'nbPlace' => $examen->getNbPlaces(),
                    'numero' => $examen->getNumero(),
                    'moniteur_id' => $moniteur->getId(),
                    'type' => 'exam',
                    'start' => $examen->getStart()->format('Y-m-d H:i:s'),
                    'end' => $examen->getEnd()->format('Y-m-d H:i:s'),
                    'backgroundColor' => '#FF1493',
                    'borderColor' => '#FF1493',
                    'textColor' => 'black',
                    'display' => $modeDispo ? 'background' : 'block',

                ];
        }


        return new JsonResponse(array('events' => $events, 'infoEleve' => $infosEleve, 'forfaits' => $forfaitsEleve, 'examens' => $examensEleve, 'monoShortlist' => $monoShortlist));
    }

    /**
     * @Route("/secretaire/newExam", name="secretaire.newExam", methods={"PUT"})
     */
    public function newExam(Request $request, MoniteurRepository $moniteurRep, LieuRepository $lieuRep, ExamenRepository $examRepo, IndisponibiliteRepository $indispoRepo): Response
    {

        $id = $request->get('id');
        $numero = $request->get('numero');
        $start = $request->get('start');
        $end = $request->get('end');
        $nbPlace = (int)$request->get('nbPlace');
        $lieu = $request->get('lieu');
        $moniteur = $request->get('moniteur');

        $em = $this->getDoctrine()->getManager();

        if (
            isset($moniteur) && !empty($moniteur) &&
            isset($lieu) && !empty($lieu) &&
            isset($numero) && !empty($numero) &&
            isset($start) && !empty($start) &&
            isset($end) && !empty($end)

        ) {
            $lieu = $lieuRep->find($lieu);
            $moniteur = $moniteurRep->find($moniteur);
            if (!isset($id) || empty($id)) {
                $examen = new Examen();
                $indispo = new Indisponibilite();
            } else {
                $examen = $examRepo->find($id);

                $plageTrajet = new DateTime($examen->getStart()->format('Y-m-d H:i:s'));
                $plageTrajet->modify("-40 minutes");
                $indispo = $indispoRepo->findOneBy(array("start" => $plageTrajet, "preExam" => true));
            }

            $startPreExam = new DateTime($start);
            $startPreExam->modify("-40 minutes");
            $endPreExam = new DateTime($end);
            $endPreExam->modify("+40 minutes");
            if ($indispo) {
                $indispo->setStart($startPreExam);
                $indispo->setEnd($endPreExam);
                $indispo->setMoniteur($moniteur);
                $indispo->setPreExam(true);

                $em->persist($indispo);
            }


            $examen->setNumero($numero);
            $examen->setStart(new DateTime($start));
            $examen->setEnd(new DateTime($end));
            $examen->setNbPlaces($nbPlace);
            $examen->setLieu($lieu);
            $examen->setMoniteur($moniteur);

            $em->persist($examen);

            $em->flush();
        }



        return new Response('Ok', 200);
    }

    /**
     * @Route("/secretaire/attributionPlace", name="secretaire.attributionPlace", methods={"POST"})
     */
    public function attributionPlace(Request $request, PlaceExamenRepository $placeExamenRepo, ExamenRepository $examenRepo, EleveRepository $eleveRepo, MoniteurRepository $moniteurRepo, ShortListRepository $slRepo, LieuRepository $lieuRepo, PlaceExamenRepository $placeExamRepo, EntityManagerInterface $em): Response
    {

        $lieu = $request->get('lieu');
        $moniteurs = $moniteurRepo->findAll();
        $nbMoniteur = (int)$moniteurRepo->countMoniteur();
        $moniteursPlace = [];
        $RAW_QUERY = 'SELECT  moniteur.id, COUNT(eleve.short_list_id) AS nbEleve
        FROM `moniteur`
        inner join short_list on moniteur.id = short_list.moniteurs_id
        inner join eleve on short_list.id = eleve.short_list_id
        inner join lieu on eleve.lieu_id = lieu.id
        Where lieu.id = :lieu
        GROUP BY moniteur.id
        ORDER BY COUNT(*) DESC';

        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->bindValue('lieu', $lieu);
        $statement->execute();

        $moniteursSl = $statement->fetchAllAssociative();
        $lieu = $lieuRepo->find($lieu);
        $nbPlaceExamenLieu = $examenRepo->countPlaceLieuCurrentMonth(date('Y-m-01'), date('Y-m-t'), $lieu);
        $nbPlaceAAtribuer = $nbPlaceExamenLieu;

        if ($nbPlaceExamenLieu <= $nbMoniteur) {
            foreach ($moniteurs as $moniteur) {
                $moniteursPlace[] = array('idMono' => $moniteur->getId(), 'nbPlace' => 0);
            }
            while ($nbPlaceExamenLieu > 0) {

                foreach ($moniteursSl as $moniteurSl => $value) {
                    if ($nbPlaceExamenLieu > 0) {

                        foreach ($moniteursPlace as $key => $value) {

                            if ($moniteursPlace[$key]['idMono'] == $moniteursSl[$moniteurSl]['id']) {
                                $moniteursPlace[$key]['nbPlace']++;
                            }
                        }
                        $nbPlaceExamenLieu--;
                    }
                }
            }
        } else {


            $nbEleveLieu = $eleveRepo->countEleveLieu($lieu);

            foreach ($moniteurs as $moniteur) {
                $nbEleveSlLieu = $eleveRepo->countEleveSlLieu($moniteur, $lieu);
                $pourcentage = round(($nbEleveSlLieu * 100) / $nbEleveLieu);
                $moniteursPourcentage[] = array('idMono' => $moniteur->getId(), 'pourcentage' => $pourcentage);
            }

            $pourcentage  = array_column($moniteursPourcentage, 'pourcentage');
            array_multisort($pourcentage, SORT_ASC, $moniteursPourcentage);
            $numItems = count($moniteursPourcentage);
            $i = 0;
            foreach ($moniteursPourcentage as $moniteurPourcentage) {
                if (++$i === $numItems) {
                    $moniteursPlace[] = array('idMono' => $moniteurPourcentage['idMono'], 'nbPlace' => round($nbPlaceAAtribuer));
                } else {
                    $nbPlace = ($moniteurPourcentage['pourcentage'] / 100) * $nbPlaceExamenLieu;
                    $nbPlaceAAtribuer -= round($nbPlace);
                    $moniteursPlace[] = array('idMono' => $moniteurPourcentage['idMono'], 'nbPlace' => round($nbPlace));
                }
            }
        }



        foreach ($moniteursPlace as $moniteurPlace) {
            $moniteur = $moniteurRepo->find($moniteurPlace['idMono']);
            $alreadyExist = (int)$placeExamenRepo->checkExistingPlace($moniteur, $lieu);
            if ($alreadyExist === 0) {
                $placeExamen = new PlaceExamen();

                $placeExamen->setLieu($lieu);
                $placeExamen->setMoniteur($moniteur);
                $placeExamen->setNbPlaceAttribuee($moniteurPlace['nbPlace']);

                $em = $this->getDoctrine()->getManager();
                $em->persist($placeExamen);
                $em->flush();
            } else {

                $placeExamen = $placeExamenRepo->findOneBy(array('lieu' => $lieu, 'moniteur' => $moniteur));
                $placeExamen->setLieu($lieu);
                $placeExamen->setMoniteur($moniteur);
                $placeExamen->setNbPlaceAttribuee($moniteurPlace['nbPlace']);


                $em = $this->getDoctrine()->getManager();
                $em->persist($placeExamen);
                $em->flush();
            }



            // sinon, Faire l'inscription dans la table placeExamen

        }

        return new JsonResponse($moniteursPlace);
    }

    /**
     * @Route("/secretaire/elevesListe", name="secretaire.listeEleves")
     */
    public function listeEleves(Request $request, EleveRepository $er): Response
    {
        $secretaire = $this->security->getUser();
        $listeEleves = $secretaire->getAgence()->getEleves();

        $form = $this->createForm(SecretaireEleveType::class);
        $form->handleRequest($request);

        $formPj = $this->createForm(SecretairePjType::class);
        $formPj->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $idEleve = $form->get('id')->getData();
            $eleve = $er->findOneBy(['id' => $idEleve]);

            $mail = $form->get('mail')->getData();
            $verifEleve = $er->findOneBy(['mail' => $mail]);

            if($eleve === $verifEleve) {
                $eleve->setPrenom($form->get('prenom')->getData());
                $eleve->setNom($form->get('nom')->getData());
                $eleve->setAutrePrenoms($form->get('autrePrenoms')->getData());
                $eleve->setNomUsage($form->get('nomUsage')->getData());
                $eleve->setMail($form->get('mail')->getData());
                $eleve->setDateNaiss($form->get('dateNaiss')->getData());
                $eleve->setTelephone($form->get('telephone')->getData());
                $eleve->setTelParent($form->get('telParent')->getData());
                $eleve->setAdresse($form->get('adresse')->getData());
                $eleve->setVille($form->get('ville')->getData());
                $eleve->setCp($form->get('cp')->getData());
                $eleve->setPaysNaiss($form->get('paysNaiss')->getData());
                $eleve->setDepNaiss($form->get('depNaiss')->getData());
                $eleve->setVilleNaiss($form->get('villeNaiss')->getData());
                $eleve->setStatutSocial($form->get('statutSocial')->getData());
                $eleve->setLunette($form->get('lunette')->getData());
                $eleve->setLycee($form->get('lycee')->getData());
                $eleve->setLyceeAutre($form->get('lyceeAutre')->getData());
                $eleve->setMetier($form->get('metier')->getData());
                $eleve->setNomSociete($form->get('nomSociete')->getData());

                if ($form->get('statutSocial')->getData() != "Lycéen.ne") {
                    $eleve->setLycee("");
                    $eleve->setLyceeAutre("");
                }
                if ($form->get('statutSocial')->getData() != "Salarié.e") {
                    $eleve->setMetier("");
                    $eleve->setNomSociete("");
                }
                $this->em->persist($eleve);
                $this->em->flush();

                // On créer un nouveau form dans lequel on envoie l'élève afin de l'afficher directement sur la page
                // elevesListe

                $form = $this->createForm(SecretaireEleveType::class, (['eleve' => $eleve]));
                $form->handleRequest($request);

                return $this->render('secretaire/elevesListe.html.twig', [
                    'eleves' => $listeEleves,
                    'form' => $form->createView(),
                    'formPj' => $formPj->createView()
                ]);
            }
            else {
                $form = $this->createForm(SecretaireEleveType::class, ([
                    'eleve' => $eleve
                ]));
                return $this->render('secretaire/elevesListe.html.twig', [
                    'eleves' => $listeEleves,
                    'form' => $form->createView(),
                    'msgErreur' => "L'adresse mail existe déjà !",
                    'formPj' => $formPj->createView()
                ]);
            }
        }

        /*$formPj = $this->createForm(SecretairePjType::class);
        $formPj->handleRequest($request);

        $msgErreurPJ = "";
        if ($formPj->isSubmitted() && $formPj->isValid()) {
            $idEleve = $formPj->get('idEleve')->getData();
            $eleve = $er->findOneBy(['id' => $idEleve]);

            if($eleve != null) {

                $commentaireEphoto = $formPj->get('commentaireEphoto')->getData();
                $commentaireCNIEleve = $formPj->get('commentaireCNIEleve')->getData();
                $commentaireJustifDom = $formPj->get('commentaireJustifDom')->getData();
                $commentairAttestHeb = $formPj->get('commentaireAttestHeb')->getData();
                $commentaireJDC = $formPj->get('commentaireJDC')->getData();
                $commentairePermis = $formPj->get('commentairePermis')->getData();
                $ephoto = $formPj->get('ephoto')->getData();
                $cni = $formPj->get('cni')->getData();
                $justifdom = $formPj->get('justifdom')->getData();
                $attestheb = $formPj->get('attestheb')->getData();
                $attestjdc = $formPj->get('attestjdc')->getData();
                $autrep = $formPj->get('autrep')->getData();
                $valideANTS = $formPj->get('validerANTS')->getData();

                $etatDossier = "3";

                foreach ($pjEleve as $pj) {
                    if($pj->getTypePJ() == "EPHOTO") {
                        if($ephoto == false) {
                            if($commentaireEphoto != "") {
                                $pj->setEtat("1");
                                $etatDossier = "1";
                                $pj->setCommentaire($commentaireEphoto);
                            } else {
                                $this->addFlash("danger", "Vous devez ajouter un commentaire");
                            }
                        }
                    }
                    else if($pj->getTypePJ() == "CNI") {
                        if($cni == false) {
                            if($commentaireCNIEleve != "") {
                                $pj->setEtat("1");
                                $etatDossier = "1";
                                $pj->setCommentaire($commentaireCNIEleve);
                            } else {
                                $this->addFlash("danger", "Vous devez ajouter un commentaire");
                            }
                        }
                    }
                    else if($pj->getTypePJ() == "JUSTIFDOM") {
                        if($justifdom == false) {
                            if($commentaireJustifDom != "") {
                                $pj->setEtat("1");
                                $etatDossier = "1";
                                $pj->setCommentaire($commentaireJustifDom);
                            } else {
                                $this->addFlash("danger", "Vous devez ajouter un commentaire");
                            }
                        }
                    }
                    else if($pj->getTypePJ() == "ATTESHEB") {
                        if($attestheb == false) {
                            if($commentairAttestHeb != "") {
                                $pj->setEtat("1");
                                $etatDossier = "1";
                                $pj->setCommentaire($commentairAttestHeb);
                            } else {
                                $this->addFlash("danger", "Vous devez ajouter un commentaire");
                            }
                        }
                    }
                    else if($pj->getTypePJ() == "JDC") {
                        if($attestjdc == false) {
                            if($commentaireJDC != "") {
                                $pj->setEtat("1");
                                $etatDossier = "1";
                                $pj->setCommentaire($commentaireJDC);
                            } else {
                                $this->addFlash("danger", "Vous devez ajouter un commentaire");
                            }
                        }
                    }
                    else if($pj->getTypePJ() == "AUTREP") {
                        if($autrep == false) {
                            if($commentairePermis != "") {
                                $pj->setEtat("1");
                                $etatDossier = "1";
                                $pj->setCommentaire($commentairePermis);
                            } else {
                                $this->addFlash("danger", "Vous devez ajouter un commentaire");
                            }
                        }
                    }
                    $this->em->persist($pj);
                }
                if($valideANTS == true) {
                    $etatDossier = "4";
                }
                $eleve->setEtatDossier($etatDossier);
                $this->em->persist($eleve);
                $this->em->flush();
                $form = $this->createForm(SecretaireEleveType::class, (['eleve' => $eleve]));
                $form->handleRequest($request);
            } else {
                $msgErreurPJ = "Vous devez choisir un élève avant de valider des pièces jointes";
            }

            return $this->render('secretaire/elevesListe.html.twig', [
                'eleves' => $listeEleves,
                'form' => $form->createView(),
                'formPj' => $formPj->createView(),
                'msgErreurPJ' => $msgErreurPJ,
            ]);

        }*/

        return $this->render('secretaire/elevesListe.html.twig', [
            'eleves' => $listeEleves,
            'form' => $form->createView(),
            /*'formPj' => $formPj->createView(),
            'msgErreurPJ' => $msgErreurPJ,*/
        ]);

    }

    /**
     * @Route("/secretaire/validationPJ", name="secretaire.validationPJ", methods={"GET","POST"})
     */
    public function validationPJ(EleveRepository $er): Response
    {
        $idEleve = $_POST["idEleve"];
        $eleve = $er->findOneBy(['id' => $idEleve]);
        $pjEleve = $eleve->getPiecesjointes();
        $etatDossier = "3";

        foreach ($pjEleve as $pj) {
            $inputPj = $_POST[$pj->getNomFichier() . $pj->getId()];
            if($inputPj == "refuse") {
                $typePj = $pj->getTypePJ();
                $commentaire = $_POST['commentaire' . $typePj];
                if($commentaire != null) {
                    $pj->setEtat("1");
                    $etatDossier = "1";
                    if($typePj == "EPHOTO") {
                        $eleve->setCommentaireEPHOTO($commentaire);
                    } else if($typePj == "CNI") {
                        $eleve->setCommentaireCNI($commentaire);
                    } else if($typePj == "JUSTIFDOM") {
                        $eleve->setCommentaireJUSTIFDOM($commentaire);
                    } else if($typePj == "ATTESHEB") {
                        $eleve->setCommentaireATTESHEB($commentaire);
                    } else if($typePj == "JDC") {
                        $eleve->setCommentaireJDC($commentaire);
                    } else if($typePj == "AUTREP") {
                        $eleve->setCommentaireAUTREP($commentaire);
                    }
                } else {
                    if($pj->getEtat() != "1") {
                        $this->addFlash("danger", "Vous devez ajouter un commentaire pour " . $pj->getTypePJ());
                        return $this->redirectToRoute("secretaire.listeEleves");
                    }
                }
                $this->em->persist($pj);
            } else {
                if(isset($_POST["validerANTS"]) && $_POST["validerANTS"] == "oui") {
                    $etatDossier = "4";
                }
            }
        }
        if($etatDossier == "4") {
            foreach ($pjEleve as $pj) {
                $this->em->remove($pj);
                $nomPJ = $pj->getNomFichierUnique();
                unlink("piecesjointes/" . $nomPJ);
            }
        }

        $eleve->setEtatDossier($etatDossier);
        $this->em->persist($eleve);
        $this->em->flush();

        return $this->redirectToRoute("secretaire.listeEleves");
    }

    /**
     * @Route("/secretaire/lycee", name="secretaire.lycee", methods={"GET"})
     */
    public function lycee(LyceeRepository $lyceeRepository): Response
    {
        return $this->render('secretaire/lycee/index.html.twig', [
            'lycees' => $lyceeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/secretaire/ajouterLycee", name="secretaire.ajoutLycee", methods={"GET","POST"})
     */
    public function ajoutLycee(Request $request): Response
    {
        $lycee = new Lycee();
        $form = $this->createForm(LyceeType::class, $lycee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($lycee);
            $entityManager->flush();

            return $this->redirectToRoute('secretaire.lycee');
        }

        return $this->render('secretaire/lycee/new.html.twig', [
            'lycee' => $lycee,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/secretaire/modificationLycee/{nom}", name="secretaire.modifLycee", methods={"GET","POST"})
     */
    public function edit(Request $request, Lycee $lycee): Response
    {
        $form = $this->createForm(LyceeType::class, $lycee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('secretaire.lycee');
        }

        return $this->render('secretaire/lycee/edit.html.twig', [
            'lycee' => $lycee,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("secretaire/supprLycee/{nom}", name="secretaire.supprLycee", methods={"POST"})
     */
    public function delete(Request $request, Lycee $lycee): Response
    {
        if ($this->isCsrfTokenValid('delete'.$lycee->getNom(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($lycee);
            $entityManager->flush();
        }

        return $this->redirectToRoute('secretaire.lycee');
    }

    /**
     * @Route("/secretaire/forfait", name="secretaire.forfait", methods={"GET"})
     */
    public function forfait(ForfaitRepository $forfaitRepository): Response
    {
        return $this->render('secretaire/forfait/index.html.twig', [
            'forfaits' => $forfaitRepository->findAll(),
        ]);
    }

    /**
     * @Route("/secretaire/ajoutForfait", name="secretaire.nouveauForfait", methods={"GET","POST"})
     */
    public function nouveauForfait(Request $request): Response
    {
        $secretaire = $this->security->getUser();
        $forfait = new Forfait();
        $form = $this->createForm(ForfaitType::class, $forfait);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $forfait->addAgence($secretaire->getAgence());
            $entityManager->persist($forfait);
            $entityManager->flush();

            return $this->redirectToRoute('secretaire.forfait');
        }

        return $this->render('secretaire/forfait/new.html.twig', [
            'forfait' => $forfait,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/secretaire/modificationForfait/{id}", name="secretaire.modifForfait", methods={"GET","POST"})
     */
    public function modifForfait(Request $request, Forfait $forfait): Response
    {
        $secretaire = $this->security->getUser();
        $form = $this->createForm(ForfaitType::class, $forfait);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $forfait->addAgence($secretaire->getAgence());
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('secretaire.forfait');
        }

        return $this->render('secretaire/forfait/edit.html.twig', [
            'forfait' => $forfait,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/secretaire/supprimerForfaitForfait/{id}", name="secretaire.supprForfait", methods={"DELETE"})
     */
    public function supprForfait(Request $request, Forfait $forfait): Response
    {
        if ($this->isCsrfTokenValid('delete'.$forfait->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($forfait);
            $entityManager->flush();
        }

        return $this->redirectToRoute('secretaire.forfait');
    }

    /**
     * @Route("/secretaire/prestation", name="secretaire.prestation", methods={"GET"})
     */
    public function prestation(PrestationRepository $prestationRepository): Response
    {
        return $this->render('secretaire/prestation/index.html.twig', [
            'prestations' => $prestationRepository->findAll(),
        ]);
    }

    /**
     * @Route("/secretaire/nouvellePrestation", name="secretaire.ajoutPrestation", methods={"GET","POST"})
     */
    public function ajoutPrestation(Request $request): Response
    {
        $prestation = new Prestation();
        $form = $this->createForm(PrestationType::class, $prestation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($prestation);
            $entityManager->flush();

            return $this->redirectToRoute('secretaire.prestation');
        }

        return $this->render('secretaire/prestation/new.html.twig', [
            'prestation' => $prestation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/secretaire/prestation/modif/{id}", name="secretaire.modifPrestation", methods={"GET","POST"})
     */
    public function modifPrestation(Request $request, Prestation $prestation): Response
    {
        $form = $this->createForm(PrestationType::class, $prestation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('secretaire.prestation');
        }

        return $this->render('secretaire/prestation/edit.html.twig', [
            'prestation' => $prestation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/secretaire/supprPrestation/{id}", name="secretaire.supprPrestation", methods={"POST"})
     */
    public function supprPrestation(Request $request, Prestation $prestation): Response
    {
        if ($this->isCsrfTokenValid('delete'.$prestation->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($prestation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('secretaire.prestation');
    }


}
