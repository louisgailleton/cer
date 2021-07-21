<?php

namespace App\Controller;

use App\Entity\Indisponibilite;
use App\Entity\Lieu;
use App\Entity\Moniteur;
use App\Entity\Agence;
use App\Form\IndisponibiliteType;
use App\Form\LieuType;
use App\Form\MoniteurType;
use App\Repository\IndisponibiliteRepository;
use App\Repository\LieuRepository;
use App\Repository\AgenceRepository;
use DateTime;
use Symfony\Component\Security\Core\Security;
use App\Form\GerantFormulaireType;
use App\Form\ChangeGerantType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class AgenceController extends AbstractController
{
    private $security;
    /**
     * @var EntityManagerInterface 
     */
    private $em;

    public function __construct(AgenceRepository $repository, EntityManagerInterface  $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }


    /**
     * @Route("/admin/agence", name="admin.agence.index")
     */
    public function index(): Response
    {
        $agences = $this->repository->findAll();

        return $this->render('admin/agence/show.html.twig', [
            'agences' => $agences
        ]);
    }
    /**
     * @Route("/admin/agence/acceptRequest/{id}", name="admin.agence.acceptrequest")
     */
    public function acceptRequest(Request $request, Agence $agence): Response
    {
        if ($agence->getDemande() == "Suppression") {
            $agence->setDemande("aucune");
            $this->em->remove($agence);
            $this->em->flush();
        } elseif ($agence->getDemande() == "Changement") {
            $form = $this->createForm(ChangeGerantType::class, $agence);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->em->persist($agence);
                $this->em->flush();
                return $this->redirectToRoute('admin.agence.index');
            }

            return $this->render('admin/agence/editGerant.html.twig', [
                'agence' => $agence,
                'form' => $form->createView()
            ]);
        }

        $agences = $this->repository->findAll();
        return $this->render('admin/agence/show.html.twig', [
            'agences' => $agences,
        ]);
    }

    /**
     * @Route("/agence/new", name="agence.new")
     */
    public function new(Request $req): Response
    {
        return new Response(200);
    }

    /**
     * @Route("/agence/edit/{id}", name="agence.edit")
     */
    public function edit(Request $req, Agence $agence): Response
    {
        return new Response(200);

    }

     /**
     * @Route("/agence/delete/{id}", name="agence.delete")
     */
    public function delete(Request $req, Agence $agence): Response
    {
        return new Response(200);

    }
}
