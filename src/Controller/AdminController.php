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

}
