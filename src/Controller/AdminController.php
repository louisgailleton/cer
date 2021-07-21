<?php

namespace App\Controller;

use App\Entity\Eleve;
use App\Entity\Indisponibilite;
use App\Entity\Lieu;
use App\Entity\Moniteur;
use App\Entity\Gerant;
use App\Entity\Agence;
use App\Entity\PorteOuverte;
use App\Form\GerantEditType;
use App\Form\IndisponibiliteType;
use App\Form\LieuType;
use App\Form\MoniteurType;
use App\Form\GerantType;
use App\Form\PorteOuverteType;
use App\Repository\IndisponibiliteRepository;
use App\Repository\LieuRepository;
use App\Repository\MoniteurRepository;
use App\Repository\GerantRepository;
use App\Repository\AgenceRepository;
use App\Repository\PorteOuverteRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Message;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;

class AdminController extends AbstractController
{
    private $em;
    private $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    /**
     * @Route("/admin", name="admin.index")
     * @param MoniteurRepository $moniteurRep
     * @param GerantRepository $gerantRep
     * @return Response
     */
    public function index(MoniteurRepository $moniteurRep, GerantRepository $gerantRep): Response
    {
        $moniteurs = $moniteurRep->findAll();
        $gerants = $gerantRep->findAll();

        return $this->render('admin/index.html.twig', [
            'moniteurs' => $moniteurs,
            'gerants' => $gerants
        ]);
    }

    /**
     * @Route("/admin/porteOuverte", name="admin.porteOuverte")
     * @param PorteOuverteRepository $pr
     * @return Response
     */
    public function porteOuverte(PorteOuverteRepository $pr): Response
    {
        $portesOuvertes = $pr->findAll();

        return $this->render('admin/porteOuverte/affichage.html.twig', [
            'portesOuvertes' => $portesOuvertes
        ]);
    }

