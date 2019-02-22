<?php

namespace App\Controller;


use App\Entity\Answer;
use App\Entity\Hall;
use App\Entity\Player;
use App\Entity\Question;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function randomPlayerAction(EntityManagerInterface $em)
    {
        $players = $em->getRepository(Player::class)->findforRandom();

        if (count($players) > 0) {
            /** @var Player $player */
            $player = $players[rand(0, count($players) - 1)];
            $player->setLastRandom(new \DateTime());
            $em->flush();
            $this->addFlash("success", "$player");
        }
        else {
            $this->addFlash("warning", "Aucun joueur disponible");
        }

        /*
        dump($players, $player);
        die();
        */

        return $this->redirectToRoute('easyadmin', array(
            'action' => 'list',
            'entity' => 'Player',
        ));
    }


    /**
     * @Route("/admin/test")
     * @param EntityManagerInterface $em
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function testHalls(EntityManagerInterface $em)
    {
        $user = $this->getUser();

        $player = $em->getRepository(Player::class)->find(22);
        dump($user,
            $player,
            $user == $player,
            $user === $player
        );
        die();
        return new Response("<html><body></body></html>");
    }

}