<?php

namespace App\Controller;

use App\Entity\Agence;
use App\Entity\Indisponibilite;
use App\Entity\Lieu;
use App\Entity\Moniteur;
use App\Entity\Gerant;
use App\Form\GerantEditType;
use App\Form\IndisponibiliteType;
use App\Form\LieuType;
use App\Form\MoniteurType;
use App\Repository\IndisponibiliteRepository;
use App\Repository\LieuRepository;
use App\Repository\GerantRepository;
use DateTime;
use Symfony\Component\Security\Core\Security;
use App\Form\GerantFormulaireType;
use App\Form\GerantType;
use App\Form\ModifMdpType;
use App\Repository\AgenceRepository;
use App\Repository\RoleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class GerantController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }


    /**
     * @Route("/gerant", name="gerant.index")
     */
    public function index(AgenceRepository $agenceRepo): Response
    {

        $agences = $agenceRepo->findAll();

        return $this->render('gerant/index.html.twig', [
            'agences' => $agences,
        ]);
    }

    /**
     * @Route("/gerant/new", name="gerant.new")
     */
    public function new(Request $request, RoleRepository $roleRepo, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $gerant = new Gerant();

        $form = $this->createForm(GerantType::class, $gerant);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $role = $roleRepo->findOneById(4);
            $entityManager = $this->getDoctrine()->getManager();
            $gerant->setRole($role);

            $gerant->setMdp(
                $passwordEncoder->encodePassword(
                    $gerant,
                    $form->get('mdp')->getData()
                )
            );

            $entityManager->persist($gerant);
            $entityManager->flush();
            return $this->redirectToRoute('admin.index');
        }

        return $this->render('admin/gerant/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/gerant/editMdp/{id}", name="gerant.editMdp")
     */
    public function editMdp(Gerant $gerant, Request $request, RoleRepository $roleRepo, UserPasswordEncoderInterface $passwordEncoder): Response
    {

        $form = $this->createForm(ModifMdpType::class);
        $entityManager = $this->getDoctrine()->getManager();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $mdpSoumis = $form->get('mdp')->getData();
            $nouveauMdp =  $form->get('nouveauMdp')->getData();


            if (!($passwordEncoder->isPasswordValid($gerant, $mdpSoumis))) {
                $this->addFlash(
                    'danger',
                    'Le mot de passe fourni est incorrect.'
                );
                return $this->render('eleve/modifMdp.html.twig', [
                    'form' => $form->createView(),
                    'gerant' => $gerant,
                ]);
            }
            $gerant->setMdp(
                $passwordEncoder->encodePassword(
                    $gerant,
                    $nouveauMdp
                )
            );

            $this->addFlash(
                'success',
                'Le mot de passe a bien été modifié.'
            );

            $entityManager->persist($gerant);
            $entityManager->flush();
            return $this->redirectToRoute('admin.index');
        }

        return $this->render('admin/gerant/editMdp.html.twig', [
            'form' => $form->createView(),
            'gerant' => $gerant
        ]);
    }

    /**
     * @Route("/gerant/connexion", name="gerant.connexion")
     */
    public function formulaireInscription(Request $request)
    {
        $gerant = $this->security->getUser();
        $form = $this->createForm(GerantFormulaireType::class, $gerant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($gerant);
            $this->em->flush();
            return $this->redirectToRoute('gerant.index');
        }

        return $this->render('gerant/formulaireInscription.html.twig', [
            'gerant' => $gerant,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/gerant/editGerant/{id}", name="gerant.edit")
     */
    public function editGerant(Request $req, Gerant $gerant, UserPasswordEncoderInterface $passwordEncoder)
    {
        $form = $this->createForm(GerantEditType::class, $gerant);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin.index');
        }

        return $this->render('admin/gerant/edit.html.twig', [
            'gerant' => $gerant,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/gerant/deleteGerant/{id}", name="gerant.delete")
     */
    public function deleteGerant(Request $req, Gerant $gerant)
    {
        if (count($gerant->getAgences()) == 0) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($gerant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin.index');
    }


      /**
     * @Route("/gerant/moniteursAgence/{id}", name="gerant.show.moniteursagence")
     */
    public function showMoniteurs(Request $req, Agence $agence)
    {
        $moniteurs = $agence->getMoniteurs();
        return $this->render('admin/moniteur/show.html.twig', [
            'moniteurs' => $moniteurs,
        ]);
    }

}