    /**
     * @Route("/admin/ajouterPorteOuverte", name="admin.ajouterPorteOuverte")
     * @param Request $request
     * @return Response
     */
    public function ajouterPorteOuverte(Request $request): Response
    {
        $porteOuverte = new PorteOuverte();

        $form = $this->createForm(PorteOuverteType::class, $porteOuverte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($porteOuverte);
            $this->em->flush();

            return $this->redirectToRoute('admin.porteOuverte');
        }
        return $this->render('admin/porteOuverte/ajout.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/voirPorteOuverte/{id}", name="admin.voirPorteOuverte")
     * @return Response
     */
    public function voirPorteOuverte(PorteOuverte $porteOuverte): Response
    {
        $eleves = $porteOuverte->getEleve();

        return $this->render('admin/porteOuverte/info.html.twig', [
            "porteOuverte" => $porteOuverte,
            "eleves" => $eleves,
        ]);
    }

    /**
     * @Route("/admin/voirElevePo/{id}", name="admin.voirElevePo")
     * @return Response
     */
    public function voirElevePo(Eleve $eleve): Response
    {
        return $this->render('admin/porteOuverte/infoEleve.html.twig', [
            "eleve" => $eleve,
        ]);
    }

    /**
     * @Route("/admin/elevePresentPo/{id}", name="admin.elevePresentPo")
     * @return Response
     */
    public function elevePresentPo(Eleve $eleve): Response
    {
        if($eleve->getPresentJourneeInfo() == "1") {
            $eleve->setPresentJourneeInfo(null);
        } else {
            $eleve->setPresentJourneeInfo("1");
        }
        $this->em->persist($eleve);
        $this->em->flush();

        $porteOuverte = $eleve->getPorteOuverte();
        $elevesPo = $porteOuverte->getEleve();
        return $this->render('admin/porteOuverte/info.html.twig', [
            "porteOuverte" => $porteOuverte,
            "eleves" => $elevesPo,
        ]);
    }

    /**
     * @Route("/admin/modifierPorteOuverte.html.twig/{id}", name="admin.modifierPorteOuverte.html.twig")
     * @param Request $request
     * @param PorteOuverte $porteOuverte
     * @return Response
     */
    public function modifierPorteOuverte(Request $request, PorteOuverte $porteOuverte): Response
    {
        $form = $this->createForm(PorteOuverteType::class, $porteOuverte, ([
            'po' => $porteOuverte
        ]));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($porteOuverte);
            $this->em->flush();

            return $this->redirectToRoute('admin.porteOuverte');
        }
        return $this->render('admin/porteOuverte/modifierPorteOuverte.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/supprimerPorteOuverte/{id}", name="admin.supprimerPorteOuverte", methods={"DELETE"})
     */
    public function supprimerPorteOuverte(Request $request, PorteOuverte $porteOuverte, MailerInterface $mailer): Response
    {
        if ($this->isCsrfTokenValid('delete' . $porteOuverte->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $eleves = $porteOuverte->getEleve();
            foreach ($eleves as $eleve) {
                $eleve->setPorteOuverte(null);
                $eleve->setPorteOuverteAnnule(1);
                $entityManager->persist($eleve);
                $email = (new TemplatedEmail())
                    ->from('cergaribaldi@monpermis.fr')
                    ->to($eleve->getMail())
                    ->subject('CER GARIBALDI : Journée d\'information annulée')
                    ->htmlTemplate("mail/confirmationPo.html.twig")
                    ->context([
                        'eleve' => $eleve,
                        'po' => $porteOuverte
                    ]);
                if($eleve->getMailParent() != null) {
                    $email->cc($eleve->getMailParent());
                }
                $mailer->send($email);
            }
            $entityManager->remove($porteOuverte);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin.porteOuverte');
    }



    /**
     * @Route("/admin/newMoniteur", name="admin.newMoniteur")
     */
    public function newMoniteur(Request $req): Response
    {
        $moniteur = new Moniteur();
        $form = $this->createForm(MoniteurType::class, $moniteur);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($moniteur);
            $entityManager->flush();

            return $this->redirectToRoute('admin.index');
        }

        return $this->render('admin/moniteur/newMoniteur.html.twig', [
            'moniteur' => $moniteur,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/admin/{id}/deleteMoniteur", name="admin.deleteMoniteur", methods={"DELETE"})
     */
    public function deleteMoniteur(Request $request, Moniteur $moniteur)
    {
        if ($this->isCsrfTokenValid('delete' . $moniteur->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($moniteur);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin.index');
    }

    /**
     * @Route("/admin/{id}/editMoniteur", name="admin.editMoniteur", methods={"GET","POST"})
     */
    public function editMoniteur(Request $request, Moniteur $moniteur): Response
    {

        $form = $this->createForm(moniteurType::class, $moniteur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin.index');
        }

        return $this->render('admin/moniteur/editMoniteur.html.twig', [
            'moniteur' => $moniteur,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/admin/{id}/indispo", name="admin.indispo", methods={"GET","POST"})
     */
    public function indispo(Moniteur $moniteur, IndisponibiliteRepository $indispoRep): Response
    {
        $indispos = $indispoRep->Indispo($moniteur); // a remplacer par find by

        return $this->render('admin/indispo/indispo.html.twig', [
            'moniteur' => $moniteur,
            'indispos' => $indispos
        ]);
    }

    /**
     * @Route("/admin/{id}/deleteIndispo", name="indispo.delete", methods={"DELETE"})
     */
    public function indispoDelete(Request $request, Indisponibilite $indispo)
    {
        $moniteur = $indispo->getMoniteur();

        if ($this->isCsrfTokenValid('delete' . $indispo->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($indispo);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin.indispo', ['id' => $moniteur->getId()]);
    }

    /**
     * @Route("/admin/{id}/editIndispo", name="indispo.edit", methods={"GET","POST"})
     */
    public function editIndispo(Request $request, Indisponibilite $indispo): Response
    {
        $moniteur = $indispo->getMoniteur();

        $form = $this->createForm(IndisponibiliteType::class, $indispo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin.indispo', ['id' => $moniteur->getId()]);
        }

        return $this->render('admin/indispo/editIndispo.html.twig', [
            'indispo' => $indispo,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/{id}/pageNewIndispo", name="indispo.pageNewIndispo")
     */
    public function pageNewIndispo(Moniteur $moniteur): Response
    {

        return $this->render('admin/indispo/newIndispo.html.twig', [
            'moniteur' => $moniteur,
        ]);
    }

    /**
     * @Route("/admin/{id}/newIndispo", name="indispo.newIndispo")
     */
    public function newIndispo(Request $req, Moniteur $moniteur): Response
    {
        $donnees = json_decode($req->getContent());

        foreach ($donnees as $donnee) {
            $indispo = new Indisponibilite();

            if (
                isset($donnee->start) && !empty($donnee->start) &&
                isset($donnee->end) && !empty($donnee->end)

            ) {
                $indispo->setStart(new DateTime($donnee->start));
                $indispo->setEnd(new DateTime($donnee->end));
                $indispo->setMoniteur($moniteur);


                $em = $this->getDoctrine()->getManager();
                $em->persist($indispo);
                $em->flush();
            }
        }
        return $this->redirectToRoute('admin.indispo', ['id' => $moniteur->getId()]);
    }

    /**
     * @Route("/admin/{id}/lieu", name="admin.lieu", methods={"GET","POST"})
     */
    public function lieu(Moniteur $moniteur, LieuRepository $lieuRep): Response
    {
        $lieux = $lieuRep->Lieux($moniteur); //remplacer par findBy

        return $this->render('admin/lieu/lieu.html.twig', [
            'moniteur' => $moniteur,
            'lieux' => $lieux
        ]);
    }
    /**
     * @Route("/admin/{id}/deleteLieu", name="lieu.delete", methods={"DELETE"})
     */
    public function lieuDelete(Request $request, Lieu $lieu)
    {
        $moniteur = $lieu->getMoniteur();

        if ($this->isCsrfTokenValid('delete' . $lieu->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($lieu);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin.lieu', ['id' => $moniteur->getId()]);
    }

    /**
     * @Route("/admin/{id}/editLieu", name="lieu.edit", methods={"GET","POST"})
     */
    public function editLieu(Request $request, Lieu $lieu, MoniteurRepository $moniteurRep): Response
    {
        $moniteur = $lieu->getMoniteur();

        $form = $this->createForm(LieuType::class, $lieu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin.lieu', ['id' => $moniteur->getId()]);
        }

        return $this->render('admin/lieu/editLieu.html.twig', [
            'lieu' => $lieu,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/{id}/pageNewLieu", name="lieu.pageNewLieu")
     */
    public function pageNewLieu(Moniteur $moniteur): Response
    {

        return $this->render('admin/lieu/newLieu.html.twig', [
            'moniteur' => $moniteur,
        ]);
    }

    /**
     * @Route("/admin/{id}/newLieu", name="lieu.newLieu")
     */
    public function newLieu(Request $req, Moniteur $moniteur): Response
    {
        $donnees = json_decode($req->getContent());

        foreach ($donnees as $donnee) {
            $lieu = new Lieu();

            if (
                isset($donnee->lieu) && !empty($donnee->lieu)
            ) {
                $lieu->setLibelle($donnee->lieu);
                $lieu->setMoniteur($moniteur);

                $em = $this->getDoctrine()->getManager();
                $em->persist($lieu);
                $em->flush();
            }
        }
        return $this->redirectToRoute('admin.lieu', ['id' => $moniteur->getId()]);
    }


    /**
     * @Route("/admin/checkMdp", name="admin.checkMdp", methods={"POST"})
     */
    public function chekMdp(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $admin = $this->security->getUser();

        $submittedToken = $request->request->get('token');
        $mdpPrincipal =  $request->request->get('mdpPrincipal');
        $mdpSecondaire =  $request->request->get('mdpSecondaire');
        $idGerant =  $request->request->get('idGerant');
        if ($this->isCsrfTokenValid('checkMdp', $submittedToken)) {
            if (!($passwordEncoder->isPasswordValid($admin, $mdpPrincipal)) || hash('sha256', $mdpSecondaire) != $admin->getMdpSecondaire()) {
                $this->addFlash(
                    'danger',
                    'Un des mots de passe est incorrect.'
                );
                return $this->redirectToRoute('admin.index');
            }

            // retourner chez le gerant cliqué
            return $this->redirectToRoute('gerant.edit', ['id' => $idGerant]);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();
        }

        return $this->redirectToRoute('admin.porteOuverte');
    }
}
