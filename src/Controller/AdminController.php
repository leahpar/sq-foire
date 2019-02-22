<?php

namespace App\Controller;

use App\Entity\Player;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;

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
     * @Route("/init", name="init")
     * @param EntityManagerInterface $em
     */
    public function initAction(EntityManagerInterface $em)
    {
        $connection = $em->getConnection();


        $ret = $connection->fetchAll("SELECT * FROM sqlite_sequence WHERE name = 'player'");
        $seq = $ret[0]["seq"];

        if ($seq < 1000) {
            for ($i = 0; $i < 1324; $i++) {
                $p = new Player();
                $p  ->setMail("toto")
                    ->setTel("1234")
                    ->setName("toto");
                $em->persist($p);
            }
            $em->flush();

            $players = $em->getRepository(Player::class)->findByName("toto");
            foreach ($players as $p) {
                $em->remove($p);
            }
            $em->flush();

        }

        $ret = $connection->fetchAll("SELECT * FROM sqlite_sequence WHERE name = 'player'");
        $seq = $ret[0]["seq"];
        dump($seq); die();
    }
}
