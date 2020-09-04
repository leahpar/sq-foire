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
     * @Route("/admin/stats", name="admin_stats")
     */
    public function StatsAction(EntityManagerInterface $em)
    {
        $players = $em->getRepository(Player::class)->findAll();

        $share = 0;
        $emil = 0;
        $sms = 0;
        $actifHeure = 0;
        $actifJour = 0;

        //date_default_timezone_set("Europe/Paris");
        $now = (new \DateTime)->modify("-1 hour");
        $today = (new \DateTime)->setTime(0, 0);
        dump($now, $today);

        /** @var Player $player */
        foreach ($players as $player) {
            if ($player->getFbshare()) $share++;
            if ($player->getData()["authMailPub"]??"off" == "on") $emil++;
            if ($player->getData()["authSmsPub"]??"off" == "on") $sms++;
            if ($player->getLastConnection() >= $now) $actifHeure++;
            if ($player->getLastConnection() >= $today) $actifJour++;
        }


        return $this->render('admin/stats.html.twig', [
            "share" => $share,
            "sms" => $sms,
            "email" => $emil,
            "total" => count($players),
            "actifHeure" => $actifHeure,
            "actifJour" => $actifJour
        ]);
    }

    /**
     * @Route("/admin/export")
     * @param EntityManagerInterface $em
     */
    public function export(EntityManagerInterface $em)
    {
        // TODO: filtre RGPD
        $players = []; // $em->getRepository(Player::class)->findAll();

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
