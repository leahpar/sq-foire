<?php

namespace App\Controller;

use App\Entity\Player;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('game/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/admin/qrcodes', name: 'app_login_qrcodes')]
    public function qrcodes(Request $request): Response
    {
        $cpt = $request->query->get('cpt', 10);
        $CONST = 3141;
        // Génère des codes à 6 chars hexa
        $tokens = [];
        for ($i=1048576; $i<16777215; $i++) {
            if ($i%$CONST == 0) $tokens[] = dechex($i);
        }
        shuffle($tokens);

        return $this->render('admin/qrcodes.html.twig', [
            'tokens' => array_splice($tokens, 0, $cpt)
        ]);
    }

    #[Route('/code', name: 'app_login_code')]
    public function login_code(Request $request, EntityManagerInterface $em, Security $security)
    {
        $token = strtolower(trim($request->query->get('t')));

        if ($token) {
            // Check token validity
            $CONST = 3141;
            $decoded = hexdec($token);
            if ($decoded < 1048576 || $decoded > 16777215 || $decoded % $CONST != 0) {
                sleep(3);
                $this->addFlash('error', 'Code invalide');
                return $this->redirectToRoute('app_login_code');
            }
        }

        if ($token) {
            /** @var ?Player $user */
            $user = $em->getRepository(Player::class)->findOneBy(['token' => $token]);

            if (!$user) {
                $user = Player::createAnonymeFromToken($token);
                $em->persist($user);
                $em->flush();
            }

            $security->login($user, 'form_login');

            return $this->redirectToRoute('game_halls');
        }

        return $this->render('game/code.html.twig');
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }

    #[Route('/inscription', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        Security $security,
        #[Autowire(env: 'APP_INSCRIPTION')]
        bool $inscription,
    ): Response
    {
        if (!$inscription) {
            return $this->redirectToRoute('game_index');
        }

        if ($this->isGranted("ROLE_USER")) {
            return $this->redirectToRoute("game_halls");
        }

        $user = new Player();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

//            // Send email login url
//            $url = $this->generateUrl('game_index', [], UrlGeneratorInterface::ABSOLUTE_URL);
//
//            $message = <<<EOF
//Bienvenue au grand jeu Vikings de la foire de Rouen
//
//Cliquez ici pour vous connecter à votre compte et commencer à jouer :
//$url
//
//Bonne soirée !
//EOF;
//        $this->mail->send($player->getEmail(), $message);

            $security->login($user, 'form_login');

            return $this->redirectToRoute('game_halls');
        }

        return $this->render('game/signup.html.twig', [
            'form' => $form,
        ]);
    }

}
