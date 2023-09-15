<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Hall;
use App\Entity\Player;
use App\Entity\Pub;
use App\Entity\Question;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/jeu')]
#[IsGranted('ROLE_USER')]
class GameController extends AbstractController
{
    #[Route(path: '/fin', name: 'game_end')]
    public function end()
    {
        return $this->render('game/end.html.twig');
    }

    #[Route(path: '/halls', name: 'game_halls')]
    public function halls(EntityManagerInterface $em)
    {
        /** @var Player $player */
        $player = $this->getUser();
        if ($player->isAnonyme()) {
            return $this->redirectToRoute("game_player_infos");
        }

        $halls = $em->getRepository(Hall::class)->findBy([], ['tri' => 'ASC']);

        return $this->render('game/halls.html.twig', [
            'halls' => $halls,
            'player' => $this->getUser()
        ]);
    }

    #[Route(path: '/halls/{name}', name: 'game_hall')]
    public function hall(Request $request, EntityManagerInterface $em, Hall $hall)
    {
        /** @var Player $user */
        $user = $this->getUser();

        /** @var Question $question */
        $question = $hall->getQuestion();

        $halls = $em->getRepository(Hall::class)->findBy([], ['name' => 'ASC']);

        $exclu = $hall->getExclu();
        $pubs = $hall->getPubs()->toArray();
        foreach ($pubs as $k => $pub) {
            if ($pub->isExclu()) unset($pubs[$k]);
        }
        shuffle($pubs);

        $partenaires = [];
        if ($exclu || isset($pubs[0])) $partenaires[] = $exclu ?? $pubs[0];
        if (isset($pubs[1])) $partenaires[] = $pubs[1];

        if ($request->isMethod("POST")) {

            $answer = $em->getRepository(Answer::class)->findOneBy([
                'player' => $user,
                'question' => $question
            ]);

            // Nouvelle réponse si pas existante
            if (null == $answer) {
                $answer = new Answer($user, $question);
            }

            // MAJ réponse
            $answer->setAnswer($request->request->get('answer'));
            $em->persist($answer);
            $user->addAnswer($answer);
            $user->updateLastConnection();
            $em->flush();


            // Liste des halls restants
            $hallTodo = [];
            foreach ($halls as $h) {
                if (!$user->isHallDone($h->getId())) {
                    $hallTodo[] = $h;
                }
            }

            return $this->render('game/answer.html.twig', [
                'halls' => $halls,
                'hallTodo' => $hallTodo,
                'player' => $user,
                'question' => $question,
                'hall' => $hall,
                'pubs' => $partenaires,
                'answer' => $answer
            ]);
        }

        return $this->render('game/question.html.twig', [
            'hall' => $hall,
            'player' => $user,
            'pubs' => $partenaires,
            'question' => $question
        ]);
    }


//    #[Route(path: '/code', name: 'game_token_signup')]
//    public function tokenLogin(EntityManagerInterface $em, Request $request)
//    {
//        $user = $this->getUser();
//        if (null !== $user && in_array("ROLE_USER", $user->getRoles())) {
//            return $this->redirectToRoute("game_halls");
//        }
//
//        $token = $request->query->get('t', null);
//
//        if ($token) {
//            $player = $em->getRepository(Player::class)->findOneByToken($token);
//            if ($player) {
//                // Manually authenticate user
//                $token = new UsernamePasswordToken($player, null, 'main', ["ROLE_USER"]);
//                $this->get('security.token_storage')->setToken($token);
//                $this->get('session')->set('_security_main', serialize($token));
//
//                return $this->redirectToRoute("game_halls");
//            }
//        }
//
//        return $this->render('game/code.html.twig');
//    }

//    #[Route(path: '/inscription-accueil', name: 'game_remote_signup')]
//    public function remoteSignup(Request $request) {
//        if ($request->isMethod("POST")) {
//
//            $player = $this->signupPlayer($request);
//
//            if ($player) {
//                return $this->render('game/qrcode.html.twig', [
//                    'player' => $player,
//                    'token' => $player->getToken()
//                ]);
//            }
//        }
//        return $this->render('game/signup.html.twig', [
//            'remote' => true,
//            'errors' => $this->errors,
//            'form' => $request->request->all()
//        ]);
//    }

//    private function signupPlayer(Request $request): ?Player
//    {
//        $post = $request->request->all();
//        //$email = $request->request->get('email');
//        //$name = $request->request->get('name', []);
//        //$name = trim(implode(' ', $name));
//        //$data = $request->request->get('data', []);
//        $email = $post['email'] ?? '';
//        $name = $post['name'] ?? [];
//        $name = trim(implode(' ', $name));
//        $data = $post['data'] ?? [];
//
//        $player = null;
//        $this->errors = [];
//
//        // Email unique (obligatoire, c'est le login)
//        $p = $this->em->getRepository(Player::class)->findOneByEmail($email);
//        if (empty($email) || null != $p) {
//            $this->errors['email'] = true;
//        }
//
//        // Tel unique
//        if (isset($data['tel'])) {
//            $p = $this->em->getRepository(Player::class)->findLikeDataTel($data['tel'] ?? '');
//            if (count($p) > 0) {
//                $this->errors['tel'] = true;
//            }
//        }
//
//        if (count($this->errors) > 0) {
//            return null;
//        }
//
//        $player = new Player();
//        $player->setName($name);
//        $player->setEmail($email);
//        $player->setData($data);
//
//        $token = substr(strtoupper(uniqid()), -6);
//        $player->setToken($token);
//
//        $this->em->persist($player);
//        $this->em->flush();
//
//        // Send email login url
//        $url = $this->router->generate('game_token_signup', [], UrlGeneratorInterface::ABSOLUTE_URL);
//        $url .= "?t=" . $token;
//
//        $message = <<<EOF
//Bienvenue au grand jeu Vikings de la foire de Rouen
//
//Cliquez ici pour vous connecter à votre compte et commencer à jouer :
//$url
//
//Bonne soirée !
//EOF;
//        $this->mail->send($player->getEmail(), $message);
//
//        return $player;
//    }

}
