<?php

namespace App\Controller\Admin;

use App\Entity\Answer;
use App\Entity\Hall;
use App\Entity\Player;
use App\Entity\Question;
use App\Entity\Sms;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // redirect to some CRUD controller
        $routeBuilder = $this->get(AdminUrlGenerator::class);
        return $this->redirect($routeBuilder->setController(PlayerCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('SmartQuiz');
    }

    public function configureCrud(): Crud
    {
        return Crud::new()
            ->setDateFormat('dd/MM/yyyy')
            ->setDateTimeFormat('dd/MM HH:mm')
            ->setTimeFormat('HH:mm');
    }

    public function configureMenuItems(): iterable
    {

        yield MenuItem::section('Le jeu', 'fas fa-folder-open');
        yield MenuItem::linkToCrud('Joueurs', 'fas fa-user', Player::class);
        yield MenuItem::linkToCrud('Réponses', 'fas fa-list-ul', Answer::class);

        yield MenuItem::section('fas fa-folder-open');
        yield MenuItem::linkToCrud('SMS', 'fas fa-list-ul', Sms::class);
        yield MenuItem::linkToRoute('Stats', 'fas fa-signal', 'admin_stats');

        yield MenuItem::section('Paramétrage', 'fas fa-folder-open');
        yield MenuItem::linkToCrud('Halls', 'fas fa-building', Hall::class);
        yield MenuItem::linkToCrud('Questions', 'fas fa-question', Question::class);

        yield MenuItem::section('Site', 'fas fa-folder-open');
        yield MenuItem::linkToRoute('Fermer', 'fas fa-lock', 'admin_onoff', ['closed' => 1]);
        yield MenuItem::linkToRoute('Ouvrir', 'fas fa-unlock', 'admin_onoff', ['closed' => 0]);
    }
}
