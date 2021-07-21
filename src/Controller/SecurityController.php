<?php

namespace App\Controller;


use App\Form\MdpPerduType;
use App\Repository\EleveRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    private $security;
    private $em;

    public function __construct(Security $security, EntityManagerInterface $em)
    {
        $this->security = $security;
        $this->em = $em;
    }

    /**
     * @Route("/connexion", name="login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        $lastUsername = $authenticationUtils->getLastUsername();
        $error = $authenticationUtils->getLastAuthenticationError();
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * @Route("/recuperationMDP", name="recupMdp", methods={"GET","POST"})
     * @param Request $request
     * @param UtilisateurRepository $ur
     * @param MailerInterface $mailer
     * @return Response
     * @throws TransportExceptionInterface
     */
    public function recupMdp(Request $request, UtilisateurRepository $ur, MailerInterface $mailer, UserPasswordEncoderInterface $passwordEncoder): Response
    {

        $form = $this->createFormBuilder()
            ->add('mail', EmailType::class, [
                'label' => 'Entrez votre adresse mail'
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $mail = $form->get('mail')->getData();
            $utilisateur = $ur->findOneBy(['mail' => $mail]);
            if (!empty($utilisateur)) {
                $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $longueurMax = strlen($caracteres);
                $chaineAleatoire = '';
                for ($i = 0; $i < 15; $i++) {
                    $chaineAleatoire .= $caracteres[rand(0, $longueurMax - 1)];
                }
                $utilisateur->setMdp($passwordEncoder->encodePassword(
                    $utilisateur,
                    $chaineAleatoire
                ));

                $this->em->persist($utilisateur);
                $this->em->flush();
                $email = (new Email())
                    ->from('cergaribaldi@monpermis.fr')
                    ->to($mail)
                    ->subject('CER GARIBALDI : NOUVEAU MOT DE PASSE')
                    ->html("<p>Bonjour, votre nouveau mot de passe est : $chaineAleatoire</p>");
                $mailer->send($email);

                $message = "Un nouveau mot de passe vous à été envoyé par mail.";
                return $this->render('security/recupMdp.html.twig', [
                    'form' => $form->createView(),
                    'message' => $message
                ]);
            } else {
                $message = "Adresse mail incorrect";
                return $this->render('security/recupMdp.html.twig', [
                    'form' => $form->createView(),
                    'message' => $message
                ]);
            }
        }

        return $this->render('security/recupMdp.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
