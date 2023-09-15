<?php

namespace App\Controller;

use App\Entity\Player;
use App\Form\PlayerType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PlayerController extends AbstractController
{

    #[Route(path: '/infos', name: 'game_player_infos')]
    public function infos(Request $request, EntityManagerInterface $em)
    {
        /** @var Player $player */
        $player = $this->getUser();

        $form = $this->createForm(PlayerType::class, $player);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($player);
            $em->flush();
            return $this->redirectToRoute('game_halls');
        }

        return $this->render('game/player_infos.html.twig', [
            'form' => $form,
        ]);
    }

}
