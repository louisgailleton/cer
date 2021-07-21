<?php


namespace App\Controller;


use App\Entity\Agence;
use App\Entity\Eleve;
use App\Entity\Forfait;
use App\Entity\Utilisateur;
use App\Form\EleveFormulaireType;
use App\Form\EleveType;
use App\Form\NumNephType;
use App\Repository\AgenceRepository;
use App\Repository\ContenuForfaitRepository;
use App\Repository\ForfaitRepository;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class HomeController extends AbstractController
{
    private $security;
    private $em;

    public function __construct(Security $security, EntityManagerInterface $em)
    {
        $this->security = $security;
        $this->em = $em;
    }

    /**
     * @Route("/", name="home")
     * @param AgenceRepository $ar
     * @return Response
     */
    public function index(AgenceRepository $ar): Response
    {
        $agences = $ar->findAll();
        $utilisateur = $this->security->getUser();
        if ($utilisateur) {
            if ($utilisateur->getRole() == "ROLE_ELEVE") {
                return $this->redirectToRoute("eleve.index");
            } else if ($utilisateur->getRole() == "ROLE_ADMIN") {
                return $this->redirectToRoute("admin.index");
            } else if ($utilisateur->getRole() == "ROLE_GERANT") {
                return $this->redirectToRoute("gerant.index");
            } else if ($utilisateur->getRole() == "ROLE_SECRETAIRE") {
                return $this->redirectToRoute("secretaire.index");
            } else if ($utilisateur->getRole() == "ROLE_MONITEUR") {
                return $this->redirectToRoute("moniteur.index");
            } else {
                return $this->render('pages/home.html.twig', [
                    'agences' => $agences,
                ]);
            }
        } else {
            return $this->render('pages/home.html.twig', [
                'agences' => $agences,
            ]);
        }
    }

    /**
     * @Route("/inscription/agence/{id}", name="inscription.choixAgence")
     * @param Agence $agence
     * @param SessionInterface $session
     * @return RedirectResponse
     */
    public function choixAgence(Agence $agence, SessionInterface $session)
    {
        $session->set('agence', $agence->getId());

        return $this->redirectToRoute("inscription.creationCompte");
    }

    /**
     * @Route("/inscription", name="inscription.creationCompte")
     * @param Request $request
     * @param AuthenticationUtils $authenticationUtils
     * @param RoleRepository $rp
     * @param SessionInterface $session
     * @param AgenceRepository $ar
     * @param ForfaitRepository $fr
     * @return Response
     * @throws \Exception
     */
    public function inscription(Request $request, UserPasswordEncoderInterface $passwordEncoder, AuthenticationUtils $authenticationUtils, RoleRepository $rp, SessionInterface $session, AgenceRepository $ar, ForfaitRepository $fr)
    {
        $eleve = new Eleve();

        $form = $this->createForm(EleveType::class, $eleve);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $role = $rp->findOneById(1);
            $eleve->setRole($role);

            $mdp = $form->get('mdp')->getData();

            $idAgence = $session->get('agence');
            $agence = $ar->findOneBy(['id' => $idAgence]);
            $eleve->setAgence($agence);
            $eleve->setEtatDossier("1");
            $eleve->setMdp(
                $passwordEncoder->encodePassword(
                    $eleve,
                    $form->get('mdp')->getData()
                )
            );
            $this->em->persist($eleve);
            $this->em->flush();
            $session->clear();

            $token = new UsernamePasswordToken($eleve, $mdp, 'main', $eleve->getRoles());
            $this->get('security.token_storage')->setToken($token);
            $this->get('session')->set('_security_main', serialize($token));
            $this->addFlash('success', 'Votre compte a été créé');
            return $this->redirectToRoute('eleve.index');
        }

        return $this->render('inscription/creationCompte.html.twig', [
            'property' => $eleve,
            'form' => $form->createView()
        ]);
    }
}
