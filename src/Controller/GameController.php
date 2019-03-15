<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Hall;
use App\Entity\Player;
use App\Entity\Pub;
use App\Entity\Question;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class GameController extends AbstractController
{

    /**
     * @Route("/", name="game_index")
     */
    public function indexAction()
    {
        $user = $this->getUser();
        if (null !== $user && in_array("ROLE_USER", $user->getRoles())) {
            return $this->redirectToRoute("game_start");
        }
        else {
            return $this->redirectToRoute("game_halls");
        }
    }

    /**
     * @Route("/jeu", name="game_start")
     */
    public function gameAction()
    {
        $user = $this->getUser();
        if (null !== $user && in_array("ROLE_USER", $user->getRoles())) {
            return $this->redirectToRoute("game_halls");
        }

        return $this->render('game/start.html.twig');
    }

    /**
     * @Route("/jeu/fin", name="game_end")
     */
    public function endAction()
    {
        $user = $this->getUser();
        if (null == $user || !in_array("ROLE_USER", $user->getRoles())) {
            return $this->redirectToRoute("game_start");
        }

        return $this->render('game/end.html.twig');
    }

    /**
     * @Route("/jeu/halls", name="game_halls")
     * @param EntityManagerInterface $em
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function hallsAction(EntityManagerInterface $em)
    {
        /** @var Player $user */
        $user = $this->getUser();
        if (null == $user || !in_array("ROLE_USER", $user->getRoles())) {
            return $this->redirectToRoute("game_start");
        }

        $halls = $em->getRepository(Hall::class)->findBy([], ['name' => 'ASC']);

        return $this->render('game/halls.html.twig', [
            'halls' => $halls,
            'player' => $user
        ]);
    }

    /**
     * @Route("/jeu/halls/{name}", name="game_hall")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param Hall $hall
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function hallAction(Request $request, EntityManagerInterface $em, Hall $hall)
    {
        /** @var Player $user */
        $user = $this->getUser();
        if (null == $user || !in_array("ROLE_USER", $user->getRoles())) {
            return $this->redirectToRoute("game_start");
        }

        /** @var Question $question */
        $question = $hall->getQuestion();

        if ($request->isMethod("POST")) {

            $answer = $em->getRepository(Answer::class)->findOneBy([
                'player' => $user,
                'question' => $question
            ]);
            if (null == $answer) {
                $answer = new Answer($user, $question);
                $answer->setAnswer($request->request->get('answer'));
                $em->persist($answer);
                $user->addAnswer($answer);
                $em->flush();
            }

            $halls = $em->getRepository(Hall::class)->findBy([], ['name' => 'ASC']);

            $hallTodo = [];
            foreach ($halls as $h) {
                if (!$user->isHallDone($h->getId())) {
                    $hallTodo[] = $h;
                }
            }

            $exclu = $hall->getExclu();
            $pubs = $hall->getPubs()->toArray();
            foreach ($pubs as $k => $pub) {
                if ($pub->isExclu()) unset($pubs[$k]);
            }
            shuffle($pubs);

            $partenaires = [];
            if ($exclu || isset($pubs[0])) $partenaires[] = $exclu ?? $pubs[0];
            if (isset($pubs[1]))    $partenaires[] = $pubs[1];

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
            'question' => $question
        ]);
    }


    /**
     * @Route("/reglement", name="game_rules")
     */
    public function rulesAction()
    {
        return $this->render('game/rules.html.twig');
    }
    /**
     * @Route("/comment-jouer", name="game_howto")
     */
    public function howtoAction()
    {
        return $this->render('game/howto.html.twig');
    }

    /**
     * @Route("/share", name="game_share")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function shareAction(Request $request, EntityManagerInterface $em)
    {
        /** @var Player $user */
        $user = $this->getUser();
        if ($user) {
            $user->setFbshare(true);
            $em->flush();
        }
        $url = $request->getHost();
        return $this->redirect("https://www.facebook.com/sharer/sharer.php?u=$url");
    }


    /**
     * @Route("/inscription", name="game_signup")
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function signupAction(EntityManagerInterface $em, Request $request) {

        $user = $this->getUser();
        if (null !== $user && in_array("ROLE_USER", $user->getRoles())) {
            return $this->redirectToRoute("game_halls");
        }

        $errors = [];

        if ($request->isMethod("POST")) {

            $email = $request->request->get('email');
            $name = $request->request->get('name', []);
            $name = trim(implode(' ', $name));
            $data = $request->request->get('data', []);

            // TODO : check values ?
            $p = $em->getRepository(Player::class)->findOneByEmail($email);
            if (empty($email) || null != $p) {
                $errors['email'] = true;
            }

            $player = new Player();
            $player->setName($name);
            $player->setEmail($email);
            $player->setData($data);

            if (count($errors) == 0) {

                $token = substr(strtoupper(uniqid()), -6);
                $player->setToken($token);

                $em->persist($player);
                $em->flush();

                // Manually authenticate user
                $token = new UsernamePasswordToken($player, null, 'main', ["ROLE_USER"]);
                $this->get('security.token_storage')->setToken($token);
                $this->get('session')->set('_security_main', serialize($token));

                return $this->redirectToRoute("game_halls");
            }
        }
        return $this->render('game/signup.html.twig', [
            'remote' => false,
            'errors' => $errors,
            'form' => $request->request->all()
        ]);
    }


    /**
     * @Route("/code", name="game_token_signup")
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function tokenLoginAction(EntityManagerInterface $em, Request $request)
    {
        $user = $this->getUser();
        if (null !== $user && in_array("ROLE_USER", $user->getRoles())) {
            return $this->redirectToRoute("game_halls");
        }

        $token = $request->query->get('t', null);

        if ($token) {
            $player = $em->getRepository(Player::class)->findOneByToken($token);
            if ($player) {
                // Manually authenticate user
                $token = new UsernamePasswordToken($player, null, 'main', ["ROLE_USER"]);
                $this->get('security.token_storage')->setToken($token);
                $this->get('session')->set('_security_main', serialize($token));

                return $this->redirectToRoute("game_halls");
            }
        }

        return $this->render('game/code.html.twig');
    }


    /**
     * @Route("/inscription-accueil", name="game_remote_signup")
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function remoteSignupAction(EntityManagerInterface $em, Request $request)
    {
        $errors = [];
        if ($request->isMethod("POST")) {

            $email = $request->request->get('email');
            $name = $request->request->get('name', []);
            $name = trim(implode(' ', $name));
            $data = $request->request->get('data', []);

            // TODO : check values ?
            $p = $em->getRepository(Player::class)->findOneByEmail($email);
            if (empty($email) || null != $p) {
                $errors['email'] = true;
            }

            $player = new Player();
            $player->setName($name);
            $player->setEmail($email);
            $player->setData($data);

            if (count($errors) == 0) {
                $token = substr(strtoupper(uniqid()), -6);
                $player->setToken($token);

                $em->persist($player);
                $em->flush();

                return $this->render('game/qrcode.html.twig', [
                    'player' => $player,
                    'token' => $token
                ]);
            }
        }
        return $this->render('game/signup.html.twig', [
            'remote' => true,
            'errors' => $errors,
            'form' => $request->request->all()
        ]);
    }
}
