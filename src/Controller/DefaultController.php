<?php

namespace App\Controller;


use App\Entity\Answer;
use App\Entity\Hall;
use App\Entity\Player;
use App\Entity\Question;
use App\Service\SMSService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DefaultController extends AbstractController
{
    /**
     * @Route("/add-players")
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function addPlayersAction(EntityManagerInterface $em)
    {
        $player = new Player();
        $player
            ->setName("Scarlett Johansson")
            ->setData([
                'tel' => "0987654321",
                'ville' => "Rouen",
                'mail' => "scarlett@johansson.com"
            ]);
        $em->persist($player);

        $player = new Player();
        $player
            ->setName("Jean Dujardin")
            ->setData([
                'tel' => "0987654321",
                'ville' => "Rouen",
                'mail' => "jean@dujardin.com"
            ]);
        $em->persist($player);

        $player = new Player();
        $player
            ->setName("Juste Leblanc")
            ->setData([
                'tel' => "0987654321",
                'ville' => "Rouen",
                'mail' => "juste.leblanc@gmail.com"
            ]);
        $em->persist($player);


        $em->flush();


        return new Response("OK");
    }
    /**
     * @Route("/add-answers")
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function addAnswersAction(EntityManagerInterface $em)
    {
        $players = $em->getRepository(Player::class)->findAll();
        $questions = $em->getRepository(Question::class)->findAll();

        /** @var PLayer $player */
        /** @var Question $question */
        foreach ($players as $player) {
            foreach ($questions as $question) {
                $i = rand(0, $question->getAnswersCount());
                $a = $question->getAnswer($i);
                if (null != $a) {
                    $answer = new Answer($player, $question);
                    $answer->setAnswer($a);
                    $em->persist($answer);
                }
            }
        }

        $em->flush();


        return new Response("OK");
    }

    /**
     * @Route("/admin/rand_player", name="rand_player")
     * @param EntityManagerInterface $em
     * @param UrlGeneratorInterface $router
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     */
    public function randomPlayerAction(EntityManagerInterface $em, UrlGeneratorInterface $router)
    {
        $players = $em->getRepository(Player::class)->findforRandom();

        if (count($players) > 0) {
            /** @var Player $player */
            $player = $players[rand(0, count($players) - 1)];
            $player->setLastRandom(new \DateTime());
            $em->flush();

            $msg = "$player";
            $msg .= " - ";
            $msg .= "<a href='".$router->generate('notif_player', ["id" => $player->getId()])."'>Envoyer SMS</a>";
            $this->addFlash("success", $msg);
        }
        else {
            $this->addFlash("warning", "Aucun joueur disponible");
        }

        return $this->redirectToRoute('easyadmin', array(
            'action' => 'list',
            'entity' => 'Player',
        ));
    }

    /**
     * @Route("/admin/reload", name="reload_players")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     * @throws \Exception
     */
    public function reloadPlayers(Request $request, EntityManagerInterface $em)
    {
        $time = $request->query->get("t", 0);
        $date = (new \DateTime())->setTimestamp($time);
        $count = $em->getRepository(Player::class)->checkForReload($date);

        return new Response(null, $count > 0 ? 200 : 304);
    }


    /**
     * @Route("/admin/notif/{id}", name="notif_player")
     * @param Player $player
     * @param SMSService $sms
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function sendNotif(Player $player, SMSService $sms)
    {
        $message = "Bravo ! Vous avez été tiré au sort, venez récupérer votre lot au stand animation GSA à l'entrée de la foire. Un soucis ? 0607840001";
        $target  = $player->getData()["tel"] ?? null;

        if (!$player->isRandom()) {
            $this->addFlash("warning", "Joueur $player non tiré au sort");
        }
        else if ($target)  {
            $sms->send($target, $message);
            $this->addFlash("success", "SMS envoyé");
        }

        return $this->redirectToRoute('easyadmin', array(
            'action' => 'list',
            'entity' => 'Player',
        ));
    }
}
