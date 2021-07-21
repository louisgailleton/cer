<?php

namespace App\Controller;

use App\Repository\EleveRepository;
use App\Repository\LieuRepository;
use App\Repository\MoniteurRepository;
use App\Repository\PlaceExamenRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExamenController extends AbstractController
{
    /**
     * @Route("/examen", name="examen.index")
     */
    public function index(MoniteurRepository $moniteurRepo, LieuRepository $lieuRepo, PlaceExamenRepository $placeExamRepo, EleveRepository $eleveRepo): Response
    {

        $allMoniteurs = $moniteurRepo->findAll();
        $lieuxExamen = $lieuRepo->findBy(array('examen' => true));
        $placeExamen = $placeExamRepo->findAll();
        $reussites = $eleveRepo->findSucceedByMoniteur();
        $moniteurs = [];
        foreach ($allMoniteurs as $moniteur) {
            foreach ($reussites as $reussite) {
                if ($reussite['id'] == $moniteur->getId()) {
                    $moniteurs[$moniteur->getId()] = [
                        'id' => $moniteur->getId(),
                        'prenom' => $moniteur->getPrenom(),
                        'nom' => $moniteur->getNom(),
                        'nbReussite' => $reussite['nbReussite'],
                    ];
                }
            }
        }
        foreach ($allMoniteurs as $moniteur) {
            if (!array_key_exists($moniteur->getId(), $moniteurs)) {
                $moniteurs[$moniteur->getId()] = [
                    'id' => $moniteur->getId(),
                    'prenom' => $moniteur->getPrenom(),
                    'nom' => $moniteur->getNom(),
                    'nbReussite' => 0,
                ];
            }
        }
        return $this->render('examen/index.html.twig', [
            'lieuxExamen' => $lieuxExamen,
            'moniteurs' => $moniteurs,
            'placeExamen' => $placeExamen
        ]);
    }
}
