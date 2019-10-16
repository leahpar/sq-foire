<?php

namespace App\Controller;

use App\Entity\Player;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends EasyAdminController
{
    /**
     * @Route("/admin/dashboard", name="admin_dashboard")
     */
    public function dashboardAction()
    {
        return $this->render('admin/dashboard.html.twig', [
        ]);
    }

    /**
     * @Route("/admin/export")
     * @param EntityManagerInterface $em
     */
    public function export(EntityManagerInterface $em)
    {
        $players = $em->getRepository(Player::class)->findAll();

        $output = implode(';', [
            "ID",
            "Nom",
            "Email",
            "Tel",
            "Ville",
            "Réponses",
            "Bonnes réponses",
            "Tirage au sort",
            "Partage Facebook"
        ]);
        $output .= "\n";

        foreach ($players as $player) {
            $output .= implode(';', [
                $player->getId(),
                $player->getName(),
                $player->getEmail(),
                $player->tel,
                $player->ville,
                $player->countAnswers(),
                $player->countGoodAnswers(),
                $player->getLastRandom() != null ? "oui" : "non",
                $player->getFbshare()    != null ? "oui" : "non",

            ]);
            $output .= "\n";
        }

        return new Response($output);
    }

}
