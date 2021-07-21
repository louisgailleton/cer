<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Entity\Moniteur;
use App\Entity\Motif;
use App\Form\AppointmentType;
use App\Form\MoniteurType;
use App\Repository\AppointmentRepository;
use App\Repository\EleveRepository;
use App\Repository\LieuRepository;
use App\Repository\MoniteurRepository;
use App\Repository\MotifRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MoniteurController extends AbstractController
{
    /**
     * @Route("/moniteur", name="moniteur.index")
     */
    public function index(MotifRepository $motifRepo, MoniteurRepository $moniteurRep,  AppointmentRepository $appointmentRep, EleveRepository $eleveRep, LieuRepository $lieuRep): Response
    {

        $moniteur = $moniteurRep->findOneById($this->getUser()->getId());

        $rdvs = $appointmentRep->Moniteur($moniteur);
        $listeRdvs = [];

        foreach ($rdvs as $rdv) {
            if (!$rdv->getCancelled()) {
                $eleveRdv = $eleveRep->find($rdv->getEleve());
                $LieuRDV = $lieuRep->find($rdv->getLieu());
                $title = 'Rendez-vous avec ' . $eleveRdv->getPrenom() . ' ' . $eleveRdv->getNom() . ' à ' . $LieuRDV->getLibelle();
                $color = $moniteur->getColor();
                if ($rdv->getMotif() && $rdv->getMotif()->getId() == 1 ) {
                    $title = $eleveRdv->getPrenom() . ' ' . $eleveRdv->getNom() . ' ABSENT';
                    $color = '#e00000';
                }
                $listeRdvs[] = [

                    'id' => $rdv->getId(),
                    'title' => $title,
                    'start' => $rdv->getStart()->format('Y-m-d H:i:s'),
                    'end' => $rdv->getEnd()->format('Y-m-d H:i:s'),
                    'backgroundColor' => $color,
                    'borderColor' =>  $color,
                    'textColor' => 'black',
                    'display' => 'block',
                    'type' => 'rdv',
                    'done' => $rdv->getDone()

                ];
            }
        }

        $data = json_encode($listeRdvs);
        $motifs = [];
        $motifs = $motifRepo->findAll();
        return $this->render('moniteur/calendar.html.twig', [
            'rdvs' => $data,
            'motifs' => $motifs
        ]);
    }


    /**
     * @Route("/moniteur/{id}/getOptionsRdv", name="moniteur.getOptionsRdv", methods={"GET"})
     */
    public function getOptionsRdv(Appointment $rdv, AppointmentRepository $rdvRepo, MotifRepository $motifRepo)
    {
        $incident = false;
        $other = false;
        $otherMotif = '';
        $otherId = 0;
        $appointment = $rdvRepo->find($rdv);
        if ($appointment->getMotif() != null) {
            $motif = $motifRepo->find($appointment->getMotif());

            if ($motif->getIncident()) {
                $incident = true;
            } else {
                if ($motif->getOther()) {
                    $other = true;
                    $otherMotif = $motif->getLibelle();
                    $otherId = $motif->getId();
                }
            }
            $motif = $motif->getId();
        } else {
            $motif = null;
        }
        $appointmentTable = [];
        $appointmentTable = [
            'id' => $appointment->getId(),
            'done' => $appointment->getDone(),
            'motif' => $motif,
            'incident' => $incident,
            'other' => $other,
            'otherMotif' => $otherMotif,
            'otherId' => $otherId
        ];
        return new JsonResponse($appointmentTable);
    }


    /**
     * @Route("/moniteur/{id}/majRdv", name="moniteur.majRdv", methods={"PUT"})
     */
    public function majRdv(Appointment $rdv, Request $request, AppointmentRepository $rdvRepo, MotifRepository $motifRepo)
    {

        $appointment = $rdvRepo->find($rdv);
        if ($appointment->getDone() != true) {
            $motif = $request->get('motif');
            $done = filter_var($request->get('done'), FILTER_VALIDATE_BOOLEAN);
            $otherMotif = trim($request->get('otherMotif'));
            $otherId = $request->get('otherId');

            if (isset($done) &&  !empty($done)) {
                $appointment->setDone($done);
            }

            if (isset($motif) && !empty($motif)) {
                $motif = $motifRepo->find($request->get('motif'));
                $appointment->setMotif($motif);

                if (isset($otherId) && !empty($otherId)) {
                    $otherMotif = $motifRepo->find($otherId);
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->remove($otherMotif);
                    $entityManager->flush();
                }
            } else if (isset($otherId) && !empty($otherId)) {
                $newMotif = $motifRepo->find($otherId);
                $newMotif->setLibelle($otherMotif);
                $newMotif->setIncident(false);
                $newMotif->setOther(true);

                $em = $this->getDoctrine()->getManager();
                $em->persist($newMotif);
                $em->flush();

                $appointment->setMotif($newMotif);
            } else if (isset($otherMotif) && !empty($otherMotif)) {
                $newMotif = new Motif();
                $newMotif->setLibelle($otherMotif);
                $newMotif->setIncident(false);
                $newMotif->setOther(true);

                $em = $this->getDoctrine()->getManager();
                $em->persist($newMotif);
                $em->flush();


                $lastMotif = $motifRepo->findLastInserted();
                $appointment->setMotif($lastMotif);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($appointment);
            $em->flush();
        }

        $status =  $appointment->getDone();


        return new JsonResponse($status);
    }


    /**
     * @Route("/new", name="moniteur_new", methods={"GET","POST"})
     */
    // public function new(Request $request): Response
    // {
    //     $moniteur = new Moniteur();
    //     $form = $this->createForm(MoniteurType::class, $moniteur);
    //     $form->handleRequest($request);

    // if ($form->isSubmitted() && $form->isValid()) {
    //     $entityManager = $this->getDoctrine()->getManager();
    //     $entityManager->persist($moniteur);
    //     $entityManager->flush();

    //     return $this->redirectToRoute('moniteur_index');
    // }

    //     return $this->render('moniteur/new.html.twig', [
    //         'moniteur' => $moniteur,
    //         'form' => $form->createView(),
    //     ]);
    // }

}
